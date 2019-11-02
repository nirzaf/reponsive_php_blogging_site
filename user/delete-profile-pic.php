<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// csrf check
require_once(__DIR__ . '/_user_inc_request_with_ajax.php');

// update table
$stmt = $conn->prepare("UPDATE users SET profile_pic_status = 'none' WHERE id = :userid");
$stmt->bindValue(':userid', $userid);
$stmt->execute();

$folder = floor($userid / 1000) + 1;

if(strlen($folder) < 1) {
	$folder = '999';
}

// path full pic
$path_full = $pic_basepath . '/' . $profile_full_folder . '/' . $folder;

// path thumb
$path_thumb = $pic_basepath . '/' . $profile_thumb_folder . '/' . $folder;

// first remove previous profile pictures
$thumb = glob("$path_thumb/$userid.*");
foreach($thumb as $v) {
	unlink($v);
}
$full = glob("$path_full/$userid.*");
foreach($full as $v) {
	unlink($v);
}