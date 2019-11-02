<?php
require_once(__DIR__ . '/../inc/config.php');

if(empty($userid)) {
	$redir_url = $baseurl . '/user/login/select-plan';
	header("Location: $redir_url");
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

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_select-plan.php');