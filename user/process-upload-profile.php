<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/smart_resize_image.php');
//require_once(__DIR__ . '/user_area_inc.php');

// only allow access to this file for logged in users
if(!array_key_exists('userid', $_SESSION) && empty($_SESSION['userid'])) {
    die('You do not have permission to access this page');
}

// user details
$userid = $_SESSION['userid'];

// max size
$upload_max_filesize = ini_get('upload_max_filesize');

if($_FILES['userfile']['error'] != 0 || !exif_imagetype($_FILES['userfile']['tmp_name'])) {
	echo "error";
}
else {
	// basename - Returns trailing name component of path
	$uploaded_img = basename($_FILES['userfile']['name']);

	// get file extension
	$extension = pathinfo($uploaded_img, PATHINFO_EXTENSION);
	$extension = mb_strtolower($extension);

	// define upload folder
	$folder = floor($userid / 1000) + 1;

	if(strlen($folder) < 1) {
		$folder = '999';
	}

	// paths
	$filename = $userid . '.' . $extension;
	$path_tmp = $pic_basepath . '/' . $profile_tmp_folder . '/' . $filename;

	// path full pic
	$path_full = $pic_basepath . '/' . $profile_full_folder . '/' . $folder;
	if (!is_dir($path_full)) {
		mkdir($path_full, 0777, true);
	}
	$dst_img_path_full = $path_full . '/' . $userid . '.' . $extension;

	// path thumb
	$path_thumb = $pic_basepath . '/' . $profile_thumb_folder . '/' . $folder;
	if (!is_dir($path_thumb)) {
		mkdir($path_thumb, 0777, true);
	}
	$dst_img_path_thumb = $path_thumb . '/' . $userid . '.' . $extension;

	// move uploaded
	if(@move_uploaded_file($_FILES['userfile']['tmp_name'], $path_tmp)) {
		// update table
		if($is_admin) {
			$stmt = $conn->prepare("UPDATE users SET profile_pic_status = 'approved' WHERE id = :userid");
		}
		else {
			$stmt = $conn->prepare("UPDATE users SET profile_pic_status = 'pending' WHERE id = :userid");
		}
		$stmt->bindValue(':userid', $userid);
		$stmt->execute();

		// first remove previous profile pictures
		$thumb = glob("$path_thumb/$userid.*");
		foreach($thumb as $v) {
			unlink($v);
		}
		$full = glob("$path_full/$userid.*");
		foreach($full as $v) {
			unlink($v);
		}

		// upload
		smart_resize_image($path_tmp, null, 720, 540, false, $dst_img_path_full, false, false, 85);
		smart_resize_image($path_tmp, null, 360, 360, false, $dst_img_path_thumb, false, false, 85);

		echo $filename;

		// unlink
		unlink($path_tmp);
	}
	else {
		echo "error";
	}
}
