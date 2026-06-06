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
		$filter = $this->input->get('filter');
		if (!$filter) $filter = 'this_week';

		$from_date = $this->input->get('from_date');
		$to_date = $this->input->get('to_date');

		$start_date = '';
		$end_date = '';

		switch ($filter) {
			case 'today':
				$start_date = date('Y-m-d 00:00:00');
				$end_date = date('Y-m-d 23:59:59');
				break;
			case '7_days':
				$start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
				$end_date = date('Y-m-d 23:59:59');
				break;
			case 'this_week':
				$start_date = date('Y-m-d 00:00:00', strtotime('monday this week'));
				$end_date = date('Y-m-d 23:59:59', strtotime('sunday this week'));
				break;
			case 'this_month':
				$start_date = date('Y-m-01 00:00:00');
				$end_date = date('Y-m-t 23:59:59');
				break;

			case 'custom':
				if ($from_date) $start_date = date('Y-m-d 00:00:00', strtotime($from_date));
				if ($to_date) $end_date = date('Y-m-d 23:59:59', strtotime($to_date));
				break;
		}

		$this->data['filter'] = $filter;
		$this->data['from_date'] = $from_date;
		$this->data['to_date'] = $to_date;

		// 1. KPI
		// Tổng đơn hàng
		$this->db->where("created >= '$start_date'");
		if ($end_date) $this->db->where("created <= '$end_date'");
		$total_all_orders = $this->transaction_model->get_total();
		$this->data['total_all_orders'] = $total_all_orders;

		// Đơn hàng thành công
		$this->db->where("status > 0");
		$this->db->where("created >= '$start_date'");
		if ($end_date) $this->db->where("created <= '$end_date'");
		$successful_orders = $this->transaction_model->get_total();

		// Tổng doanh thu
		$this->db->select_sum('amount', 'total_revenue');
		$this->db->where('status >', 0);
		$this->db->where("created >= '$start_date'");
		if ($end_date) $this->db->where("created <= '$end_date'");
		$revenue_query = $this->db->get('transaction')->row();
		$revenue = $revenue_query ? $revenue_query->total_revenue : 0;
		$this->data['revenue'] = $revenue;

		// Khách mới
		$this->db->where("created >= '$start_date'");
		if ($end_date) $this->db->where("created <= '$end_date'");
		$new_customers = $this->user_model->get_total();
		$this->data['new_customers'] = $new_customers;

		// Giá trị đơn TB
		$avg_order_value = ($successful_orders > 0) ? ($revenue / $successful_orders) : 0;
		$this->data['avg_order_value'] = $avg_order_value;

		// 2. Chart data
		$chart_labels = [];
		$chart_revenue = [];
		$chart_orders = [];

		if ($filter == 'this_month') {
			// 4 weeks
			for ($i = 1; $i <= 4; $i++) {
				$chart_labels[] = "Tuần $i";
				$w_start = date('Y-m-d 00:00:00', strtotime(date('Y-m-01') . " + " . ($i-1)*7 . " days"));
				$w_end = ($i == 4) ? date('Y-m-t 23:59:59') : date('Y-m-d 23:59:59', strtotime($w_start . " + 6 days"));

				$this->db->select_sum('amount', 'total_revenue');
				$this->db->where('status >', 0);
				$this->db->where("created >= '$w_start'");
				$this->db->where("created <= '$w_end'");
				$r_query = $this->db->get('transaction')->row();
				$chart_revenue[] = $r_query ? (int)$r_query->total_revenue : 0;

				$this->db->where("created >= '$w_start'");
				$this->db->where("created <= '$w_end'");
				$chart_orders[] = $this->transaction_model->get_total();
			}
		} else {
			// Days
			$diff = 0;
			if ($start_date && $end_date) {
				$d1 = new DateTime($start_date);
				$d2 = new DateTime($end_date);
				$diff = $d1->diff($d2)->days;
			}
			if ($diff > 31) $diff = 31; 
			if ($diff == 0 && $filter == 'today') {
				// today: just show today
				$temp_start = date('Y-m-d');
				$diff = 0;
			} else if ($diff == 0) {
				// default to 6 days ago (total 7 days) if not today but no end date logic (shouldn't happen)
				$temp_start = date('Y-m-d', strtotime('-6 days'));
				$diff = 6;
			} else {
				$temp_start = date('Y-m-d', strtotime($start_date));
			}

			$day_names = [
				'Mon' => 'T2',
				'Tue' => 'T3',
				'Wed' => 'T4',
				'Thu' => 'T5',
				'Fri' => 'T6',
				'Sat' => 'T7',
				'Sun' => 'CN'
			];

			for ($i = 0; $i <= $diff; $i++) {
				$d = date('Y-m-d', strtotime($temp_start . " + $i days"));
				if ($filter == 'this_week') {
					$chart_labels[] = $day_names[date('D', strtotime($d))];
				} else {
					$chart_labels[] = date('d/m', strtotime($d));
				}
				
				$this->db->select_sum('amount', 'total_revenue');
				$this->db->where('status >', 0);
				$this->db->where("created >= '$d 00:00:00'");
				$this->db->where("created <= '$d 23:59:59'");
				$r_query = $this->db->get('transaction')->row();
				$chart_revenue[] = $r_query ? (int)$r_query->total_revenue : 0;

				$this->db->where("created >= '$d 00:00:00'");
				$this->db->where("created <= '$d 23:59:59'");
				$chart_orders[] = $this->transaction_model->get_total();
			}
		}

		$this->data['chart_labels'] = $chart_labels;
		$this->data['chart_revenue'] = $chart_revenue;
		$this->data['chart_orders'] = $chart_orders;

		// 3. Compare Period
		$current_period_start = $start_date;
		$current_period_end = $end_date;

		$prev_period_start = '';
		$prev_period_end = '';

		$compare_label_current = 'Kỳ này';
		$compare_label_prev = 'Kỳ trước';

		switch ($filter) {
			case 'today':
				$prev_period_start = date('Y-m-d 00:00:00', strtotime('-1 day'));
				$prev_period_end = date('Y-m-d 23:59:59', strtotime('-1 day'));
				$compare_label_current = 'Hôm nay';
				$compare_label_prev = 'Hôm qua';
				break;
			case '7_days':
				$prev_period_start = date('Y-m-d 00:00:00', strtotime('-14 days'));
				$prev_period_end = date('Y-m-d 23:59:59', strtotime('-8 days'));
				$compare_label_current = '7 ngày qua';
				$compare_label_prev = '7 ngày trước';
				break;
			case 'this_week':
				$prev_period_start = date('Y-m-d 00:00:00', strtotime('monday last week'));
				$prev_period_end = date('Y-m-d 23:59:59', strtotime('sunday last week'));
				$compare_label_current = 'Tuần này';
				$compare_label_prev = 'Tuần trước';
				break;
			case 'this_month':
				$prev_period_start = date('Y-m-01 00:00:00', strtotime('first day of last month'));
				$prev_period_end = date('Y-m-t 23:59:59', strtotime('last day of last month'));
				$compare_label_current = 'Tháng này';
				$compare_label_prev = 'Tháng trước';
				break;

			case 'custom':
			default:
				if ($start_date && $end_date) {
					$d1 = new DateTime($start_date);
					$d2 = new DateTime($end_date);
					$diff = $d1->diff($d2)->days + 1;
					$prev_period_start = date('Y-m-d 00:00:00', strtotime($start_date . " -$diff days"));
					$prev_period_end = date('Y-m-d 23:59:59', strtotime($end_date . " -$diff days"));
				}
				$compare_label_current = 'Kỳ này';
				$compare_label_prev = 'Kỳ trước';
				break;
		}

		$this->db->select_sum('amount', 'total_revenue');
		$this->db->where('status >', 0);
		if ($current_period_start) $this->db->where("created >= '$current_period_start'");
		if ($current_period_end) $this->db->where("created <= '$current_period_end'");
		$r_query = $this->db->get('transaction')->row();
		$this_period_revenue = $r_query ? $r_query->total_revenue : 0;

		$this->db->select_sum('amount', 'total_revenue');
		$this->db->where('status >', 0);
		if ($prev_period_start) $this->db->where("created >= '$prev_period_start'");
		if ($prev_period_end) $this->db->where("created <= '$prev_period_end'");
		$r_query = $this->db->get('transaction')->row();
		$last_period_revenue = $r_query ? $r_query->total_revenue : 0;

		$growth = 0;
		if ($last_period_revenue > 0) {
			$growth = (($this_period_revenue - $last_period_revenue) / $last_period_revenue) * 100;
		} else if ($this_period_revenue > 0) {
			$growth = 100;
		}

		$this->data['this_month_revenue'] = $this_period_revenue;
		$this->data['last_month_revenue'] = $last_period_revenue;
		$this->data['growth'] = $growth;
		$this->data['compare_label_current'] = $compare_label_current;
		$this->data['compare_label_prev'] = $compare_label_prev;

		// 4. Top products by revenue
		$this->db->select('product.*, COALESCE(sales.sold_count, 0) as sold_count, COALESCE(sales.revenue, 0) as revenue');
		$this->db->from('product');
		$this->db->join('(SELECT `order`.product_id, SUM(`order`.qty) as sold_count, SUM(`order`.amount) as revenue FROM `order` JOIN transaction ON transaction.id = `order`.transaction_id WHERE transaction.created >= "' . $start_date . '" ' . ($end_date ? 'AND transaction.created <= "' . $end_date . '"' : '') . ' AND transaction.status > 0 GROUP BY `order`.product_id) as sales', 'product.id = sales.product_id', 'left');
		$this->db->order_by('revenue', 'DESC');
		$this->db->limit(5);
		$best_revenue = $this->db->get()->result();
		$this->data['best_revenue'] = $best_revenue;

		// 5. Best selling
		$this->db->select('product.*, COALESCE(sales.sold_count, 0) as sold_count');
		$this->db->from('product');
		$this->db->join('(SELECT `order`.product_id, SUM(`order`.qty) as sold_count FROM `order` JOIN transaction ON transaction.id = `order`.transaction_id WHERE transaction.created >= "' . $start_date . '" ' . ($end_date ? 'AND transaction.created <= "' . $end_date . '"' : '') . ' AND transaction.status > 0 GROUP BY `order`.product_id) as sales', 'product.id = sales.product_id', 'left');
		$this->db->order_by('sold_count', 'DESC');
		$this->db->limit(5);
		$best_selling = $this->db->get()->result();
		$this->data['best_selling'] = $best_selling;

		// 6. Worst selling
		$this->db->select('product.*, COALESCE(sales.sold_count, 0) as sold_count');
		$this->db->from('product');
		$this->db->join('(SELECT `order`.product_id, SUM(`order`.qty) as sold_count FROM `order` JOIN transaction ON transaction.id = `order`.transaction_id WHERE transaction.created >= "' . $start_date . '" ' . ($end_date ? 'AND transaction.created <= "' . $end_date . '"' : '') . ' AND transaction.status > 0 GROUP BY `order`.product_id) as sales', 'product.id = sales.product_id', 'left');
		$this->db->order_by('sold_count', 'ASC');
		$this->db->limit(5);
		$worst_selling = $this->db->get()->result();
		$this->data['worst_selling'] = $worst_selling;

		$this->data['temp'] = 'admin/home/index';
		$this->load->view('admin/main', $this->data);
	}
}