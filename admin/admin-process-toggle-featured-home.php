<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$place_id      = $_POST['place_id'];
$featured_home = $_POST['featured_home'];

if($featured_home == 'featured'){
	$query = "UPDATE places SET feat_home = 0 WHERE place_id= :place_id";
	$data = 'not_featured';
}
else {
	$query = "UPDATE places SET feat_home = 1 WHERE place_id= :place_id";
	$data = 'featured';
}

$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

echo $data;