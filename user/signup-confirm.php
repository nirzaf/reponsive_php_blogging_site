<?php
require_once(__DIR__ . '/../inc/config.php');

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
$frags = explode("/", $frags);
$frags = explode(',', $frags[1]);

if(empty($frags[1])) {
	$frags[1] = '';
}

// check to see if email already exists
$query = "SELECT COUNT(*) FROM signup_confirm WHERE user_id = :user_id AND confirm_str = :confirm_str";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $frags[0]);
$stmt->bindValue(':confirm_str', $frags[1]);
$stmt->execute();
$count = $stmt->fetchColumn();

// confirm string and user id exists in db?
$user_confirmed = 0;
if($count > 0) {
	$user_confirmed = 1;
}

// delete from signup confirm and change status to approved in users table
if(!empty($user_confirmed)) {
	try {
		$conn->beginTransaction();

		$query = "DELETE FROM signup_confirm WHERE user_id = :user_id AND confirm_str = :confirm_str";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':user_id', $frags[0]);
		$stmt->bindValue(':confirm_str', $frags[1]);
		$stmt->execute();

		$query = "UPDATE users SET status = :status WHERE id = :id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':id', $frags[0]);
		$stmt->bindValue(':status', 'approved');
		$stmt->execute();

		$conn->commit();
	} // end try block ()
	catch(PDOException $e) {
		$conn->rollBack();
	}
}

// maybe the user reloaded the confirmation page, in this case, row in signup_confirm table already deleted
$query = "SELECT COUNT(*) FROM users WHERE id = :user_id AND status = :status";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $frags[0]);
$stmt->bindValue(':status', 'approved');
$stmt->execute();
$count = $stmt->fetchColumn();
if($count > 0) {
	$user_confirmed = 1;
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_signup-confirm.php');