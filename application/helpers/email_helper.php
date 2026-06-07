<?php

function mail_is_configured()
{
	$username = getenv('MAIL_USERNAME');
	$password = getenv('MAIL_PASSWORD');
	$invalid = array('YOUR_EMAIL', 'YOUR_PASSWORD', 'YOUR_KEY', '');

	if (empty($username) || empty($password)) {
		return false;
	}

	return !in_array(strtoupper(trim($username)), $invalid, true)
		&& !in_array(strtoupper(trim($password)), $invalid, true);
}

function mail_load_phpmailer()
{
	$composerAutoload = FCPATH . 'vendor/autoload.php';
	if (file_exists($composerAutoload)) {
		require_once $composerAutoload;
		return class_exists('PHPMailer\\PHPMailer\\PHPMailer');
	}

	$base = APPPATH . 'third_party/PHPMailer/src/';
	$files = array('Exception.php', 'PHPMailer.php', 'SMTP.php');
	foreach ($files as $file) {
		if (!file_exists($base . $file)) {
			return false;
		}
	}

	require_once $base . 'Exception.php';
	require_once $base . 'PHPMailer.php';
	require_once $base . 'SMTP.php';

	return class_exists('PHPMailer\\PHPMailer\\PHPMailer');
}

function send_email($to, $to_name, $subject, $body, $altBody)
{
	if (!mail_is_configured()) {
		log_message('debug', 'Email skipped: SMTP chưa cấu hình trong .env');
		return true;
	}

	if (!mail_load_phpmailer()) {
		log_message('error', 'Email skipped: thư viện PHPMailer chưa được cài đặt');
		return true;
	}

	$mail = new PHPMailer\PHPMailer\PHPMailer(true);

	try {
		$mail->isSMTP();
		$mail->Host = 'smtp.gmail.com';
		$mail->SMTPAuth = true;
		$mail->Username = getenv('MAIL_USERNAME');
		$mail->Password = getenv('MAIL_PASSWORD');
		$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
		$mail->Port = 587;

		$mail->setFrom(getenv('MAIL_USERNAME'), 'Shop quần áo mini');
		$mail->addAddress($to, $to_name);
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Subject = $subject;
		$mail->Body = $body;
		$mail->AltBody = $altBody;

		return $mail->send();
	} catch (Exception $e) {
		log_message('error', 'Gửi email thất bại: ' . $e->getMessage());
		return false;
	} catch (PHPMailer\PHPMailer\Exception $e) {
		log_message('error', 'Gửi email thất bại: ' . $e->getMessage());
		return false;
	}
}
