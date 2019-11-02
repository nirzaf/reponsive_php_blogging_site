<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/translation.php');

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

// get place id
$frags = explode("/", $frags);
$place_id = $frags[1];

// check if place id is numeric
if(!is_numeric($place_id)) {
	throw new Exception('Place id must be numeric');
}

// redirect if not logged in
if(empty($userid)) {
	$redir_url = $baseurl . '/user/login/claim/' . $place_id;
	header("Location: $redir_url");
}

// query db for place details
$query = "SELECT * FROM places WHERE place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$place_name   = (!empty($row['place_name']))   ? $row['place_name']   : '';
	$address      = (!empty($row['address']))      ? $row['address']      : '';
	$postal_code  = (!empty($row['postal_code']))  ? $row['postal_code']  : '';
	$cross_street = (!empty($row['cross_street'])) ? $row['cross_street'] : '';
	$neighborhood = (!empty($row['neighborhood'])) ? $row['neighborhood'] : 0;
	$city_id      = (!empty($row['city_id']))      ? $row['city_id']      : 0;
	$inside       = (!empty($row['inside']))       ? $row['inside']       : '';
	$area_code    = (!empty($row['area_code']))    ? $row['area_code']    : '';
	$phone        = (!empty($row['phone']))        ? $row['phone']        : '';
	$description  = (!empty($row['description']))  ? $row['description']  : '';
	$place_userid = (!empty($row['userid']))       ? $row['userid']       : '1';
}

// only allow claiming if place_userid == 1
if($place_userid != 1) {
	throw new Exception('This place has already been claimed');
}

// city and state details
$query = "SELECT
		c.city_name, c.slug AS city_slug,
		s.state_id, s.state_name, s.state_abbr, s.slug AS state_slug
		FROM cities c
		LEFT JOIN states s ON c.state_id = s.state_id
		WHERE city_id = :city_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':city_id', $city_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$city_name  = (!empty($row['city_name'] )) ? $row['city_name']  : '';
$city_slug  = (!empty($row['city_slug'] )) ? $row['city_slug']  : '';
$state_id   = (!empty($row['state_id']  )) ? $row['state_id']   : '';
$state_name = (!empty($row['state_name'])) ? $row['state_name'] : '';
$state_abbr = (!empty($row['state_abbr'])) ? $row['state_abbr'] : '';
$state_slug = (!empty($row['state_slug'])) ? $row['state_slug'] : '';

// get photo
$query = "SELECT * FROM photos WHERE place_id = :place_id ORDER BY photo_id DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$dir      = (!empty($row['dir']     )) ? $row['dir']      : '';
$filename = (!empty($row['filename'])) ? $row['filename'] : '';

if(!empty($filename) && !empty($dir)) {
	$photo_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;
}
else {
	$photo_url = $baseurl . '/imgs/blank.png';
}

// get plans
$query = "SELECT * FROM plans WHERE plan_status = 1 ORDER BY plan_order";
$stmt = $conn->prepare($query);
$stmt->execute();

$plans_arr = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$plan_id           = $row['plan_id'];
	$plan_type         = (!empty($row['plan_type']        )) ? $row['plan_type']         : '';
	$plan_name         = (!empty($row['plan_name']        )) ? $row['plan_name']         : '';
	$plan_period       = (!empty($row['plan_period']      )) ? $row['plan_period']       : 0;
	$plan_description1 = (!empty($row['plan_description1'])) ? $row['plan_description1'] : '';
	$plan_description2 = (!empty($row['plan_description2'])) ? $row['plan_description2'] : '';
	$plan_description3 = (!empty($row['plan_description3'])) ? $row['plan_description3'] : '';
	$plan_description4 = (!empty($row['plan_description4'])) ? $row['plan_description4'] : '';
	$plan_description5 = (!empty($row['plan_description5'])) ? $row['plan_description5'] : '';
	$plan_price        = (!empty($row['plan_price']       )) ? $row['plan_price']        : '0.00';

	// sanitize
	// ignored

	if($plan_type != 'free' && $plan_type != 'free_feat') {
		// prepare vars
		if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
			$plan_price = $plan_price . '/' . $txt_month;
		}

		$cur_loop_arr = array(
			'plan_id'           => $plan_id,
			'plan_type'         => $plan_type,
			'plan_name'         => $plan_name,
			'plan_period'       => $plan_period,
			'plan_description1' => $plan_description1,
			'plan_description2' => $plan_description2,
			'plan_description3' => $plan_description3,
			'plan_description4' => $plan_description4,
			'plan_description5' => $plan_description5,
			'plan_price'        => $plan_price
		);

		$plans_arr[] = $cur_loop_arr;
	}
}

// template file
require_once(__DIR__ . '/tpl_claim.php');
