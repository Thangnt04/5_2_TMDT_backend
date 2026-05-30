<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('recaptcha_site_key')) {
	function recaptcha_site_key()
	{
		$key = getenv('RECAPTCHA_SITE_KEY');
		if ($key === false || $key === '') {
			$key = getenv('PUBLIC_KEY');
		}
		$key = is_string($key) ? trim($key) : '';
		$invalid = array('YOUR_KEY', 'YOUR_SITE_KEY', '6LcKdPUqAAAAAGv-BwfXyqkrqpTuVEUCQLGwbG6Z');
		if ($key === '' || in_array($key, $invalid, true)) {
			return '';
		}
		return $key;
	}
}

if (!function_exists('recaptcha_secret_key')) {
	function recaptcha_secret_key()
	{
		$key = getenv('RECAPTCHA_SECRET_KEY');
		if ($key === false || $key === '') {
			$key = getenv('PRIVATE_KEY');
		}
		$key = is_string($key) ? trim($key) : '';
		$invalid = array('YOUR_KEY', 'YOUR_SECRET_KEY');
		if ($key === '' || in_array($key, $invalid, true)) {
			return '';
		}
		return $key;
	}
}

if (!function_exists('recaptcha_is_enabled')) {
	function recaptcha_is_enabled()
	{
		if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
			if (recaptcha_site_key() === '' || recaptcha_secret_key() === '') {
				return false;
			}
		}
		return recaptcha_site_key() !== '' && recaptcha_secret_key() !== '';
	}
}
