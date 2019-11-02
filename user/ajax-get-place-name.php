<?php
require_once(__DIR__ . '/../inc/config.php');

$place_id = $_POST['place_id'];

$stmt = $conn->prepare('SELECT * FROM places WHERE place_id = :place_id');
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

// check if user is owner of this place
if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	echo $row['place_name'];
}
else {
	echo "Problem retrieving information $place_id ";
}