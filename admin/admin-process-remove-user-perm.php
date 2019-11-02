<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$remove_user_id = (!empty($_POST['remove_user_id'])) ? $_POST['remove_user_id'] : 0;

if(!empty($remove_user_id)) {
	$query = "DELETE FROM users WHERE id = :remove_user_id AND status = 'trashed'";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':remove_user_id', $remove_user_id);
	$stmt->execute();
}