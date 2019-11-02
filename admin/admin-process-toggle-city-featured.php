<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$city_id     = (int)$_POST['city_id'];
$city_status = $_POST['city_status'];

if(is_int($city_id)) {
	if($city_status == 'not_featured'){
		$query  = "INSERT INTO cities_feat(city_id) VALUES(:city_id)";
		$status = 'featured';
	}
	else {
		$query  = "DELETE FROM cities_feat WHERE city_id = :city_id";
		$status = 'not_featured';
	}

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $city_id);
	$stmt->execute();

	echo $status;
}
else {
	echo "Wrong plan_id or plan_status.";
	echo gettype($plan_id);
}