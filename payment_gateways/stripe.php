<?php
/**
 * Stripe webhook listener
 * The URL to this file should be defined in your Stripe Dashboard
 *
 * since v.1.08
 */

/**
GENERAL LAYOUT

if(!$is_subscription) {
	if($event['type'] == 'charge.succeeded') {
		...
	}
	else if($event['type'] == "charge.failed" || $event['type'] == "charge.refunded") {
		...
	}
	else {
		...
	}
}
else {
	if($event['type'] == 'customer.subscription.created') {
		...
	}
	else if($event['type'] == 'charge.succeeded') {
		...
	}
	else if($event['type'] == 'invoice.payment_succeeded') {
		...
	}
	else if($event['type'] == 'invoice.payment_failed') {
		...
	}
	else if($event['type'] == 'customer.subscription.deleted') {
		...
	}
	else {
		...
	}
}
*/

// includes
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../vendor/swiftmailer/swiftmailer/lib/swift_required.php');

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

// initialize swiftmailer
$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
	->setUsername($smtp_user)
	->setPassword($smtp_pass);

$mailer = Swift_Mailer::newInstance($transport_smtp);

// Retrieve the request's body and parse it as JSON
$input = @file_get_contents("php://input");

// extract json str into assoc array
//$event_json = json_decode($input); --> object
$event_arr = json_decode($input, true); // --> array

// Check against Stripe to confirm that the ID is valid
if(!empty($event_arr['id'])) {
	$event_obj = \Stripe\Event::retrieve($event_arr['id']);

	// convert $event into assoc array
	$event = $event_obj->__toArray(true);
}

// check if it's proper event
if(!isset($event)) {
	die();
}

// event types
$events = array('charge.succeeded', 'charge.failed', 'customer.subscription.created', 'customer.subscription.deleted', 'invoice.payment_succeeded', 'invoice.payment_failed');

// act only on the event types above
if(!in_array($event['type'], $events)) {
	die();
}

// check if is subscription or not
$is_subscription = null;

if($event['type'] == 'charge.succeeded') {
	if(empty($event['data']['object']['invoice'])) {
		$is_subscription = false;
	} else {
		$is_subscription = true;
	}
}

if( $event['type'] == 'customer.subscription.created' ||
	$event['type'] == 'customer.subscription.deleted' ||
	$event['type'] == 'invoice.payment_succeeded'
	) {
	$is_subscription = true;
}

if($event['type'] == 'charge.failed') {
	// if charge failed, it could be either subscription or buy now
	// but if metadata is empty, it means it's subscription, if contains metadata, it's buy now
	if(!empty($event['data']['object']['metadata'])) {
		$is_subscription = false;
	} else {
		$is_subscription = true;
	}
}

// metadata
// for 'buy now', metadata data is sent with \Stripe\Charge::create and received in webhook of type 'charge.succeeded'
// for 'subscriptions', metadata data is sent with \Stripe\Subscription::create and received in webhook type 'customer.subscription.created' and 'customer.subscription.deleted', also in 'invoice.payment_succeeded', 'invoice.created'
$plan_type = 'undefined plan';
$plan_id   = 0;
$place_id  = 0;
$payer_id  = ''; // in case it's from the claim listings form

if($event['type'] == 'charge.succeeded' || $event['type'] == 'charge.failed') {
	if(!$is_subscription) {
		$plan_type = (!empty($event['data']['object']['metadata']['plan_type'])) ? $event['data']['object']['metadata']['plan_type'] : 'undefined plan';
		$plan_id   = (!empty($event['data']['object']['metadata']['plan_id']  )) ? $event['data']['object']['metadata']['plan_id']   : 0;
		$place_id  = (!empty($event['data']['object']['metadata']['place_id'] )) ? $event['data']['object']['metadata']['place_id']  : 0;
		$payer_id  = (!empty($event['data']['object']['metadata']['payer_id'] )) ? $event['data']['object']['metadata']['payer_id']  : 0;
	}
}

