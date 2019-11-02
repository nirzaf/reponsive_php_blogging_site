<?php
require_once(__DIR__ . '/inc/config.php');

$submit_check = $_POST['submit_check'];
$name         = $_POST['name'];
$email        = $_POST['email'];
$message      = $_POST['message'];

// initialize swiftmailer
$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
	->setUsername($smtp_user)
	->setPassword($smtp_pass);
$mailer = Swift_Mailer::newInstance($transport_smtp);

// send web_accept email
$message = Swift_Message::newInstance()
	->setSubject($site_name . ' contact form')
	->setFrom(array($email => $name))
	->setTo($admin_email)
	->setBody($message)
	->setReplyTo($email)
	->setReturnPath($admin_email)
	;

	try {
		$mailer->send($message);
	} catch (Exception $e) {
		echo $e->getMessage;
	}

header("Location: " . $baseurl . "/_msg.php?msg=contact_submitted");