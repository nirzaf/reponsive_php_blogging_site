<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$review_id = $_POST['review_id'];
$status = $_POST['status'];

if($status == 'pending'){
	$query = "UPDATE reviews SET status = 'approved' WHERE review_id= :review_id";
	$status = $txt_approved; // from _trans-global.php
}
else {
	$query = "UPDATE reviews SET status = 'pending' WHERE review_id= :review_id";
	$status = $txt_pending; // from _trans-global.php
}

$stmt = $conn->prepare($query);
$stmt->bindValue(':review_id', $review_id);
$stmt->execute();

echo $status;