if($event['type'] == 'customer.subscription.created' || $event['type'] == 'customer.subscription.deleted') {
	$plan_type = (!empty($event['data']['object']['metadata']['plan_type'])) ? $event['data']['object']['metadata']['plan_type'] : 'undefined plan';
	$plan_id   = (!empty($event['data']['object']['metadata']['plan_id']  )) ? $event['data']['object']['metadata']['plan_id']   : 0;
	$place_id  = (!empty($event['data']['object']['metadata']['place_id'] )) ? $event['data']['object']['metadata']['place_id']  : 0;
	$payer_id  = (!empty($event['data']['object']['metadata']['payer_id'] )) ? $event['data']['object']['metadata']['payer_id']  : 0;
}

// subscr_id
if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
	$subscr_id = 'undefined';
} else {
	$subscr_id = (!empty($event['data']['object']['id'])) ? $event['data']['object']['id'] : 'empty';
}

// customer info
$customer_obj = \Stripe\Customer::retrieve($event['data']['object']['customer']);
$customer = $customer_obj->__toArray(true);
$customer_email = $customer['email'];
$customer_id = $customer['id'];

// transaction table vars
$ipn_description = (!empty($event['data']['object']['object'])) ? $event['data']['object']['object'] : 'undefined';
// $place_id     = ''; defined above
$payer_email     = (!empty($customer['email']))                 ? $customer['email']                 : 'undefined';
$txn_type        = (!empty($event['type']))                     ? $event['type']                     : 'undefined';
$payment_status  = (!empty($event['data']['object']['status'])) ? $event['data']['object']['status'] : 'undefined';
$amount          = (!empty($event['data']['object']['amount'])) ? $event['data']['object']['amount'] : 'undefined';
$txn_id          = (!empty($event['id']))                       ? $event['id']                       : 'undefined';
$parent_txn_id   = '';
$subscr_id       = 'undefined'; // defined below
$ipn_response    = (!empty($input))                             ? $input                             : 'undefined'; '';
$ipn_vars        =  'undefined';
$txn_date        = (!empty($event['created']))                  ? $event['created']                  : 'undefined'; '';

/*--------------------------------------------------
Vars
--------------------------------------------------*/
$debug_msg = "
is_subscription = $is_subscription
ipn_description = $ipn_description
plan_id         = $plan_id
place_id        = $place_id
plan_type       = $plan_type
payer_email     = $payer_email
txn_type        = $txn_type
payment_status  = $payment_status
amount          = $amount
txn_id          = $txn_id
parent_txn_id   = $parent_txn_id
subscr_id       = $subscr_id
ipn_vars        = $ipn_vars
txn_date        = $txn_date
customer_id     = $customer_id;
ipn_response    = $ipn_response
";

/*--------------------------------------------------
HANDLE EVENT
--------------------------------------------------*/
// init vars
$approve = false;

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

// if buy now
if(!$is_subscription) {
	if($event['type'] == 'charge.succeeded') {
		// define email informing user of charge.succeeded
		$query = "SELECT * FROM email_templates WHERE type = 'web_accept'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

		// set approve to true
		$approve = true;
	}
	else if($event['type'] == 'charge.failed') {
		// define email informing user of charge.failed
		$query = "SELECT * FROM email_templates WHERE type = 'web_accept_fail'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';
	}
	else if($event['type'] == 'charge.refunded') {
		// define email informing user of charge.failed
		$query = "SELECT * FROM email_templates WHERE type = 'web_accept_fail'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';
	}
	else {
		// log transaction
		error_log("Webhook data is a one-off purchase but charge neither succeeded nor failed/refunded");
	}
}
// else is subscription
else {
	// webhook types to act on:
		// 'customer.subscription.created'
		// 'customer.subscription.deleted'
		// 'invoice.payment_succeeded'
		// 'invoice.payment_failed'
	// if subscription succeeded
	if($event['type'] == 'customer.subscription.created') {
		// define email informing user of subscr_signup success
		$query = "SELECT * FROM email_templates WHERE type = 'subscr_signup'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

		// set approve to true
		$approve = true;
	}
	else if($event['type'] == 'charge.succeeded') {
		// log transaction
	}
	else if($event['type'] == 'invoice.payment_succeeded') {
		// log transaction
	}
	else if($event['type'] == 'invoice.payment_failed') {
		// define email informing user of invoice.payment_failed
		$query = "SELECT * FROM email_templates WHERE type = 'subscr_failed'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';
	}
	else if($event['type'] == 'customer.subscription.deleted') {
		// update places, set paid to 0
		$query = 'UPDATE places SET paid = 0 WHERE place_id = :place_id';
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_id', $place_id);
		$stmt->execute();

		// email user informing subscription ended
		$query = "SELECT * FROM email_templates WHERE type = 'subscr_eot'";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
		$email_body    = (!empty($row['body']   )) ? $row['body']    : '';
	}
	else {
		// log transaction
	}
}

