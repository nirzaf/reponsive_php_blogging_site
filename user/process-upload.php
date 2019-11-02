<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/smart_resize_image.php');
require_once(__DIR__ . '/user_area_inc.php');

// check if user is logged in
// not checking this now
/*
if(empty($userid)) {
	echo "You must be logged in to upload.";
	die();
}
*/

// check submit token
$submit_token = $_SESSION['submit_token'];

if(empty($submit_token)) {
	echo "Submit token empty";
	die();
}

// proceed to upload routine
if($_FILES['userfile']['error'] != 0 || !exif_imagetype($_FILES['userfile']['tmp_name'])) {
	// echo "Error during file upload. Make sure the selected file is an image and it's smaller than ", ini_get('upload_max_filesize');
	echo $_FILES['userfile']['error']; // can be 1 - 8, see: http://php.net/manual/en/features.file-upload.errors.php
}
else {
	// uploaded image
	$uploaded_img = basename($_FILES['userfile']['name']);

	// get file extension
	$ext = pathinfo($uploaded_img, PATHINFO_EXTENSION);
	$ext = mb_strtolower($ext);

	// generate filename
	$filename = date('y.m.d.H.i') . "-" . microtime(true) . "-" . mt_rand(0, 99999999) . '.' . $ext;

	// paths
	$place_pic_tmp = $pic_basepath . '/' . $place_tmp_folder . '/' . $filename;

	// count how many pics this user has already uploaded
	$query = "SELECT COUNT(*) AS num_pics FROM tmp_photos WHERE submit_token = :submit_token";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':submit_token', $submit_token);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$num_pics = $row['num_pics'];

	if($num_pics < $max_pics + 50) {
		if(@move_uploaded_file($_FILES['userfile']['tmp_name'], $place_pic_tmp)) {
			if(!isset($global_pic_width)) {
				$global_pic_width = 948;
			}
			if(!isset($global_pic_height)) {
				$global_pic_height = 632;
			}
			smart_resize_image($place_pic_tmp, null, $global_pic_width, $global_pic_height, false, $place_pic_tmp, false, false, 85);

			// insert into tmp_photos
			$query = "INSERT INTO tmp_photos(submit_token, filename) VALUES(:submit_token, :filename)";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':submit_token', $submit_token);
			$stmt->bindValue(':filename', $filename);
			$stmt->execute();

			echo $filename;
		}
		else {
			// WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
			// Otherwise onSubmit event will not be fired
			// return value between 2 and 8
			echo "10"; // custom error code, failed to move file
		}
	}
	else {
		echo "12"; // custom error code, more than max num pics
	}
}