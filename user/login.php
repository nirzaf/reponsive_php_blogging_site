<?php
require_once(__DIR__ . '/../inc/config.php');

$wrong_pass = 0;
$status     = 'approved';

// set referrer
$referrer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

// check if is unlogged user redirected from select plan page
$frags = '';

if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}
// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}

$frags   = explode("/", $frags);
/*
http://localhost/directoryapp/user/login/claim/2
Array(
    [0] =>
    [1] => claim
    [2] => 2
)

http://localhost/directoryapp/plugins/claim_listings/claim/7
*/

//print_r2($frags);

$from = (!empty($frags[1])) ? $frags[1] : '';
if(!empty($from) && $from == 'claim' && !empty($frags[2])) {
	$referrer = $baseurl . '/plugins/claim_listings/claim/' . $frags[2];
}

// if page requested by submitting login form
if(isset($_POST['email']) && isset($_POST['password'])) {
	$email    = $_POST['email'];
	$password = $_POST['password'];
	$referrer = $_POST['referrer'];

	// trim
	$email    = trim($email);
	$password = trim($password);
	$referrer = trim($referrer);

	$ignore_referrer = array('password-request', 'logoff', 'sign-up', 'login', 'password-reset', 'signup-confirm');

	foreach($ignore_referrer as $v) {
		$pos = strpos($referrer, $v);
		if($pos !== false) {
			$referrer = $baseurl;
		}
	}

	$stmt = $conn->prepare("SELECT id, password, status FROM users WHERE email = :email");
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$userid          = $row['id'];
	$password_hashed = $row['password'];
	$status          = $row['status'];

	// if email is not registered
	if(empty($row)) {
		header("Location: $baseurl/_msg.php?msg=email_not_registered");
	}

	if($status == 'approved') {
		// verify password
		if(password_verify($password, $password_hashed)) {
			// generate random token
			$token = bin2hex(openssl_random_pseudo_bytes(16));

			// set cookie, provider is current site
			// signature: setcookie(name, value, expire, path, domain, secure, httponly);
			$cookie_val = "$userid-localhost-$token";
			setcookie('loggedin', $cookie_val, time()+86400*30, $install_path, '', '', true);

			// record tokens in db
			record_tokens($userid, 'localhost', $token);

			// start session
			$_SESSION['user_connected'] = true;
			$_SESSION['userid'] = $userid;

			if(!empty($referrer)) {
				header("Location: $referrer");
			}
			else {
				header("Location: $baseurl");
			}
		}

		// wrong email or password?
		else {
			$wrong_pass = 1;
		}
	} // end if status approved
} // end if login form submit

// else, if login page request by clicking a provider button
if(isset($_GET['provider'])) {
	// vars
	$email_already_used = 0;

	// the selected provider
	$provider_name = $_GET['provider'];
	$referrer      = (isset($_GET['referrer'])) ? $_GET['referrer'] : '';

	// include HybridAuth library
	$config = __DIR__ . '/../vendor/hybridauth/hybridauth/hybridauth/config.php';

	try {
		// initialize Hybrid_Auth class with the config file
		$hybridauth = new Hybrid_Auth($config);

		// try to authenticate with the selected provider
		$adapter = $hybridauth->authenticate($provider_name);

		// then grab the user profile
		$user_profile = $adapter->getUserProfile();
	} catch( Exception $e ) {
		$exception_msg = $e->getMessage();
		header("Location: $baseurl/_msg.php?msg=hybrid_auth_problem&exception=$exception_msg");
	}

	$provider_user_id = $user_profile->identifier;
	$first_name       = (!empty($user_profile->firstName)) ? $user_profile->firstName : '';
	$last_name        = (!empty($user_profile->lastName )) ? $user_profile->lastName  : '';
	$email            = (isset($user_profile->email))      ? $user_profile->email : $provider_user_id;

	// check if social email + local provider exists
	$stmt = $conn->prepare("SELECT COUNT(*) AS total_rows FROM users WHERE email = :email AND hybridauth_provider_name = 'local'");
	$stmt->bindValue(':email', $email);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	if($count > 0) {
		$email_already_used = 1;
	}

	// if social email + local provider doesn't exist
	if($email_already_used == 0) {
		// check if social id + social provider exists
		$stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE hybridauth_provider_name = :provider_name AND hybridauth_provider_uid = :provider_user_id");
		$stmt->bindValue(':provider_name', $provider_name);
		$stmt->bindValue(':provider_user_id', $provider_user_id);
		$stmt->execute();
		$count = $stmt->fetchColumn();

		// if social id + social provider doesn't exist
		// we create a new entry on users table for him
		if($count < 1) {
			// generate a random password
			$password        = generatePassword();
			$password_hashed = password_hash($password, PASSWORD_BCRYPT);

			$stmt = $conn->prepare('
			INSERT INTO users(
				email,
				password,
				first_name,
				last_name,
				hybridauth_provider_name,
				hybridauth_provider_uid,
				created,
				status
				)
			VALUES(
				:email,
				:password,
				:first_name,
				:last_name,
				:hybridauth_provider_name,
				:hybridauth_provider_uid,
				NOW(),
				:status
				)
			');

			$stmt->bindValue(':email'                   , $email);
			$stmt->bindValue(':password'                , $password_hashed);
			$stmt->bindValue(':first_name'              , $first_name);
			$stmt->bindValue(':last_name'               , $last_name);
			$stmt->bindValue(':hybridauth_provider_name', $provider_name);
			$stmt->bindValue(':hybridauth_provider_uid' , $provider_user_id);
			$stmt->bindValue(':status'                  , 'approved');
			$stmt->execute();

			// get the id of the user that we've just created
			$stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND password = :password");
			$stmt->bindValue(':email', $email);
			$stmt->bindValue(':password', $password_hashed);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$userid = $row['id'];

			// save profile picture if exists
			// check if there is a picture
			$photo_url = (isset($user_profile->photoURL)) ? $user_profile->photoURL : '';

			// maybe save profile picture in future versions
		}

		// else user have already authenticated with this provider and is already registered. Get userid
		else {
			$stmt = $conn->prepare("SELECT id FROM users WHERE hybridauth_provider_name = :provider_name AND hybridauth_provider_uid = :provider_user_id");
			$stmt->bindValue(':provider_name', $provider_name);
			$stmt->bindValue(':provider_user_id', $provider_user_id);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$userid = $row['id'];
		}

		// create token
		$token = bin2hex(openssl_random_pseudo_bytes(16));

		// record tokens
		record_tokens($userid, $provider_name, $token);

		// set session vars
		$_SESSION['user_connected'] = true;
		$_SESSION['userid'] = $userid;

		// set cookie
		$cookie_val = "$userid-$provider_name-$token";
		setcookie('loggedin', $cookie_val, time()+86400*30, $install_path, '', '', true);

		// redirect
		$ignore_referrer = array('password-request', 'logoff', 'sign-up', 'login', 'user', 'admin');

		foreach($ignore_referrer as $v) {
			$pos = strpos($referrer, $v);
			if($pos !== false) {
				$referrer = $baseurl;
			}
		}

		if(!empty($referrer)) {
			header("Location: $referrer");
		}
		else {
			header("Location: $baseurl");
		}
	} // if($email_already_used == 0)
} // end login with provider

// if just loading the page, include the login box template
// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_login.php');