// if approve
if($approve) {
	try {
		$conn->beginTransaction();

		// update paid column in places table
		$query = 'UPDATE places SET paid = 1 WHERE place_id = :place_id';
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_id', $place_id);
		$stmt->execute();

		// check if it's a claim listing
		if(!empty($payer_id)) {
			// update paid column in places table
			$query = 'UPDATE places SET userid = :userid WHERE place_id = :place_id';
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':place_id', $place_id);
			$stmt->bindValue(':userid', $payer_id);
			$stmt->execute();
		}

		$conn->commit();
	} catch(PDOException $e) {
		$conn->rollBack();
	}
}

/*--------------------------------------------------
SEND EMAIL TO USER
--------------------------------------------------*/
// get user details
$query = "SELECT email, first_name FROM users WHERE id = :place_userid";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_userid', $place_userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$place_user_email     = (!empty($row['email']     )) ? $row['email']      : $payer_email;
$place_user_firstname = (!empty($row['first_name'])) ? $row['first_name'] : $first_name;

if($event['type'] == 'charge.succeeded'
|| $event['type'] == 'charge.failed'
|| $event['type'] == 'charge.refunded'
|| $event['type'] == 'customer.subscription.created'
|| $event['type'] == 'invoice.payment_failed'
|| $event['type'] == 'customer.subscription.deleted') {
	if(!empty($email_body)) {
		// substitutions
		$email_body = str_replace('%username%'  , $place_user_firstname, $email_body);
		$email_body = str_replace('%place_link%', $place_link, $email_body);

		// send
		$message = Swift_Message::newInstance()
			->setSubject($email_subject)
			->setFrom(array($admin_email => $site_name))
			->setTo($place_user_email)
			->setBody($email_body)
			->setReplyTo($admin_email)
			->setReturnPath($admin_email)
			;

		$mailer->send($message);
	}
}

/*--------------------------------------------------
SEND EMAIL TO ADMIN
--------------------------------------------------*/

/*--------------------------------------------------
INSERT INTO TRANSACTIONS TABLE
--------------------------------------------------*/
// convert amount to decimal
$amount = $amount / 100;

$query = "INSERT INTO transactions(
		ipn_description,
		place_id,
		payer_email,
		txn_type,
		payment_status,
		amount,
		txn_id,
		parent_txn_id,
		subscr_id,
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
		:parent_txn_id,
		:subscr_id,
		:ipn_response,
		:ipn_vars
	)";
$stmt = $conn->prepare($query);
$stmt->bindValue(':ipn_description', $ipn_description);
$stmt->bindValue(':place_id'       , $place_id);
$stmt->bindValue(':payer_email'    , $payer_email);
$stmt->bindValue(':txn_type'       , $txn_type);
$stmt->bindValue(':payment_status' , $payment_status);
$stmt->bindValue(':amount'         , $amount);
$stmt->bindValue(':txn_id'         , $txn_id);
$stmt->bindValue(':parent_txn_id'  , $parent_txn_id);
$stmt->bindValue(':subscr_id'      , $subscr_id);
$stmt->bindValue(':ipn_response'   , '');
$stmt->bindValue(':ipn_vars'       , $ipn_vars);
$stmt->execute();
