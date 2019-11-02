<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// path info
$frags = '';
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}

// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}

$frags = explode("/", $frags);
$place_id = !empty($frags[1]) ? $frags[1] : 0;

if(empty($place_id)) {
	throw new Exception('Place id cannot be empty');
}

$query = "SELECT * FROM places
	WHERE place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$place_userid        = $row['userid'];
$lat                 = $row['lat'];
$lng                 = $row['lng'];
$place_name          = $row['place_name'];
$address             = $row['address'];
$postal_code         = $row['postal_code'];
$cross_street        = $row['cross_street'];
$neighborhood        = $row['neighborhood'];
$city_id             = $row['city_id'];
$state_id            = $row['state_id'];
$inside              = $row['inside'];
$area_code           = $row['area_code'];
$phone               = $row['phone'];
$twitter             = $row['twitter'];
$facebook            = $row['facebook'];
$foursq_id           = $row['foursq_id'];
$website             = $row['website'];
$description         = $row['description'];
$business_hours_info = $row['business_hours_info'];
$submission_date     = $row['submission_date'];
$status              = $row['status'];

// check if user owns this place
if($place_userid != $userid) {
	// logged in userid is different from this place's userid
	// maybe it's an admin
	if(!$is_admin) {
		die('no permission to edit ' . $place_name);
	}
}

// filter vars, sanitize
$place_name          = filter_var($place_name         , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$address             = filter_var($address            , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$postal_code         = filter_var($postal_code        , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$cross_street        = filter_var($cross_street       , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$neighborhood        = filter_var($neighborhood       , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$inside              = filter_var($inside             , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$phone               = filter_var($phone              , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$twitter             = filter_var($twitter            , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$facebook            = filter_var($facebook           , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$foursq_id           = filter_var($foursq_id          , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$website             = filter_var($website            , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$description         = filter_var($description        , FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);
$business_hours_info = filter_var($business_hours_info, FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

// get city details
$query = "SELECT * FROM cities WHERE city_id = :city_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':city_id', $city_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$city_name = $row['city_name'];
$state_abbr = $row['state'];

// get neighborhood details
$query = "SELECT * FROM neighborhoods WHERE neighborhood_id = :neighborhood_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':neighborhood_id', $neighborhood);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$neighborhood_slug = $row['neighborhood_slug'];
$neighborhood_name = $row['neighborhood_name'];

// get cat details
$query = "SELECT * FROM rel_place_cat INNER JOIN cats ON rel_place_cat.cat_id = cats.id WHERE place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$place_cats = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$cur_loop_arr = array(
		'cat_id' => $row['cat_id'],
		'name' => $row['name']
	);
	$place_cats[] = $cur_loop_arr;
}

$cat_id   = (!empty($place_cats[0]['cat_id'])) ? $place_cats[0]['cat_id'] : 0;
$cat_name = (!empty($place_cats[0]['name']  )) ? $place_cats[0]['name']   : '';

/*--------------------------------------------------
business hours
--------------------------------------------------*/
$query = "SELECT * FROM business_hours WHERE place_id = :place_id ORDER BY day";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$business_hours = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$day     = $row['day'];
	$day_int = $row['day'];
	$open    = $row['open'];
	$close   = $row['close'];

	switch($day) {
		// $week_* vars are pulled from __trans_global.php file
		case 0:
			$day = $txt_week_mon;
			break;
		case 1:
			$day = $txt_week_tue;
			break;
		case 2:
			$day = $txt_week_wed;
			break;
		case 3:
			$day = $txt_week_thu;
			break;
		case 4:
			$day = $txt_week_fri;
			break;
		case 5:
			$day = $txt_week_sat;
			break;
		case 6:
			$day = $txt_week_sun;
			break;
	}

	$display_open = mb_substr($open, 0, 2) . ':' . mb_substr($open, 2, 2);
	$display_close = mb_substr($close, 0, 2) . ':' . mb_substr($close, 2, 2);

	$cur_loop_arr = array(
		'day'           => $day,
		'day_int'       => $day_int,
		'open'          => $open,
		'display_open'  => $display_open,
		'close'         => $close,
		'display_close' => $display_close
	);
	$business_hours[] = $cur_loop_arr;
}

/*--------------------------------------------------
photos
--------------------------------------------------*/
$query = "SELECT * FROM photos WHERE place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$place_photos = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$place_photos[] = array(
		'photo_id' => $row['photo_id'],
		'dir' => $row['dir'],
		'filename' => $row['filename']
	);
}

/*--------------------------------------------------
translation replacements
--------------------------------------------------*/
$txt_sub_header = str_replace('%place_name%', $place_name, $txt_sub_header);

/*--------------------------------------------------
session to prevent multiple form submissions
--------------------------------------------------*/
$submit_token = uniqid('', true);
$_SESSION['submit_token'] = $submit_token;

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_edit-place.php');