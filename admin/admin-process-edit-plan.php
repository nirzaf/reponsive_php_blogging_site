<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-edit-plan.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

$plan_id           = (!empty($params['plan_id']))           ? $params['plan_id']           : '';
$plan_type         = (!empty($params['plan_type']))         ? $params['plan_type']         : '';
$plan_name         = (!empty($params['plan_name']))         ? $params['plan_name']         : '';
$plan_description1 = (!empty($params['plan_description1'])) ? $params['plan_description1'] : '';
$plan_description2 = (!empty($params['plan_description2'])) ? $params['plan_description2'] : '';
$plan_description3 = (!empty($params['plan_description3'])) ? $params['plan_description3'] : '';
$plan_description4 = (!empty($params['plan_description4'])) ? $params['plan_description4'] : '';
$plan_description5 = (!empty($params['plan_description5'])) ? $params['plan_description5'] : '';
$plan_period       = (!empty($params['plan_period']))       ? $params['plan_period']       : 0;
$plan_order        = (!empty($params['plan_order']))        ? $params['plan_order']        : 0;
$plan_price        = (!empty($params['plan_price']))        ? $params['plan_price']        : 0;
$plan_status       = (!empty($params['plan_status']))       ? $params['plan_status']       : 0;

// trim
$plan_name         = trim($plan_name);
$plan_description1 = trim($plan_description1);
$plan_description2 = trim($plan_description2);
$plan_description3 = trim($plan_description3);
$plan_description4 = trim($plan_description4);
$plan_description5 = trim($plan_description5);
$plan_period       = trim($plan_period);
$plan_order        = trim($plan_order);
$plan_price        = trim($plan_price);

// check vars
if(empty($plan_id)) {
	echo "Undefined plan";
	die();
}

// plan period is 0 if plan type is monthly
if($plan_type == 'monthly' || $plan_type == 'monthly_feat' || $plan_type == 'annual' || $plan_type == 'annual_feat') {
	$plan_period = 0;
}

// check vars
if(empty($plan_name)) {
	echo "Plan name cannot be empty";
	die();
}

// check if plan type is valid
$valid_types = array('free', 'free_feat', 'one_time', 'one_time_feat', 'monthly', 'monthly_feat', 'annual', 'annual_feat');
if(!in_array($plan_type, $valid_types)) {
	echo "Wrong plan type";
	die();
}

// check if these variables are numeric
if(!is_numeric($plan_price) || !is_numeric($plan_period) || !is_numeric($plan_status) || !is_numeric($plan_order)) {
	echo "Wrong values for price, period, order or status";
	echo $plan_price;
	die();
}

// set price to 0 if plan types are free
if($plan_type == 'free' || $plan_type == 'free_feat') {
	$plan_price = 0;
}

$query = "UPDATE plans SET
	plan_type         = :plan_type,
	plan_name         = :plan_name,
	plan_description1 = :plan_description1,
	plan_description2 = :plan_description2,
	plan_description3 = :plan_description3,
	plan_description4 = :plan_description4,
	plan_description5 = :plan_description5,
	plan_period       = :plan_period,
	plan_price        = :plan_price,
	plan_order        = :plan_order,
	plan_status       = :plan_status
	WHERE plan_id     = :plan_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':plan_type', $plan_type);
$stmt->bindValue(':plan_name', $plan_name);
$stmt->bindValue(':plan_description1', $plan_description1);
$stmt->bindValue(':plan_description2', $plan_description2);
$stmt->bindValue(':plan_description3', $plan_description3);
$stmt->bindValue(':plan_description4', $plan_description4);
$stmt->bindValue(':plan_description5', $plan_description5);
$stmt->bindValue(':plan_period', $plan_period);
$stmt->bindValue(':plan_price', $plan_price);
$stmt->bindValue(':plan_order', $plan_order);
$stmt->bindValue(':plan_status', $plan_status);
$stmt->bindValue(':plan_id', $plan_id);

if($stmt->execute()) {
	echo $txt_plan_updated;
}