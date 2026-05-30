<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php'; // Đường dẫn tới autoload Composer

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;

class Home extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('transaction_model');
		$this->load->model('comment_model');
		$this->load->model('user_model');
		$this->load->model('product_model');
		$this->load->model('order_model');
		$this->load->model('cart_model');
	}
	public function index()
	{
		// Lấy mốc thời gian 7 ngày trước
		$date = new DateTime();
		$date->modify('-7 days');
		$seven_days_ago = $date->format('Y-m-d H:i:s');

		// 1. Đơn hàng mới (chưa xử lý) trong 7 ngày qua
		$input = array();
		$input['where'] = "status = '0' AND created >= '$seven_days_ago'";
		$total_order = $this->transaction_model->get_total($input);
		$this->data['total_order'] = $total_order;

		// Mốc thời gian 14 ngày trước để tính tăng trưởng
		$date14 = new DateTime();
		$date14->modify('-14 days');
		$fourteen_days_ago = $date14->format('Y-m-d H:i:s');

		// 2. Tổng số đơn hàng trong 7 ngày qua
		$input_all_orders = array();
		$input_all_orders['where'] = "created >= '$seven_days_ago'";
		$total_all_orders = $this->transaction_model->get_total($input_all_orders);
		$this->data['total_all_orders'] = $total_all_orders;
		
		// Đơn hàng 7 ngày trước đó
		$input_prev_orders = array();
		$input_prev_orders['where'] = "created >= '$fourteen_days_ago' AND created < '$seven_days_ago'";
		$prev_all_orders = $this->transaction_model->get_total($input_prev_orders);

		// Phần trăm tăng trưởng đơn hàng
		$order_percent = ($prev_all_orders > 0) ? round((($total_all_orders - $prev_all_orders) / $prev_all_orders) * 100) : (($total_all_orders > 0) ? 100 : 0);
		$this->data['order_percent'] = $order_percent;

		// 3. Tổng doanh thu trong 7 ngày qua
		$this->db->select_sum('amount', 'total_revenue');
		$this->db->where('status >', 0);
		$this->db->where('created >=', $seven_days_ago);
		$revenue_query = $this->db->get('transaction')->row();
		$this->data['revenue_7_days'] = $revenue_query ? $revenue_query->total_revenue : 0;

		// 4. Tổng số bình luận trong 7 ngày qua
		$input_comment = array();
		$input_comment['where'] = "created >= '$seven_days_ago'";
		$total_comments = $this->comment_model->get_total($input_comment);
		$this->data['total_comments'] = $total_comments;
		
		// Bình luận 7 ngày trước đó
		$input_prev_comment = array();
		$input_prev_comment['where'] = "created >= '$fourteen_days_ago' AND created < '$seven_days_ago'";
		$prev_comments = $this->comment_model->get_total($input_prev_comment);

		// Phần trăm tăng trưởng bình luận
		$comment_percent = ($prev_comments > 0) ? round((($total_comments - $prev_comments) / $prev_comments) * 100) : (($total_comments > 0) ? 100 : 0);
		$this->data['comment_percent'] = $comment_percent;

		// 5. Khách hàng mới (đăng ký trong 7 ngày qua)
		$input_user = array();
		$input_user['where'] = "created >= '$seven_days_ago'";
		$new_customers = $this->user_model->get_total($input_user);
		$this->data['new_customers'] = $new_customers;
		
		// Khách hàng mới 7 ngày trước đó
		$input_prev_user = array();
		$input_prev_user['where'] = "created >= '$fourteen_days_ago' AND created < '$seven_days_ago'";
		$prev_customers = $this->user_model->get_total($input_prev_user);

		// Phần trăm tăng trưởng người dùng
		$user_percent = ($prev_customers > 0) ? round((($new_customers - $prev_customers) / $prev_customers) * 100) : (($new_customers > 0) ? 100 : 0);
		$this->data['user_percent'] = $user_percent;

		// 6. Tổng lượt xem sản phẩm (Giữ nguyên do view count là cộng dồn, không có timestamp)
		$total_views = 0;
		$products = $this->product_model->get_list();
		foreach ($products as $product) {
			if (isset($product->view)) {
				$total_views += $product->view;
			}
		}
		$this->data['total_views'] = $total_views;
		$this->data['visitor_percent'] = $user_percent; // Lấy tạm theo user_percent vì không có lịch sử traffic local

		// 7. 5 sản phẩm bán chạy nhất (7 ngày qua)
		$this->db->select('product.*, COALESCE(sales.sold_count, 0) as sold_count');
		$this->db->from('product');
		$this->db->join('(SELECT product_id, SUM(qty) as sold_count FROM `order` JOIN transaction ON transaction.id = `order`.transaction_id WHERE transaction.created >= "' . $seven_days_ago . '" GROUP BY product_id) as sales', 'product.id = sales.product_id', 'left');
		$this->db->order_by('sold_count', 'DESC');
		$this->db->limit(5);
		$best_selling = $this->db->get()->result();
		$this->data['best_selling'] = $best_selling;

		// 8. 5 sản phẩm bán ít nhất (7 ngày qua)
		$this->db->select('product.*, COALESCE(sales.sold_count, 0) as sold_count');
		$this->db->from('product');
		$this->db->join('(SELECT product_id, SUM(qty) as sold_count FROM `order` JOIN transaction ON transaction.id = `order`.transaction_id WHERE transaction.created >= "' . $seven_days_ago . '" GROUP BY product_id) as sales', 'product.id = sales.product_id', 'left');
		$this->db->order_by('sold_count', 'ASC');
		$this->db->limit(5);
		$worst_selling = $this->db->get()->result();
		$this->data['worst_selling'] = $worst_selling;

		// 9. 5 sản phẩm được đánh giá tốt nhất (7 ngày qua)
		$this->db->select('product.*, COALESCE(rating.avg_rating, 0) as avg_rating, COALESCE(rating.rating_count, 0) as rating_count');
		$this->db->from('product');
		$this->db->join('(SELECT product_id, AVG(rate) as avg_rating, COUNT(id) as rating_count FROM comments WHERE rate IS NOT NULL AND created >= "' . $seven_days_ago . '" GROUP BY product_id) as rating', 'product.id = rating.product_id', 'inner');
		$this->db->order_by('avg_rating', 'DESC');
		$this->db->limit(5);
		$best_rated = $this->db->get()->result();
		$this->data['best_rated'] = $best_rated;

		// 10. 5 sản phẩm được đánh giá kém nhất (7 ngày qua)
		$this->db->select('product.*, COALESCE(rating.avg_rating, 0) as avg_rating, COALESCE(rating.rating_count, 0) as rating_count');
		$this->db->from('product');
		$this->db->join('(SELECT product_id, AVG(rate) as avg_rating, COUNT(id) as rating_count FROM comments WHERE rate IS NOT NULL AND created >= "' . $seven_days_ago . '" GROUP BY product_id) as rating', 'product.id = rating.product_id', 'inner');
		$this->db->order_by('avg_rating', 'ASC');
		$this->db->limit(5);
		$worst_rated = $this->db->get()->result();
		$this->data['worst_rated'] = $worst_rated;

		// 11. 5 sản phẩm được cho vào giỏ hàng nhiều nhất (Không đổi nếu bảng cart không có created, hoặc đổi nếu có)
		// Giả sử bảng cart không có created, ta dùng all-time.
		$this->db->select('product.*, COUNT(cart.product_id) as cart_count');
		$this->db->from('product');
		$this->db->join('cart', 'product.id = cart.product_id', 'left');
		$this->db->group_by('product.id');
		$this->db->order_by('cart_count', 'DESC');
		$this->db->limit(5);
		$most_carted = $this->db->get()->result();
		$this->data['most_carted'] = $most_carted;


		$dataWeek = $this->get_local_traffic_stats();
		$dataMonth = $this->get_local_month_stats();
		$chartSeries = $this->get_local_chart_series();

		$KEY_FILE_PATH = APPPATH . 'third_party/ga4-key.json';
		$property_id = getenv('GA4_PROPERTY');
		if (empty($property_id) || strtoupper(trim($property_id)) === 'YOUR_KEY') {
			$property_id = '492315679';
		}

		try {
			if (!file_exists(FCPATH . 'vendor/autoload.php')) {
				throw new RuntimeException('Composer vendor chưa được cài đặt');
			}
			if (!file_exists($KEY_FILE_PATH) || !is_readable($KEY_FILE_PATH)) {
				throw new RuntimeException('Missing or unreadable GA4 key file: ' . $KEY_FILE_PATH);
			}

			$client = new BetaAnalyticsDataClient([
				'credentials' => $KEY_FILE_PATH
			]);

			// ===================== THỐNG KÊ 7 NGÀY QUA =====================
			$responseWeek = $client->runReport([
				'property' => 'properties/' . $property_id,
				'dateRanges' => [
					new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today'])
				],
				'metrics' => [
					new Metric(['name' => 'activeUsers']),
					new Metric(['name' => 'sessions']),
					new Metric(['name' => 'newUsers']),
					new Metric(['name' => 'engagedSessions']),
				]
			]);

			$gaWeek = array();
			foreach ($responseWeek->getRows() as $row) {
				foreach ($row->getMetricValues() as $i => $metric) {
					$name = $responseWeek->getMetricHeaders()[$i]->getName();
					$gaWeek[$name] = $metric->getValue();
				}
			}
			if (!empty($gaWeek)) {
				$dataWeek = $gaWeek;
			}

			// ===================== NGƯỜI DÙNG TRONG THÁNG HIỆN TẠI =====================
			$startOfMonth = date('Y-m-01');
			$today = date('Y-m-d');

			$responseMonth = $client->runReport([
				'property' => 'properties/' . $property_id,
				'dateRanges' => [
					new DateRange(['start_date' => $startOfMonth, 'end_date' => $today])
				],
				'metrics' => [
					new Metric(['name' => 'totalUsers']),
					new Metric(['name' => 'newUsers']), // 👈 Thêm dòng này
				]
			]);

			$gaMonth = array();
			foreach ($responseMonth->getRows() as $row) {
				foreach ($row->getMetricValues() as $i => $metric) {
					$name = $responseMonth->getMetricHeaders()[$i]->getName();
					$gaMonth[$name] = $metric->getValue();
				}
			}
			if (!empty($gaMonth)) {
				$dataMonth = $gaMonth;
			}
		} catch (Throwable $e) {
			log_message('error', 'GA4 dashboard error: ' . $e->getMessage());
		}

		$this->data['data'] = $dataWeek;
		$this->data['dataMonth'] = $dataMonth;
		$this->data['chartSeries'] = $chartSeries;

		$this->data['temp'] = 'admin/home/index';
		$this->load->view('admin/main', $this->data);
	}

	private function get_local_traffic_stats()
	{
		$from = date('Y-m-d 00:00:00', strtotime('-6 days'));

		$newUsers = (int) $this->db
			->where('created >=', $from)
			->count_all_results('user');

		$sessions = (int) $this->db
			->where('created >=', $from)
			->count_all_results('transaction');

		$activeRow = $this->db
			->select('COUNT(DISTINCT user_id) AS total', false)
			->where('created >=', $from)
			->get('transaction')
			->row();
		$activeUsers = $activeRow ? (int) $activeRow->total : 0;

		$engagedSessions = (int) $this->db
			->where('created >=', $from)
			->where('status >', 0)
			->count_all_results('transaction');

		return array(
			'activeUsers' => max($activeUsers, $newUsers),
			'newUsers' => $newUsers,
			'sessions' => $sessions,
			'engagedSessions' => $engagedSessions,
		);
	}

	private function get_local_month_stats()
	{
		$startOfMonth = date('Y-m-01 00:00:00');

		$totalUsers = (int) $this->db
			->where('created >=', $startOfMonth)
			->count_all_results('user');

		$newUsers = $totalUsers;

		$orderUsers = $this->db
			->select('COUNT(DISTINCT user_id) AS total', false)
			->where('created >=', $startOfMonth)
			->get('transaction')
			->row();

		if ($orderUsers && (int) $orderUsers->total > $totalUsers) {
			$totalUsers = (int) $orderUsers->total;
		}

		return array(
			'totalUsers' => $totalUsers,
			'newUsers' => $newUsers,
		);
	}

	private function get_local_chart_series()
	{
		$labels = array();
		$dailyOrders = array();
		$dailyNewUsers = array();

		for ($i = 6; $i >= 0; $i--) {
			$day = date('Y-m-d', strtotime("-{$i} days"));
			$nextDay = date('Y-m-d', strtotime('-' . ($i - 1) . ' days'));
			$labels[] = date('d/m', strtotime($day));

			$dailyOrders[] = (int) $this->db
				->where('created >=', $day . ' 00:00:00')
				->where('created <', $nextDay . ' 00:00:00')
				->count_all_results('transaction');

			$dailyNewUsers[] = (int) $this->db
				->where('created >=', $day . ' 00:00:00')
				->where('created <', $nextDay . ' 00:00:00')
				->count_all_results('user');
		}

		return array(
			'labels' => $labels,
			'dailyOrders' => $dailyOrders,
			'dailyNewUsers' => $dailyNewUsers,
		);
	}
}