<?php
	$labels = [
		'today' => 'Hôm nay',
		'7_days' => '7 ngày qua',
		'this_week' => 'Tuần này',
		'this_month' => 'Tháng này',
		'this_year' => 'Năm nay',
		'custom' => 'Tùy chọn'
	];
	$display_label = $labels[$filter] ?? 'Tuần này';
?>
<style>
	.chart-legend {
		font-size: 14px;
		font-weight: 500;
	}

	.chart-legend span {
		display: inline-block;
		margin-right: 8px;
	}
</style>

<div class="row">
	<ol class="breadcrumb">
		<li><a href="#"><svg class="glyph stroked home">
					<use xlink:href="#stroked-home"></use>
				</svg></a></li>
		<li class="active">Quản trị</li>
	</ol>
</div><!--/.row-->

<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">Trang quản trị</h1>
	</div>
</div><!--/.row-->

<!-- Hàng bộ lọc thời gian -->
<div class="row">
	<div class="col-lg-12">
		<form method="get" action="<?php echo admin_url('home'); ?>" class="form-inline" style="margin-bottom: 20px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
			<div class="form-group">
				<label style="margin-right: 10px;">Thống kê theo:</label>
				<select name="filter" class="form-control" onchange="this.form.submit()">
					<option value="today" <?php if($filter=='today') echo 'selected';?>>Hôm nay</option>
					<option value="7_days" <?php if($filter=='7_days') echo 'selected';?>>7 ngày gần nhất</option>
					<option value="this_week" <?php if($filter=='this_week') echo 'selected';?>>Tuần này</option>
					<option value="this_month" <?php if($filter=='this_month') echo 'selected';?>>Tháng này</option>
				</select>
			</div>
		</form>
	</div>
</div>

<!-- Hàng KPI -->
<div class="row">
	<!-- Doanh thu -->
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-teal panel-widget" style="border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left" style="background-color: #28a745; color: white; border-radius: 8px 0 0 8px;">
					<svg class="glyph stroked line-graph">
						<use xlink:href="#stroked-line-graph"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large" style="color: #28a745; font-size: 24px;"><?php echo number_format($revenue); ?>đ</div>
					<div class="text-muted">Doanh thu <br><small>(<?php echo $display_label; ?>)</small></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Đơn hàng -->
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-blue panel-widget ">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked bag">
						<use xlink:href="#stroked-bag"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large" style="font-size: 24px;"><?php echo $total_all_orders; ?></div>
					<div class="text-muted">Đơn hàng <br><small>(<?php echo $display_label; ?>)</small></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Khách mới -->
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-teal panel-widget">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked male-user">
						<use xlink:href="#stroked-male-user"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large" style="font-size: 24px;"><?php echo $new_customers; ?></div>
					<div class="text-muted">Khách mới <br><small>(<?php echo $display_label; ?>)</small></div>
				</div>
			</div>
		</div>
	</div>

	<!-- Giá trị đơn TB -->
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-orange panel-widget">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked tag">
						<use xlink:href="#stroked-tag"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large" style="font-size: 24px;"><?php echo number_format($avg_order_value); ?>đ</div>
					<div class="text-muted">Giá trị đơn TB <br><small>(<?php echo $display_label; ?>)</small></div>
				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->

