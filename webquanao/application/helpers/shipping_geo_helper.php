<?php
defined('BASEPATH') or exit('No direct script access allowed');

function shipping_openroute_key()
{
	$key = getenv('OPENROUTE_SERVICE');
	if ($key === false || $key === '') {
		return null;
	}
	$key = trim($key);
	$invalid = array('YOUR_KEY', 'YOUR_OPENROUTE_KEY');
	if (in_array(strtoupper($key), $invalid, true)) {
		return null;
	}
	return $key;
}

function shipping_remove_accents($str)
{
	$map = array(
		'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
		'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
		'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
		'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
		'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
		'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
		'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
		'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
		'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
		'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
		'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
		'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
		'đ' => 'd',
	);
	$str = mb_strtolower($str, 'UTF-8');
	return strtr($str, $map);
}

function shipping_match_words($inputStr, $targetStr)
{
	if (empty($targetStr)) {
		return false;
	}
	$inputWords = preg_split('/\s+/', strtolower(shipping_remove_accents($inputStr)));
	$targetWords = preg_split('/\s+/', strtolower(shipping_remove_accents($targetStr)));
	foreach ($inputWords as $word) {
		if ($word !== '' && !in_array($word, $targetWords, true)) {
			return false;
		}
	}
	return true;
}

function shipping_http_request($url, $method = 'GET', $body = null, $headers = array())
{
	if (!function_exists('curl_init')) {
		return null;
	}

	$ch = curl_init($url);
	$options = array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_TIMEOUT => 20,
		CURLOPT_HTTPHEADER => $headers,
	);

	if ($method === 'POST') {
		$options[CURLOPT_POST] = true;
		$options[CURLOPT_POSTFIELDS] = $body;
	}

	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($response === false || $httpCode >= 400) {
		return null;
	}

	return json_decode($response, true);
}

function shipping_geocode_openroute($city, $district, $ward, $apiKey)
{
	$location = $ward . ', ' . $district . ', ' . $city . ', Vietnam';
	$url = 'https://api.openrouteservice.org/geocode/search?api_key=' . urlencode($apiKey)
		. '&text=' . urlencode($location);

	$data = shipping_http_request($url);
	if (empty($data['features'])) {
		return null;
	}

	$cityHint = implode(' ', array_slice(preg_split('/\s+/', trim($city)), -2));
	foreach ($data['features'] as $feature) {
		$p = isset($feature['properties']) ? $feature['properties'] : array();
		$combined = trim(
			(isset($p['region']) ? $p['region'] : '') . ' '
			. (isset($p['county']) ? $p['county'] : '') . ' '
			. (isset($p['locality']) ? $p['locality'] : '') . ' '
			. (isset($p['label']) ? $p['label'] : '')
		);
		if (shipping_match_words($cityHint, $combined)) {
			return $feature['geometry']['coordinates'];
		}
	}

	$first = $data['features'][0];
	return isset($first['geometry']['coordinates']) ? $first['geometry']['coordinates'] : null;
}

function shipping_strip_admin_prefix($name)
{
	$name = trim($name);
	$prefixes = array(
		'Phường', 'P.', 'Xã', 'Thị trấn', 'Thị xã',
		'Quận', 'Q.', 'Huyện', 'H.',
		'Thành phố', 'TP.', 'Tỉnh',
	);
	foreach ($prefixes as $prefix) {
		if (stripos($name, $prefix . ' ') === 0) {
			return trim(substr($name, strlen($prefix)));
		}
	}
	return $name;
}

function shipping_nominatim_search($query)
{
	$url = 'https://nominatim.openstreetmap.org/search?'
		. http_build_query(array(
			'q' => $query,
			'format' => 'json',
			'limit' => 5,
			'countrycodes' => 'vn',
		));

	$headers = array('User-Agent: NgocLanShop/1.0 (shipping-fee; contact@localhost)');
	$data = shipping_http_request($url, 'GET', null, $headers);

	return (is_array($data) && !empty($data)) ? $data : null;
}

function shipping_pick_nominatim_result($data, $city)
{
	$cityHint = implode(' ', array_slice(preg_split('/\s+/', trim($city)), -2));
	foreach ($data as $item) {
		$label = isset($item['display_name']) ? $item['display_name'] : '';
		if (shipping_match_words($cityHint, $label)) {
			return array((float) $item['lon'], (float) $item['lat']);
		}
	}

	$first = $data[0];
	return array((float) $first['lon'], (float) $first['lat']);
}

function shipping_geocode_nominatim($city, $district, $ward)
{
	$simpleWard = shipping_strip_admin_prefix($ward);
	$simpleDistrict = shipping_strip_admin_prefix($district);
	$simpleCity = shipping_strip_admin_prefix($city);

	$queries = array(
		$ward . ', ' . $district . ', ' . $city . ', Vietnam',
		$simpleWard . ', ' . $simpleDistrict . ', ' . $simpleCity . ', Vietnam',
		$simpleWard . ', ' . $simpleDistrict . ', Vietnam',
		$simpleDistrict . ', ' . $simpleCity . ', Vietnam',
		$simpleCity . ', Vietnam',
	);

	foreach ($queries as $query) {
		$data = shipping_nominatim_search($query);
		if ($data) {
			return shipping_pick_nominatim_result($data, $city);
		}
	}

	return null;
}

function shipping_resolve_coordinates($city, $district, $ward)
{
	$apiKey = shipping_openroute_key();
	if ($apiKey) {
		$coords = shipping_geocode_openroute($city, $district, $ward, $apiKey);
		if ($coords) {
			return $coords;
		}
	}

	return shipping_geocode_nominatim($city, $district, $ward);
}

function shipping_haversine_meters($from, $to)
{
	$R = 6371000;
	list($lon1, $lat1) = $from;
	list($lon2, $lat2) = $to;

	$phi1 = deg2rad($lat1);
	$phi2 = deg2rad($lat2);
	$dPhi = deg2rad($lat2 - $lat1);
	$dLambda = deg2rad($lon2 - $lon1);

	$a = pow(sin($dPhi / 2), 2) + cos($phi1) * cos($phi2) * pow(sin($dLambda / 2), 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

	return $R * $c;
}

function shipping_distance_openroute($from, $to, $apiKey)
{
	$url = 'https://api.openrouteservice.org/v2/directions/driving-car';
	$body = json_encode(array('coordinates' => array($from, $to)));
	$headers = array(
		'Authorization: ' . $apiKey,
		'Content-Type: application/json',
	);

	$data = shipping_http_request($url, 'POST', $body, $headers);
	if (!empty($data['routes'][0]['summary']['distance'])) {
		return array(
			'distance' => (float) $data['routes'][0]['summary']['distance'],
			'duration' => isset($data['routes'][0]['summary']['duration'])
				? (float) $data['routes'][0]['summary']['duration']
				: null,
		);
	}

	return null;
}

function shipping_calculate_distance($from, $to)
{
	$apiKey = shipping_openroute_key();
	if ($apiKey) {
		$result = shipping_distance_openroute($from, $to, $apiKey);
		if ($result) {
			return $result;
		}
	}

	return array(
		'distance' => shipping_haversine_meters($from, $to),
		'duration' => null,
	);
}
