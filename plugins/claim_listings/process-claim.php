<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/translation.php');

if(empty($userid)) {
	$redir_url = $baseurl . '/user/login/claim';
	header("Location: $redir_url");
}

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
$plan_id  = $frags[2];

// check if place id is numeric
if(!is_numeric($place_id) || !is_numeric($plan_id)) {
	throw new Exception('Invalid query string');
}

// check plan id selection
if(empty($plan_id)) {
	throw new Exception('Invalid plan selection');
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

// only allow claiming if place_userid == 1, that is, created by admin
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

// get plan details
/* plan types
free
free_feat
one_time
one_time_feat
monthly
monthly_feat
*/
$query = "SELECT plan_type, plan_name, plan_period, plan_price, plan_status FROM plans WHERE plan_id = :plan_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':plan_id', $plan_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$plan_type   = (!empty($row['plan_type']))   ? $row['plan_type']   : '';
$plan_name   = (!empty($row['plan_name']))   ? $row['plan_name']   : '';
$plan_period = (!empty($row['plan_period'])) ? $row['plan_period'] : 0;
$plan_price  = (!empty($row['plan_price']))  ? $row['plan_price']  : 0;
$plan_status = (!empty($row['plan_status'])) ? $row['plan_status'] : '';

// if not a free plan
if($plan_type != 'free' && $plan_type != 'free_feat') {
	// if it's a monthly plan
	if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
		// init vars
		$cmd = "_xclick-subscriptions";
		$p3  = '';
		$t3  = '';
		$src = '';
		$srt = '';
		$a3 = $plan_price;

		if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
			$p3  = '1';
			$t3  = 'M';
			$src = '1';
			$srt = '52';
		}
	}

	// if it's a one time plan
	if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
		// init vars
		$cmd = "_xclick";
		$amount = $plan_price;
	}

	// bn (<Company>_<Service>_<Product>_<Country>)
	$bn = $paypal_bn . '_Subscribe_WPS_' . $default_country_code;
} // end if($plan_type != 'free' && $plan_type != 'free_feat')

// add monthly text if needed
if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
	$plan_price_display = $plan_price . '/' . $txt_month;
} else {
	$plan_price_display = $plan_price;
}

// paypal form vars
$claim_notify_url = $baseurl . '/plugins/claim_listings/ipn-handler.php';

/*--------------------------------------------------
stripe vars
--------------------------------------------------*/
// if stripe live mode
if($stripe_mode == 1) {
	$stripe_key = $stripe_live_publishable_key;
}
// else is stripe test mode
else {
	$stripe_key = $stripe_test_publishable_key;
}

$stripe_amount = str_replace('.', '', $plan_price);
$stripe_amount = str_replace(',', '', $stripe_amount);

/*--------------------------------------------------
mercadopago preference builder
--------------------------------------------------*/
if($mercadopago_mode != -1) {
	// init create payment preference
	$mp     = new MP($mercadopago_client_id, $mercadopago_client_secret);
	$amount = (!empty($plan_price)) ? $plan_price  : 0;

	if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
		// preference data buy now
		$preference_data = array(
			'items' => array(
				array(
					'id'          => "$userid-$place_id",
					'title'       => "$plan_name - $site_name",
					'quantity'    => 1,
					'currency_id' => $mercadopago_currency_id,
					'unit_price'  => (float)$amount
				)
			),
			'back_urls' => array(
				'success' => "$baseurl/user/thanks",
				'failure' => "$baseurl/user/thanks",
				'pending' => "$baseurl/user/thanks"
			),
			'notification_url' => $mercadopago_notification_url
		);

		$button_link = $mp->create_preference($preference_data);
	}

	if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
		// preapproval data (recurring)
		$preapproval_data = array(
			"payer_email" => $email,
			"back_url" => "$baseurl/user/thanks",
			"reason" => "$plan_name - $site_name",
			"external_reference" => "$userid-$place_id",
			"auto_recurring" => array(
				"frequency"          => 1,
				"frequency_type"     => "months",
				"transaction_amount" => (float)$plan_price,
				"currency_id"        => $mercadopago_currency_id
			)
		);

		$button_link = $mp->create_preapproval_payment($preapproval_data);
		$action      = 'subscribe';
	}
}

// global translation file
require_once($lang_folder . '/__trans-global.php');

// check if translation var exists (v.1.08b)
$txt_pay_with_paypal      = (!empty($txt_pay_with_paypal))      ? $txt_pay_with_paypal      : "Pay with Paypal";
$txt_pay_with_stripe      = (!empty($txt_pay_with_stripe))      ? $txt_pay_with_stripe      : "Pay with Stripe";
$txt_pay_with_mercadopago = (!empty($txt_pay_with_mercadopago)) ? $txt_pay_with_mercadopago : "Pagar com Mercadopago";

// template file
require_once(__DIR__ . '/tpl_process-claim.php');