<!-- Biểu đồ doanh thu chính và So sánh -->
<div class="row">
	<div class="col-lg-8">
		<div class="panel panel-default">
			<div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
				<span>Biểu đồ doanh thu</span>
			</div>
			<div class="panel-body">
				<div class="canvas-wrapper">
					<canvas class="main-chart" id="revenue-chart" height="200" width="600"></canvas>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">So sánh doanh thu</div>
			<div class="panel-body" style="font-size: 16px;">
				<div style="margin-bottom: 15px; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px;">
					<strong><?php echo $compare_label_current; ?>:</strong> 
					<span style="color: #28a745; font-weight: bold;"><?php echo number_format($this_month_revenue); ?>đ</span>
				</div>
				<div style="margin-bottom: 15px; display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px;">
					<strong><?php echo $compare_label_prev; ?>:</strong> 
					<span style="color: #666;"><?php echo number_format($last_month_revenue); ?>đ</span>
				</div>
				<div style="display: flex; justify-content: space-between; align-items: center;">
					<strong>Tăng trưởng:</strong>
					<?php if ($growth > 0): ?>
						<span style="color: #28a745; font-weight: bold; font-size: 18px;">
							<svg class="glyph stroked arrow-up" style="width: 20px; height: 20px; vertical-align: middle;"><use xlink:href="#stroked-arrow-up"></use></svg>
							+<?php echo number_format($growth, 1); ?>%
						</span>
					<?php elseif ($growth < 0): ?>
						<span style="color: #dc3545; font-weight: bold; font-size: 18px;">
							<svg class="glyph stroked arrow-down" style="width: 20px; height: 20px; vertical-align: middle;"><use xlink:href="#stroked-arrow-down"></use></svg>
							<?php echo number_format($growth, 1); ?>%
						</span>
					<?php else: ?>
						<span style="color: #666; font-weight: bold; font-size: 18px;">0%</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->

<!-- Thống kê sản phẩm -->
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">Thống kê sản phẩm</div>
			<div class="panel-body">
				<div class="row">
					<!-- 5 sản phẩm doanh thu cao nhất -->
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">5 sản phẩm doanh thu cao nhất</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Đã bán</th>
												<th>Doanh thu</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($best_revenue as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->sold_count; ?></td>
													<td style="color: #28a745; font-weight: bold;"><?php echo number_format($product->revenue); ?>đ</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<!-- 5 sản phẩm bán chạy nhất -->
					<div class="col-md-6">
						<div class="panel panel-info">
							<div class="panel-heading">5 sản phẩm bán chạy nhất</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Đã bán</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($best_selling as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->sold_count; ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- 5 sản phẩm bán ít nhất -->
					<div class="col-md-6">
						<div class="panel panel-danger">
							<div class="panel-heading">5 sản phẩm bán ít nhất</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Đã bán</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($worst_selling as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->sold_count; ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->

<script>
window.addEventListener('load', function () {
	setTimeout(function() {
		// Dữ liệu biểu đồ từ PHP
		var chartLabels = <?php echo json_encode($chart_labels); ?>;
		var chartRevenue = <?php echo json_encode($chart_revenue); ?>;
		var chartOrders = <?php echo json_encode($chart_orders); ?>;

		var chartData = {
			labels : chartLabels,
			datasets : [
				{
					label: "Doanh thu (VNĐ)",
					fillColor : "rgba(40, 167, 69, 0.2)",
					strokeColor : "rgba(40, 167, 69, 1)",
					pointColor : "rgba(40, 167, 69, 1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(40, 167, 69, 1)",
					data : chartRevenue
				},
				{
					label: "Số đơn hàng",
					fillColor : "rgba(48, 164, 255, 0.2)",
					strokeColor : "rgba(48, 164, 255, 1)",
					pointColor : "rgba(48, 164, 255, 1)",
					pointStrokeColor : "#fff",
					pointHighlightFill : "#fff",
					pointHighlightStroke : "rgba(48, 164, 255, 1)",
					data : chartOrders
				}
			]
		};

		var canvasEl = document.getElementById("revenue-chart");
		if (canvasEl && typeof Chart !== 'undefined') {
			var chartContext = canvasEl.getContext("2d");
			if (chartLabels.length <= 1) {
				// Nếu chỉ có 1 điểm (ví dụ: Hôm nay), dùng Bar chart để dễ nhìn
				window.myRevenueChart = new Chart(chartContext).Bar(chartData, {
					responsive: true,
					scaleLineColor: "rgba(0,0,0,.2)",
					scaleGridLineColor: "rgba(0,0,0,.05)",
					scaleFontColor: "#c5c7cc"
				});
			} else {
				window.myRevenueChart = new Chart(chartContext).Line(chartData, {
					responsive: true,
					scaleLineColor: "rgba(0,0,0,.2)",
					scaleGridLineColor: "rgba(0,0,0,.05)",
					scaleFontColor: "#c5c7cc",
					multiTooltipTemplate: "<%= datasetLabel %>: <%= value %>"
				});
			}
		}
	}, 500); // Đợi 500ms để chắc chắn chart-data.js không ghi đè
});
</script>