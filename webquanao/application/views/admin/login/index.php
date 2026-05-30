<!DOCTYPE html>
<html>
<head>
	<?php $this->load->view('admin/head.php'); ?>
</head>

<body>
	<div class="row">
		<div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
			<div class="login-panel panel panel-default">
				<div class="panel-heading">Đăng nhập</div>
				<div class="panel-body">
					<?php if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') { ?>
						<p class="text-muted" style="font-size:12px;margin-bottom:12px;">
							Tài khoản mẫu: <strong>admin@gmail.com</strong> / <strong>11111111</strong> (8 số 1)
						</p>
					<?php } ?>
					<form role="form" method="post">
						<fieldset>
							<div class="form-group">
								<input class="form-control" placeholder="E-mail" name="email" type="email" autofocus=""><?php echo form_error('email'); ?>
							</div>
							<div class="form-group">
								<input class="form-control" placeholder="Mật khẩu" name="password" type="password" value=""><?php echo form_error('password'); ?>
							</div>
							<h2><?php echo form_error('login'); ?></h2>
							<div class="checkbox">
								<label>
									<input name="remember" type="checkbox" value="Remember Me">Nhớ tên đăng nhập
								</label>
							</div>
							<button type="submit" class="btn btn-primary">Đăng nhập</button>
						</fieldset>
					</form>
				</div>
			</div>
		</div><!-- /.col-->
	</div><!-- /.row -->	
	<?php $this->load->view('admin/footer.php'); ?>	
</body>

</html>
