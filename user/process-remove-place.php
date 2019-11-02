<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// csrf check
require_once(__DIR__ . '/_user_inc_request_with_ajax.php');

$place_id = $_POST['place_id'];

// delete photos first, before cascading foreign key
$query = "SELECT dir, filename FROM photos WHERE place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$dir = $row['dir'];
	$filename = $row['filename'];

	$full_pic = $pic_basepath . '/' . $place_full_folder . '/' . $dir . '/' . $filename;
	$thumb_pic = $pic_basepath . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;

	if(is_file($full_pic)) {
		unlink($full_pic);
	}
	if(is_file($thumb_pic)) {
		unlink($thumb_pic);
	}
}

$stmt = $conn->prepare('SELECT * FROM places WHERE place_id = :place_id AND userid = :userid');
$stmt->bindValue(':place_id', $place_id);
$stmt->bindValue(':userid', $userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// check if user is owner of this place
if($row['userid'] == $userid) {
	$query = "UPDATE places SET status = 'trashed' WHERE place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();

	echo "Place deleted";
}
else {
	echo "Problem removing place";
}