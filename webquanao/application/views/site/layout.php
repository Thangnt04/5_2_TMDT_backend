<!DOCTYPE html>
<html lang="en">
<head>
	<?php $this->load->view('site/head',$this->data); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo public_url('site/'); ?>css/validation.css">
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QPG3ZQV73K"></script>
<script>
	window.dataLayer = window.dataLayer || [];

	function gtag() {
		dataLayer.push(arguments);
	}
	gtag('js', new Date());

	gtag('config', 'G-QPG3ZQV73K');
</script>
<body>
	<div class="container">
		<?php $this->load->view('site/header',$this->data); ?>
		<?php $this->load->view($temp,$this->data); ?>
		<?php $this->load->view('site/footer',$this->data); ?>
	</div>
    <script src="<?php echo public_url('site/'); ?>bootstrap/js/bootstrap.min.js"></script>
	<!-- Chative Live Chat -->
	<script src="https://messenger.svc.chative.io/static/v1.0/channels/s421f6635-9e6c-4a14-a621-af9385424f8f/messenger.js?mode=livechat" defer="defer"></script>
</body>
</html>