<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$plan_id     = (int)$_POST['plan_id'];
$plan_status = $_POST['plan_status'];

if(is_int($plan_id)) {
	if($plan_status == 'inactive'){
		$query  = "UPDATE plans SET plan_status = 1 WHERE plan_id= :plan_id";
		$status = $txt_active;
	}
	else {
		$query  = "UPDATE plans SET plan_status = 0 WHERE plan_id= :plan_id";
		$status = $txt_inactive;
	}

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':plan_id', $plan_id);
	$stmt->execute();

	echo $status;
}
else {
	echo "Wrong plan_id or plan_status.";
	echo gettype($plan_id);
}