 <?php
/**
 * Mercadopago listener
 * This file is a copy the file ipn-mercadopago.php located in the root folder
 * The old ipn-mercadopago.php located in the root folder is kept there for backwards compatibility
 *
 * since v.1.08
 */
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../vendor/swiftmailer/swiftmailer/lib/swift_required.php');

// initialize swiftmailer
$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
	->setUsername($smtp_user)
	->setPassword($smtp_pass);
$mailer = Swift_Mailer::newInstance($transport_smtp);

// get topic
$topic    = (isset($_GET['topic'])) ? $_GET['topic'] : '';
$topic_id = (isset($_GET['id']))    ? $_GET['id'] : '';

// init mercadopago
$mp = new MP($mercadopago_client_id, $mercadopago_client_secret);

// begin action
if($topic == 'payment') {
	$is_buynow = false;
	$is_claim = false;

	// get payment information
	$payment_info = $mp->get('/collections/notifications/' . $topic_id);
	$ipn_str             = json_encode($payment_info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	$merchant_order_info = array();
	$amount              = $payment_info['response']['collection']['total_paid_amount'];
	$txn_id              = $payment_info['response']['collection']['id'];

	if(!empty($payment_info['response']['collection']['merchant_order_id'])) {
		$merchant_order_info = $mp->get('/merchant_orders/' . $payment_info['response']['collection']['merchant_order_id']);
		$place_id = $merchant_order_info['response']['items'][0]['id'];
		$is_buynow = true;
		$txn_type = 'Basic Checkout';
	}

	// else it's a subscription, so item id can be found in index 'order_id' or 'external_reference' from payment information
	else {
		$place_id = $payment_info['response']['external_reference'];
		$is_buynow = false;
		$txn_type = 'Automatic Debit';

		// clean place id (remove "PLACE-" string from reference prepended in old versions of the script)
		$place_id = str_replace("PLACE-", "", $place_id);
	}

	// check if this is a claim listing operation and redefine place_id accordingly
	if(strpos($place_id, '-') !== false) {
		// string contains '-' so it's a claim listing operation
		$is_claim = true;
		$claim_arr = explode('-', $place_id);
		$claim_user_id = $claim_arr[0];
		$place_id = $claim_arr[1];
	}

	// vars
	$customer_email = $payment_info['response']['collection']['payer']['email'];
	$payment_status = $payment_info['response']['collection']['status'];

	// get place details and build link to place to use in emails
	$query = "SELECT p.place_name, p.userid, c.city_name
		FROM places p
		LEFT JOIN cities c ON p.city_id = c.city_id
		WHERE place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$place_name      = (!empty($row['place_name'])) ? $row['place_name'] : '';
	$city_name       = (!empty($row['city_name'] )) ? $row['city_name']  : '';
	$place_userid    = (!empty($row['userid'] ))    ? $row['userid']     : '';
	$place_city_slug = to_slug($city_name);
	$place_name_slug = to_slug($place_name);
	$place_link      = $baseurl . '/' . $place_city_slug . '/place/' . $place_id . '/' . $place_name_slug;

	// if this is claim listing, redefine $place_userid
	if($is_claim === true) {
		$place_userid = $claim_user_id;
	}

	// get plan details associated with this place
	$query = "SELECT plans.* FROM places
		RIGHT JOIN plans ON places.plan = plans.plan_id
		WHERE places.place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$plan_id     = $row['plan_id'];
	$plan_type   = (!empty($row['plan_type']  )) ? $row['plan_type']   : '';
	$plan_price  = (!empty($row['plan_price'] )) ? $row['plan_price']  : '0.00';
	$plan_period = (!empty($row['plan_period'])) ? $row['plan_period'] : '36500';

	// get user details
	$query = "SELECT email, first_name FROM users WHERE id = :place_userid";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_userid', $place_userid);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$place_user_email     = (!empty($row['email']     )) ? $row['email']      : '';
	$place_user_firstname = (!empty($row['first_name'])) ? $row['first_name'] : $customer_first_name;

	// if is buynow
	if($is_buynow) {
		// get email template
		$query = "SELECT * FROM email_templates WHERE type = 'web_accept'";

		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

		// replace template vars
		$email_body = str_replace('%username%', $place_user_firstname, $email_body);
		$email_body = str_replace('%place_link%', $place_link, $email_body);

		if(empty($place_user_email)) {
			$place_user_email = $customer_email;
		}

		// if payment approved
		if($payment_info['response']['collection']['status'] == 'approved') {
			// update paid column
			$query = 'UPDATE places SET
						paid = 1,
						userid = :userid,
						valid_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :valid_until DAY)
					WHERE place_id = :place_id';
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':place_id', $place_id);
			$stmt->bindValue(':valid_until', $plan_period);
			$stmt->bindValue(':userid', $place_userid);
			$stmt->execute();

			// send web_accept email
			$message = Swift_Message::newInstance()
				->setSubject($email_subject)
				->setFrom(array($admin_email => $site_name))
				->setTo($customer_email)
				->setBody($email_body)
				->setReplyTo($admin_email)
				->setReturnPath($admin_email)
				;

			$mailer->send($message);
		} // end if payment approved
	} // end if is buynow
} // end if topic == 'payment'

