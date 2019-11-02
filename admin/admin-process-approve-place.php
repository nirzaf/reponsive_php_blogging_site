<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$place_id = $_POST['place_id'];
$status = $_POST['status'];

if($status == 'pending'){
	$query = "UPDATE places SET status = 'approved' WHERE place_id= :place_id";
	$status = 'approved';
}
else {
	$query = "UPDATE places SET status = 'pending' WHERE place_id= :place_id";
	$status = 'pending';
}

$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

echo $status;