<?php
require_once(__DIR__ . '/../inc/config.php');
?>
<?php
// init vars
$user_exists    = 0;
$empty_fields   = 1;
$invalid_email  = 0;
$ip             = '';
$form_submitted = 0;
$request_sent   = 0;
$mailer_problem = 0;

// check if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$form_submitted = 1;
	$email = $_POST['email'];

	// validate email
	if(!Swift_Validate::email($email)){
		//if email is not valid
		$invalid_email = 1;
	}
}

// if all fields submitted
if(!$invalid_email && $form_submitted) {
	$empty_fields = 0;

	// check to see if email already exists
	$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	// user exists?
	if($count > 0) {
		$user_exists = 1;

		// get user id
		$stmt = $conn->prepare('SELECT id FROM users WHERE email = :email');
		$stmt->bindValue(':email', $email);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$id = $row['id'];

		// generate token
		$token = generatePassword(16);

		// insert token into db
		$stmt = $conn->prepare('INSERT INTO pass_request(user_id, token) VALUES(:userid, :token)');
		$stmt->bindValue(':userid', $id);
		$stmt->bindValue(':token', $token);
		$stmt->execute();

		// get reset password email template
		$query = "SELECT * FROM email_templates WHERE type = 'reset_pass'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = $row['subject'];
		$email_body    = $row['body'];

		$reset_link = $baseurl . "/user/password-reset/" . $id . "," . $token;
		$email_body = str_replace('%reset_link%', $reset_link, $email_body);

		$message = Swift_Message::newInstance()
			->setSubject($email_subject)
			->setFrom(array($admin_email => $site_name))
			->setTo($email)
			->setBody($email_body)
			->setReplyTo($admin_email)
			->setReturnPath($admin_email)
			;

		// Create transport with SMTP
		$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
		->setUsername($smtp_user)
		->setPassword($smtp_pass)
		;

		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport_smtp);

		// Send the message
		// $numSent = $mailer->send($message);
		if ($mailer->send($message)) {
			$request_sent = 1;
		}
		else {
			$mailer_problem = 1;
		}
	} // user exists?
	// else email doesn't exist
	else {
		$user_exists = 0;
	} // end else user doesn't exist, so create entry in db
} // end if(!$invalid_email && $form_submitted)

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_password-request.php');