<meta charset="UTF-8">
<title>Shop quần áo mini</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<script src="<?php echo public_url(); ?>js/jquery-3.1.1.js" type="text/javascript"></script>
<!-- <script src="<?php echo public_url('js/jqzoom_ev'); ?>js/jquery.jcarousel.pack.js" type="text/javascript"></script> -->
<link rel="stylesheet" type="text/css" href="<?php echo public_url('site/'); ?>bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo public_url('site/'); ?>css/style.css">
<script type="text/javascript" src="<?php echo public_url('js/raty/jquery.raty.min.js') ?>"></script>
<script type="text/javascript">
	(function() {
		if (document.getElementById('chative-messenger-script')) {
			return;
		}

		var chativeScript = document.createElement('script');
		chativeScript.id = 'chative-messenger-script';
		chativeScript.src = 'https://messenger.svc.chative.io/static/v1.0/channels/s421f6635-9e6c-4a14-a621-af9385424f8f/messenger.js?mode=livechat';
		chativeScript.defer = true;
		chativeScript.onerror = function() {
			console.warn('Không tải được chatbot Chative. Kiểm tra kết nối mạng hoặc cấu hình channel trên chative.io');
		};
		document.head.appendChild(chativeScript);
	})();
</script>
<script type="text/javascript">
	$(function() {
		$.fn.raty.defaults.path = "<?php echo public_url('js/raty/img'); ?>";
		$('.raty').raty({
			score: function() {
				return $(this).attr('data-score');
			},
			readOnly: true,
		});
	});
</script>

<style>
		.raty img {
			width: 16px !important;
			height: 16px !important;
		}

</style>