// else if topic == 'merchant_order'
elseif($topic == 'merchant_order') {
	$merchant_order_info = $mp->get('/merchant_orders/' . $topic_id);
	$ipn_str             = json_encode($merchant_order_info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	$place_id            = $merchant_order_info['response']['items']['id'];
	$customer_email      = $merchant_order_info['response']['payer']['email'];
	$txn_type            = 'Basic Checkout';
	$payment_status      = $merchant_order_info['response']['payments'][0]['status'];
	$amount              = $merchant_order_info['response']['payments']['total_paid_amount'];
	$txn_id              = $merchant_order_info['response']['id'];

	// check if this is a claim listing operation and redefine place_id accordingly
	if(strpos($place_id, '-') !== false) {
		// string contains '-' so it's a claim listing operation
		$is_claim = true;
		$claim_arr = explode('-', $place_id);
		$claim_user_id = $claim_arr[0];
		$place_id = $claim_arr[1];
	}
}

// else if topic == 'preapproval'
elseif($topic == 'preapproval') {
	$preapproval_info = $mp->get('/preapproval/' . $topic_id);

	if ($preapproval_info['status'] == 200) {
		// see statuses on https://www.mercadopago.com.br/developers/en/api-docs/recurring/ipn/recurring-status/
		/*
		pending: The subscription needs to be authorized by the user.
		authorized: The subscription is authorized to debit automatically from the user card.
		paused: No charges will be made until the subscription is reactivated.
		cancelled: The subscription is no longer active.
		*/

		$ipn_str        = json_encode($preapproval_info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$place_id       = $preapproval_info['response']['external_reference'];
		$customer_email = $merchant_order_info['response']['payer']['email'];
		$txn_type       = 'Automatic Debit';
		$payment_status = $preapproval_info['response']['status'];
		$amount         = $preapproval_info['response']['auto_recurring']['transaction_amount'];
		$txn_id         = $preapproval_info['response']['id'];

		// clean place id (remove "PLACE-" string from reference prepended in old versions of the script)
		$place_id = str_replace("PLACE-", "", $place_id);

		// check if this is a claim listing operation and redefine place_id accordingly
		if(strpos($place_id, '-') !== false) {
			// string contains '-' so it's a claim listing operation
			$is_claim = true;
			$claim_arr = explode('-', $place_id);
			$claim_user_id = $claim_arr[0];
			$place_id = $claim_arr[1];
		}

		// get place details and build link to place to use in emails
		$query = "SELECT p.place_name, p.userid, c.city_name
			FROM places p
			LEFT JOIN cities c ON p.city_id = c.city_id
			WHERE place_id = :place_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_id', $place_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$place_name      = (!empty($row['place_name'])) ? $row['place_name'] : '';
		$city_name       = (!empty($row['city_name'] )) ? $row['city_name']  : '';
		$place_userid    = (!empty($row['userid'] ))    ? $row['userid']     : '';
		$place_city_slug = to_slug($city_name);
		$place_name_slug = to_slug($place_name);
		$place_link      = $baseurl . '/' . $place_city_slug . '/place/' . $place_id . '/' . $place_name_slug;

		// get plan details associated with this place
		$query = "SELECT plans.* FROM places
			RIGHT JOIN plans ON places.plan = plans.plan_id
			WHERE places.place_id = :place_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_id', $place_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$plan_id     = $row['plan_id'];
		$plan_type   = (!empty($row['plan_type']  )) ? $row['plan_type']   : '';
		$plan_price  = (!empty($row['plan_price'] )) ? $row['plan_price']  : '0.00';
		$plan_period = (!empty($row['plan_period'])) ? $row['plan_period'] : '36500';

		// get user details

		// if this is claim listing, redefine $place_userid
		if($is_claim === true) {
			$place_userid = $claim_user_id;
		}

		$query = "SELECT email, first_name FROM users WHERE id = :place_userid";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_userid', $place_userid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$place_user_email     = (!empty($row['email']     )) ? $row['email']      : '';
		$place_user_firstname = (!empty($row['first_name'])) ? $row['first_name'] : $customer_first_name;

		// if subscription cancelled or paused
		if ($preapproval_info['response']['status'] == 'cancelled'
		|| $preapproval_info['response']['status'] == 'paused') {
			$query = 'UPDATE places SET paid = 0 WHERE place_id = :place_id';
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':place_id', $place_id);
			$stmt->execute();
		}

		// if subscription started
		if ($preapproval_info['response']['status'] == 'authorized') {
			// update place paid column
			$query = 'UPDATE places SET
						paid = 1,
						userid = :userid,
						valid_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :valid_until DAY)
					WHERE place_id = :place_id';
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':place_id', $place_id);
			$stmt->bindValue(':userid', $place_userid);
			$stmt->bindValue(':valid_until', $plan_period);
			$stmt->execute();

			// get email template
			$query = "SELECT * FROM email_templates WHERE type = 'subscr_signup'";

			$stmt = $conn->prepare($query);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
			$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

			// replace template vars
			$email_body = str_replace('%username%', $place_user_firstname, $email_body);
			$email_body = str_replace('%place_link%', $place_link, $email_body);

			if(empty($place_user_email)) {
				$place_user_email = $payment_info['response']['collection']['payer']['email'];
			}

			// send subscr_signup email
			$message = Swift_Message::newInstance()
				->setSubject($email_subject)
				->setFrom(array($admin_email => $site_name))
				->setTo($customer_email)
				->setBody($email_body)
				->setReplyTo($admin_email)
				->setReturnPath($admin_email)
				;

			$mailer->send($message);
		}
	}
} // end elseif topic == 'preapproval'

else {
	// topic not equal preapproval, payment or merchant_order

}
if(!empty($topic)) {
	// insert into transactions
	$query = 'INSERT INTO transactions(
		ipn_description,
		place_id,
		payer_email,
		txn_type,
		payment_status,
		amount,
		txn_id,
		ipn_response,
		ipn_vars
		)
	VALUES(
		:ipn_description,
		:place_id,
		:payer_email,
		:txn_type,
		:payment_status,
		:amount,
		:txn_id,
		:ipn_response,
		:ipn_vars
		)';

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':ipn_description' , 'Mercadopago IPN');
	$stmt->bindValue(':place_id'        , $place_id);
	$stmt->bindValue(':payer_email'     , $customer_email);
	$stmt->bindValue(':txn_type'        , $txn_type);
	$stmt->bindValue(':payment_status'  , $payment_status);
	$stmt->bindValue(':amount'          , $amount);
	$stmt->bindValue(':txn_id'          , $txn_id);
	$stmt->bindValue(':ipn_response'    , $topic);
	$stmt->bindValue(':ipn_vars'        , $ipn_str);
	$stmt->execute();
}