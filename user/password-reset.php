<?php
require_once(__DIR__ . '/../inc/config.php');

// init vars
$form_submitted = 0;
$valid_token    = 0;
$update_success = 0;

// check if form submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$form_submitted = 1;
}

// if form submitted
if($form_submitted) {
	$new_pass = $_POST['new_pass'];
	$user_id  = $_POST['user_id'];
	$token    = $_POST['token'];

	// trim
	$new_pass = trim($new_pass);
	$token    = trim($token);

	// check token
	$stmt = $conn->prepare("SELECT COUNT(*) FROM pass_request WHERE user_id = :user_id AND token = :token");
	$stmt->bindValue(':user_id', $user_id);
	$stmt->bindValue(':token', $token);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	// token exists?
	if($count > 0) {
		$valid_token = 1;
	}

	// if token confirmed, update password
	if($valid_token) {
		$new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
		$query = "UPDATE users SET password = :new_pass WHERE id = :id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':new_pass', $new_pass);
		$stmt->bindValue(':id', $user_id);
		if($stmt->execute()) {
			// delete confirmation token from dba_close
			$query = "DELETE FROM pass_request WHERE user_id = :user_id";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':user_id', $user_id);
			$stmt->execute();
			$update_success = 1;
		}
	} // end valid token
}

// else form not submitted, show form
else {
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
	$frags   = explode(',', $frags[1]);
	$user_id = $frags[0];
	$token   = $frags[1];

	// check to see if credentials exist
	$stmt = $conn->prepare("SELECT COUNT(*) FROM pass_request WHERE user_id = :user_id AND token = :token");
	$stmt->bindValue(':user_id', $user_id);
	$stmt->bindValue(':token', $token);
	$stmt->execute();
	$count = $stmt->fetchColumn();

	// user match?
	if($count > 0) {
		$valid_token = 1;
	}
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_password-reset.php');