<?php
require_once(__DIR__ . '/inc/config.php');

$action = (!empty($_GET['msg'])) ? $_GET['msg'] : '';

if($action == 'edited') {
	$txt_main_title = $txt_main_title_1;
	$txt_msg        = $txt_msg_1;
}
if($action == 'posted') {
	$txt_main_title = $txt_main_title_2;
	$txt_msg        = $txt_msg_2;
}
if($action == 'wrongpass') {
	$txt_main_title = $txt_main_title_3;
	$txt_msg        = $txt_msg_3;
}
if($action == 'contact_submitted') {
	$txt_main_title = $txt_main_title_4;
	$txt_msg        = $txt_msg_4;
}
if($action == 'hybrid_auth_problem') {
	$txt_main_title = $txt_main_title_5;
	$txt_msg        = $txt_msg_5;
}
if($action == 'place_submitted') {
	$txt_main_title = $txt_main_title_6;
	$txt_msg        = $txt_msg_6;
}
if($action == 'not_admin') {
	$txt_main_title = $txt_main_title_7;
	$txt_msg        = $txt_msg_7;
}
if($action == 'email_exists') {
	// check if translation vars exist, if not, set default
	$txt_main_title = (!empty($txt_main_title_8)) ? $txt_main_title_8 : 'Invalid email';
	$txt_msg        = (!empty($txt_msg_8       )) ? $txt_msg_8        : 'Sorry, the email you are trying to use is already registered.';
}
if($action == 'email_not_registered') {
	// check if translation vars exist, if not, set default
	$txt_main_title = (!empty($txt_main_title_9)) ? $txt_main_title_9 : 'Invalid email';
	$txt_msg        = (!empty($txt_msg_9       )) ? $txt_msg_9        : 'Sorry, the email you are trying to use is not registered.';
}

/*--------------------------------------------------
If it's a POST
--------------------------------------------------*/
if(!empty($_POST)) {
	$ref = (!empty($_POST['ref'])) ? $_POST['ref'] : '';

	// if POST from STRIPE form
	if ($ref == 'stripe') {
		// form post vars (from process-add-place and process-claim.php
		$plan_type = (!empty($_POST['plan_type'])) ? $_POST['plan_type'] : '';
		$plan_id   = (!empty($_POST['plan_id']))   ? $_POST['plan_id']   : '';
		$place_id  = (!empty($_POST['place_id']))  ? $_POST['place_id']  : '';

		// in case it's from claim listing form
		$payer_id  = (!empty($_POST['payer_id']))  ? $_POST['payer_id']  : '';

		// error
		$stripe_errors = array();

		// Token is created using Stripe.js or Checkout!
		// Get the payment token submitted by the form:
		$token       = $_POST['stripeToken'];
		$stripeEmail = $_POST['stripeEmail'];

		// set api key
		// if stripe live mode
		if($stripe_mode == 1) {
			$stripe_key = $stripe_live_secret_key;
		}
		// else is stripe test mode
		else {
			$stripe_key = $stripe_test_secret_key;
		}
		\Stripe\Stripe::setApiKey($stripe_key);

		// get plan details
		/* plan types
		free
		free_feat
		one_time
		one_time_feat
		monthly
		monthly_feat
		annual
		annual_feat
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

		// prepare vars
		$stripe_currency = strtolower($stripe_data_currency);
		$stripe_amount = str_replace('.', '', $plan_price);
		$stripe_amount = str_replace(',', '', $stripe_amount);

		// create customer
		try {
			$customer = \Stripe\Customer::create(array(
				'email'  => $stripeEmail,
				'source' => $token
			));
		} catch (Exception $e) {
			$stripe_errors[] = $e->getMessage();
		}

		if(isset($customer)) {
			// if it's one time purchase
			if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
				// Charge the Customer instead of the card:
				try {
					$charge = \Stripe\Charge::create(array(
						"amount"      => $stripe_amount,
						"currency"    => $stripe_currency,
						"description" => $plan_name,
						"customer"    => $customer->id,
						"metadata"    => array(
							"plan_type" => $plan_type,
							"plan_id"   => $plan_id,
							"place_id"  => $place_id,
							"payer_id"  => $payer_id
						)
					));
				} catch (Exception $e) {
					$stripe_errors[] = $e->getMessage();
				}
			}

			// else it's a subscription
			else {
				// other subscription vars
				$interval = 'month';

				// check if it's annual
				if($plan_type == 'annual' || $plan_type == 'annual_feat') {
					$interval = 'year';
				}

				// create plan on Stripe
				if(
					(!empty($plan_name)) &&
					(!empty($plan_id)) &&
					(!empty($interval)) &&
					(!empty($stripe_currency)) &&
					(!empty($stripe_amount))
					) {

					try {
						$plan = \Stripe\Plan::create(array(
							"name"     => $plan_name,
							"id"       => $plan_id,
							"interval" => $interval,
							"currency" => $stripe_currency,
							"amount"   => $stripe_amount
						));
					} catch (Exception $e) {
						// don't add to $stripe_errors array because error is expected if plan already exists
						error_log($e->getMessage());
					}
				} else {
					throw new Exception('Plan data missing');
				} // end create plan

				// subscribe customer to plan
				try {
					$subscription = \Stripe\Subscription::create(array(
						'customer' => $customer->id,
						'plan' => $plan_id,
						'metadata' => array(
							'plan_type' => $plan_type,
							'plan_id'   => $plan_id,
							'place_id'  => $place_id,
							'payer_id'  => $payer_id
						)
					));
				} catch (Exception $e) {
					$stripe_errors[] = $e->getMessage();
				}
			}
		} // end if isset customer

		// output result
		if(empty($stripe_errors)) {
			// check if translation vars exist, if not, set default
			$txt_main_title = (!empty($txt_main_title_stripe)) ? $txt_main_title_stripe : 'Thank you for your payment';
			$txt_msg        = (!empty($txt_msg_stripe       )) ? $txt_msg_stripe        : 'Payment successful. Thank you for your business!';
		} else {
			$txt_main_title = 'Error: Stripe could not complete transaction';
			$txt_msg        = '<ul>';
			foreach($stripe_errors as $v) {
				$txt_msg .= '<li>' . $v . '</li>';
			}
			$txt_msg       .= '</ul>';
		}

	} // end STRIPE
} // end if POST

// template file
require_once(__DIR__ . '/templates/tpl_msg.php');