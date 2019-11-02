<?php
require_once(__DIR__ . '/../inc/config.php');
?>
<?php
// init vars
$user_exists    = 0;
$user_created   = 0;
$form_submitted = 0;
$empty_fields   = 1;
$invalid_email  = 0;

// initialize swiftmailer
$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
	->setUsername($smtp_user)
	->setPassword($smtp_pass);
$mailer = Swift_Mailer::newInstance($transport_smtp);

// check if form submitted
if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$form_submitted = 1;
	$fname          = (!empty($_POST['fname']   )) ? $_POST['fname']    : '';
	$lname          = (!empty($_POST['lname']   )) ? $_POST['lname']    : '';
	$email          = (!empty($_POST['email']   )) ? $_POST['email']    : '';
	$password       = (!empty($_POST['password'])) ? $_POST['password'] : '';

	// validate email
	if(!Swift_Validate::email($email)){ //if email is not valid
		$invalid_email = 1;
	}
}

// if all fields submitted
if(!empty($fname) && !empty($email) && !empty($password) && !$invalid_email) {
	$empty_fields = 0;

	// user ip
	$ip = get_ip();

	// check to see if email already exists
	$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	// user exists?
	if($count > 0) {
		$user_exists = 1;
	}
	// else user doesn't exist, so create entry in db
	else {
		// hash
		$password_hashed = password_hash($password, PASSWORD_BCRYPT);

		// insert into db
		$stmt = $conn->prepare('
		INSERT INTO users(
			first_name,
			last_name,
			email,
			password,
			created,
			hybridauth_provider_name,
			ip_addr,
			status
			)
		VALUES(
			:first_name,
			:last_name,
			:email,
			:password,
			NOW(),
			:hybridauth_provider_name,
			:ip,
			:status
			)
		');

		$stmt->bindValue(':first_name'              , $fname);
		$stmt->bindValue(':last_name'               , $lname);
		$stmt->bindValue(':email'                   , $email);
		$stmt->bindValue(':password'                , $password_hashed);
		$stmt->bindValue(':hybridauth_provider_name', 'local');
		$stmt->bindValue(':ip'                      , $ip);
		$stmt->bindValue(':status'                  , 'pending');

		// if query executed fine
		if($stmt->execute()) {
			$user_created = 1;
			$confirm_str = generatePassword(16);
			$user_id = $conn->lastInsertId();

			// insert confirmation string into db
			$stmt2 = $conn->prepare('
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

			$stmt2->bindValue(':user_id'    , $user_id);
			$stmt2->bindValue(':confirm_str', $confirm_str);

			if($stmt2->execute()) { // if insert into signup confirm table executed...
				// email user
				$query = "SELECT * FROM email_templates WHERE type = 'signup_confirm'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$email_subject = $row['subject'];
				$email_body    = $row['body'];

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
				if ($mailer->send($message)) {
					$mailer_problem = 0;
				}
				else {
					$mailer_problem = 1;
				}
			} // if($stmt2->execute())
		} // if($stmt->execute())
	} // end else user doesn't exist, so create entry in db
} // end if(!empty($full_name) && !empty($email) && !empty($password) && !$invalid_email)

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_sign-up.php');