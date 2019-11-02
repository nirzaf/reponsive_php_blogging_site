<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$operation = $_POST['operation'];

if($operation == 'delete') {
	$delete_id = isset($_POST['delete_id']) ? $_POST['delete_id'] : '';

	if(!empty($delete_id)) {
		// update users table
		$query = "UPDATE users SET profile_pic_status = 'none' WHERE id = :delete_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':delete_id', $delete_id);
		$stmt->execute();

		// remove file
		$folder = floor($delete_id / 1000) + 1;

		if(strlen($folder) < 1) {
			$folder = '999';
		}

		// path full pic
		$path_full = $pic_basepath . '/' . $profile_full_folder . '/' . $folder;

		// path thumb
		$path_thumb = $pic_basepath . '/' . $profile_thumb_folder . '/' . $folder;

		// first remove previous profile pictures
		$thumb = glob("$path_thumb/$delete_id.*");
		foreach($thumb as $v) {
			unlink($v);
		}
		$full = glob("$path_full/$delete_id.*");
		foreach($full as $v) {
			unlink($v);
		}
	}
}

if($operation == 'approve') {
	$approve_id = isset($_POST['approve_id']) ? $_POST['approve_id'] : '';

	if(!empty($approve_id)) {
		// update users table
		$query = "UPDATE users SET profile_pic_status = 'approved' WHERE id = :approve_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':approve_id', $approve_id);
		$stmt->execute();
	}
}
