<style>
	.chart-legend {
		font-size: 14px;
		font-weight: 500;
	}

	.chart-legend span {
		display: inline-block;
		margin-right: 8px;
	}

	.stats-list {
		padding: 15px;
		background-color: #f9f9f9;
		border-radius: 8px;
		list-style: none;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
		font-size: 15px;
		line-height: 1.6;
		margin-bottom: 20px;
	}

	.stats-list li {
		padding: 8px 12px;
		border-left: 4px solid #007bff;
		background-color: #fff;
		margin-bottom: 8px;
		border-radius: 4px;
	}

	.stats-list li strong {
		color: #333;
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




<div class="row">
	<!-- Thêm Widget Doanh thu -->
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-teal panel-widget" style="border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left" style="background-color: #28a745; color: white; border-radius: 8px 0 0 8px;">
					<svg class="glyph stroked line-graph">
						<use xlink:href="#stroked-line-graph"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large" style="color: #28a745;"><?php echo number_format($revenue_7_days); ?>đ</div>
					<div class="text-muted">Doanh thu 7 ngày</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-blue panel-widget ">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked bag">
						<use xlink:href="#stroked-bag"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large"><?php echo $total_all_orders; ?></div>
					<div class="text-muted">Đơn mới 7 ngày</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-orange panel-widget">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked empty-message">
						<use xlink:href="#stroked-empty-message"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large"><?php echo $total_comments; ?></div>
					<div class="text-muted">Bình luận 7 ngày</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-teal panel-widget">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked male-user">
						<use xlink:href="#stroked-male-user"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large"><?php echo $new_customers; ?></div>
					<div class="text-muted">Khách mới 7 ngày</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6 col-lg-3">
		<div class="panel panel-red panel-widget">
			<div class="row no-padding">
				<div class="col-sm-3 col-lg-5 widget-left">
					<svg class="glyph stroked app-window-with-content">
						<use xlink:href="#stroked-app-window-with-content"></use>
					</svg>
				</div>
				<div class="col-sm-9 col-lg-7 widget-right">
					<div class="large"><?php echo number_format($total_views); ?></div>
					<div class="text-muted">Lượt xem (All time)</div>
				</div>
			</div>
		</div>
	</div>
</div><!--/.row-->

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">Site Traffic Overview</div>
			<div class="panel-body">

				<!-- 🔼 Thêm thống kê 7 ngày qua -->
				<h3>📊 Thống kê lưu lượng truy cập trong 7 ngày qua:</h3>
				<ul class="stats-list">
					<li><strong>Khách đặt hàng (7 ngày):</strong> <?= isset($data['activeUsers']) ? number_format($data['activeUsers']) : 'N/A' ?></li>
					<li><strong>Khách hàng mới đăng ký:</strong> <?= isset($data['newUsers']) ? number_format($data['newUsers']) : 'N/A' ?></li>
					<li><strong>Số đơn hàng:</strong> <?= isset($data['sessions']) ? number_format($data['sessions']) : 'N/A' ?></li>
					<li><strong>Đơn đã xác nhận:</strong> <?= isset($data['engagedSessions']) ? number_format($data['engagedSessions']) : 'N/A' ?></li>
				</ul>

				<!-- 🔽 Biểu đồ + legend -->
				<div class="canvas-wrapper" style="margin-top: 20px;">
					<canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>

					<!-- Legend -->
					<div id="chart-legend" class="chart-legend" style="margin-top: 10px;">
						<span style="color: rgba(220,220,220,1); font-size: 18px;">■</span> Số đơn hàng mỗi ngày &nbsp;&nbsp;
						<span style="color: rgba(48, 164, 255, 1); font-size: 18px;">■</span> Khách đăng ký mới mỗi ngày
					</div>
				</div>

			</div>
		</div>
	</div>
</div><!--/.row-->

<div id="userStats"
	data-info='<?= json_encode([
					"labels" => $chartSeries["labels"] ?? [],
					"dailyOrders" => $chartSeries["dailyOrders"] ?? [],
					"dailyNewUsers" => $chartSeries["dailyNewUsers"] ?? [],
					"totalUsers" => $dataMonth["totalUsers"] ?? 0,
					"newUsers" => $dataMonth["newUsers"] ?? 0
				], JSON_UNESCAPED_UNICODE) ?>'>
</div>

	<!-- Thống kê sản phẩm -->
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">Thống kê sản phẩm</div>
			<div class="panel-body">
				<div class="row">
					<!-- 5 sản phẩm bán chạy nhất -->
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">5 sản phẩm bán chạy nhất (7 ngày qua)</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Lượt xem</th>
												<th>Đã bán (7 ngày)</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($best_selling as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->view; ?></td>
													<td><?php echo $product->sold_count; ?></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<!-- 5 sản phẩm bán ít nhất -->
					<div class="col-md-6">
						<div class="panel panel-danger">
							<div class="panel-heading">5 sản phẩm bán ít nhất (7 ngày qua)</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Lượt xem</th>
												<th>Đã bán (7 ngày)</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($worst_selling as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->view; ?></td>
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
					<!-- 5 sản phẩm được đánh giá tốt nhất -->
					<div class="col-md-6">
						<div class="panel panel-success">
							<div class="panel-heading">5 sản phẩm được đánh giá tốt nhất (7 ngày qua)</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Lượt xem</th>
												<th>Đánh giá (7 ngày)</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($best_rated as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->view; ?></td>
													<td><?php echo number_format($product->avg_rating, 1); ?>/5 (<?php echo $product->rating_count; ?> đánh giá)</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

					<!-- 5 sản phẩm được đánh giá kém nhất -->
					<div class="col-md-6">
						<div class="panel panel-warning">
							<div class="panel-heading">5 sản phẩm được đánh giá kém nhất (7 ngày qua)</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Lượt xem</th>
												<th>Đánh giá</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($worst_rated as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->view; ?></td>
													<td><?php echo number_format($product->avg_rating, 1); ?>/5 (<?php echo $product->rating_count; ?> đánh giá)</td>
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
					<!-- 5 sản phẩm được cho vào giỏ hàng nhiều nhất -->
					<div class="col-md-6">
						<div class="panel panel-info">
							<div class="panel-heading">5 sản phẩm được cho vào giỏ hàng nhiều nhất</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Tên sản phẩm</th>
												<th>Giá</th>
												<th>Lượt xem</th>
												<th>Số lần thêm vào giỏ</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($most_carted as $product): ?>
												<tr>
													<td><?php echo $product->name; ?></td>
													<td><?php echo number_format($product->price); ?>đ</td>
													<td><?php echo $product->view; ?></td>
													<td><?php echo $product->cart_count; ?></td>
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



