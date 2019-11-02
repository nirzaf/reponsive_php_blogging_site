<?php
require_once(__DIR__ . '/../inc/config.php');
?>
<?php
// init vars
$form_submitted = 0;
$invalid_email  = 0;
$request_sent   = 0;
$user_exists    = 0;

// initialize swiftmailer
$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
	->setUsername($smtp_user)
	->setPassword($smtp_pass);
$mailer = Swift_Mailer::newInstance($transport_smtp);

// check if form submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$form_submitted = 1;
	$email          = (!empty($_POST['email'])) ? $_POST['email']    : '';

	// validate email
	if(!Swift_Validate::email($email)){ //if email is not valid
		$invalid_email = 1;
	}
}

// if all fields submitted
if(!empty($email) && !$invalid_email) {
	$empty_fields = 0;

	// user ip
	$ip = get_ip();

	// check if exists
	$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	// user exists?
	if($count > 0) {
		// get user id
		$stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
		$stmt->bindValue(':email', $email);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$user_id = $row['id'];

		// email user
		$query = "SELECT * FROM email_templates WHERE type = 'signup_confirm'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = $row['subject'];
		$email_body    = $row['body'];

		// get confirmation string
		$stmt = $conn->prepare("SELECT confirm_str FROM signup_confirm WHERE user_id = :user_id");
		$stmt->bindValue(':user_id', $user_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$confirm_str = $row['confirm_str'];

		if(empty($confirm_str)) {
			$confirm_str = generatePassword(16);

			// insert confirmation string into db
			$stmt = $conn->prepare('
			INSERT INTO signup_confirm(
				user_id,
				confirm_str,
				created
				)
			VALUES(
				:user_id,
				:confirm_str,
				NOW()
				)
			');

			$stmt->bindValue(':user_id'    , $user_id);
			$stmt->bindValue(':confirm_str', $confirm_str);
			$stmt->execute();

		} // end if empty $confirm_str

		$confirm_link = $baseurl . "/user/signup-confirm/" . $user_id . "," . $confirm_str;
		$email_body = str_replace('%confirm_link%', $confirm_link, $email_body);

		$message = Swift_Message::newInstance()
			->setSubject($email_subject)
			->setFrom(array($admin_email => $site_name))
			->setTo($email)
			->setBody($email_body)
			->setReplyTo($admin_email)
			->setReturnPath($admin_email)
			;

		// Send the message
		$mailer->send($message);

		$request_sent = 1;
		$user_exists  = 1;
	} // end if($count > 0)
} // end if(!empty($email) && !$invalid_email)

// v. 1.06
// translation var check if exists
$txt_html_title        = (!empty($txt_html_title        )) ? $txt_html_title         : "Resend Confirmation Email";
$txt_main_title        = (!empty($txt_main_title        )) ? $txt_main_title         : "Resend Confirmation Email";
$txt_wrong_pass        = (!empty($txt_wrong_pass        )) ? $txt_wrong_pass         : "Wrong password or email";
$txt_label_email       = (!empty($txt_label_email       )) ? $txt_label_email        : "Email";
$txt_confirmation_sent = (!empty($txt_confirmation_sent )) ? $txt_confirmation_sent  : "Confirmation sent. Please check your email.";
$txt_link_log_in       = (!empty($txt_link_log_in       )) ? $txt_link_log_in        : "Login";
$txt_mailer_problem    = (!empty($txt_mailer_problem    )) ? $txt_mailer_problem     : "Email failed";
$txt_link_try_again    = (!empty($txt_link_try_again    )) ? $txt_link_try_again     : "Try again";
$txt_invalid_email     = (!empty($txt_invalid_email     )) ? $txt_invalid_email      : "Invalid email";

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_resend-confirmation.php');