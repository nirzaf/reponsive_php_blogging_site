<?php
/**
 * Paypal IPN listener
 * This file is a copy the file ipn-handler.php located in the root folder
 * The old ipn-handler.php located in the root folder is kept there for backwards compatibility
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

/*--------------------------------------------------------------
RECEIVE POST FROM PAYPAL
--------------------------------------------------------------*/
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
$raw_post_data  = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$paypal_post    = array();

foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2) {
		$paypal_post[$keyval[0]] = urldecode($keyval[1]);
	}
}

//extract vars
extract($paypal_post, EXTR_OVERWRITE);

// build request
$req = 'cmd=' . urlencode('_notify-validate');
$ipn_vars = 'cmd=' . urlencode('_notify-validate');
foreach ($paypal_post as $k => $v) {
	$v = urlencode($v);
	$req .= "&$k=$v";
}

// sort array keys (only after building $req var which will be used to send curl to paypal)
ksort($paypal_post);
foreach ($paypal_post as $k => $v) {
	$ipn_vars .= "\n$k=" . urldecode($v);
}

/*--------------------------------------------------------------
SEND POST BACK TO PAYPAL
--------------------------------------------------------------*/
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $paypal_url);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
$res = curl_exec($ch);

//Check if any error occured
if(curl_errno($ch)) {
	$ipn_response = curl_error($ch);
	$query = "INSERT INTO transactions(ipn_response, ipn_vars)
		VALUES(:ipn_response, :ipn_vars)";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':ipn_response', $ipn_response);
	$stmt->bindValue(':ipn_vars', $ipn_vars);
	$stmt->execute();
}
// else no curl error
else {
	if(strcmp($res, "VERIFIED") == 0) {
		$ipn_response = "VERIFIED";
		// ipn vars
		$business         = (isset($_POST['business']      )) ? $_POST['business']       : '';
		$first_name       = (isset($_POST['first_name']    )) ? $_POST['first_name']     : '';
		$item_name        = (isset($_POST['item_name']     )) ? $_POST['item_name']      : '';
		$item_number      = (isset($_POST['item_number']   )) ? $_POST['item_number']    : '';
		$mc_amount3       = (isset($_POST['mc_amount3']    )) ? $_POST['mc_amount3']     : '';
		$mc_gross         = (isset($_POST['mc_gross']      )) ? $_POST['mc_gross']       : '';
		$payer_email      = (isset($_POST['payer_email']   )) ? $_POST['payer_email']    : '';
		$payment_amount   = (isset($_POST['mc_gross']      )) ? $_POST['mc_gross']       : '';
		$payment_currency = (isset($_POST['mc_currency']   )) ? $_POST['mc_currency']    : '';
		$payment_status   = (isset($_POST['payment_status'])) ? $_POST['payment_status'] : '';
		$place_id         = (isset($_POST['custom']        )) ? $_POST['custom']         : '';
		$receiver_email   = (isset($_POST['receiver_email'])) ? $_POST['receiver_email'] : '';
		$subscr_id        = (isset($_POST['subscr_id']     )) ? $_POST['subscr_id']      : '';
		$txn_id           = (isset($_POST['txn_id']        )) ? $_POST['txn_id']         : '';
		$txn_type         = (isset($_POST['txn_type']      )) ? $_POST['txn_type']       : '';

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
		$query = "SELECT email, first_name FROM users WHERE id = :place_userid";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_userid', $place_userid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$place_user_email     = (!empty($row['email']     )) ? $row['email']      : '';
		$place_user_firstname = (!empty($row['first_name'])) ? $row['first_name'] : $first_name;

		/*--------------------------------------------------
		SWITCH IPN TRANSACTION TYPES
		--------------------------------------------------*/

		switch($txn_type) {
			/* paypal transaction types for subscriptions
			subscr_signup	Subscription started
			subscr_payment	Subscription payment received
			subscr_cancel	Subscription canceled
			subscr_eot	    Subscription expired
			subscr_failed	Subscription payment failed
			subscr_modify	Subscription modified
			*/

			case 'subscr_signup':
				if(!empty($mc_amount3) && $mc_amount3 == $plan_price) {
					$ipn_description = 'subscr_signup: success';

					// email user informing subscr_signup success
					$query = "SELECT * FROM email_templates WHERE type = 'subscr_signup'";
					$stmt = $conn->prepare($query);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
					$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

					$email_body = str_replace('%username%', $place_user_firstname, $email_body);
					$email_body = str_replace('%place_link%', $place_link, $email_body);

					$message = Swift_Message::newInstance()
						->setSubject($email_subject)
						->setFrom(array($admin_email => $site_name))
						->setTo($payer_email)
						->setBody($email_body)
						->setReplyTo($admin_email)
						->setReturnPath($admin_email)
						;

					// Send the message
					$mailer->send($message);

					// populate 'transactions' table with subscr_signup info
					$query = 'INSERT INTO transactions(
						ipn_description,
						place_id,
						payer_email,
						txn_type,
						payment_status,
						amount,
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
						:subscr_id,
						:ipn_response,
						:ipn_vars
						)';

					$stmt = $conn->prepare($query);
					$stmt->bindValue(':ipn_description' , $ipn_description);
					$stmt->bindValue(':place_id'        , $place_id);
					$stmt->bindValue(':payer_email'     , $payer_email);
					$stmt->bindValue(':txn_type'        , $txn_type);
					$stmt->bindValue(':payment_status'  , $payment_status);
					$stmt->bindValue(':amount'          , $mc_amount3);
					$stmt->bindValue(':subscr_id'       , $subscr_id);
					$stmt->bindValue(':ipn_response'    , $ipn_response);
					$stmt->bindValue(':ipn_vars'        , $ipn_vars);
					$stmt->execute();

					// update paid column in places table
					$query = 'UPDATE places SET paid = 1 WHERE place_id = :place_id';
					$stmt = $conn->prepare($query);
					$stmt->bindValue(':place_id', $place_id);
					$stmt->execute();

				} //end if($mc_amount3 == $plan_price)
				else {
					// else problem with amount
					$ipn_description = 'subscr_signup: amount problem';

					// populate 'transactions' table with subscr_signup info
					$query = 'INSERT INTO transactions(
						ipn_description,
						place_id,
						payer_email,
						txn_type,
						payment_status,
						amount,
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
						:subscr_id,
						:ipn_response,
						:ipn_vars
						)';

					$stmt = $conn->prepare($query);
					$stmt->bindValue(':ipn_description' , $ipn_description);
					$stmt->bindValue(':place_id'        , $place_id);
					$stmt->bindValue(':payer_email'     , $payer_email);
					$stmt->bindValue(':txn_type'        , $txn_type);
					$stmt->bindValue(':payment_status'  , $payment_status);
					$stmt->bindValue(':amount'          , $mc_amount3);
					$stmt->bindValue(':subscr_id'       , $subscr_id);
					$stmt->bindValue(':ipn_response'    , $ipn_response);
					$stmt->bindValue(':ipn_vars'        , $ipn_vars);
					$stmt->execute();
				}
			break; // end case subscr_signup

			case 'subscr_payment':
				$ipn_description = 'subscr_payment: success';

				// for subscr_payment, just insert transaction into db
				$query = 'INSERT INTO transactions(
					ipn_description,
					place_id,
					payer_email,
					txn_type,
					payment_status,
					amount,
					txn_id,
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
					:subscr_id,
					:ipn_response,
					:ipn_vars
					)';
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':ipn_description', $ipn_description);
				$stmt->bindValue(':place_id'       , $place_id);
				$stmt->bindValue(':payer_email'    , $payer_email);
				$stmt->bindValue(':txn_type'       , $txn_type);
				$stmt->bindValue(':payment_status' , $payment_status);
				$stmt->bindValue(':txn_id'         , $txn_id);
				$stmt->bindValue(':amount'         , $mc_gross);
				$stmt->bindValue(':subscr_id'      , $subscr_id);
				$stmt->bindValue(':ipn_response'   , $ipn_response);
				$stmt->bindValue(':ipn_vars'       , $ipn_vars);
				$stmt->execute();
			break;

			case 'subscr_cancel':
				$ipn_description = 'subscr_cancel';

				// just insert cancel transaction, no need to update place paid field, do on subscr_eot
				$query = 'INSERT INTO transactions(
					ipn_description,
					place_id,
					payer_email,
					txn_type,
					payment_status,
					amount,
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
					:subscr_id,
					:ipn_response,
					:ipn_vars
					)';
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':ipn_description', $ipn_description);
				$stmt->bindValue(':place_id'       , $place_id);
				$stmt->bindValue(':payer_email'    , $payer_email);
				$stmt->bindValue(':txn_type'       , $txn_type);
				$stmt->bindValue(':payment_status' , $payment_status);
				$stmt->bindValue(':amount'         , $mc_gross);
				$stmt->bindValue(':subscr_id'      , $subscr_id);
				$stmt->bindValue(':ipn_response'   , $ipn_response);
				$stmt->bindValue(':ipn_vars'       , $ipn_vars);
				$stmt->execute();
			break;

			case 'subscr_eot':
				$ipn_description = 'subscr_eot';

				// for subscr_eot, insert transaction into db and update place paid to 0
				$query = 'INSERT INTO transactions(
					ipn_description,
					place_id,
					payer_email,
					txn_type,
					payment_status,
					amount,
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
					:subscr_id,
					:ipn_response,
					:ipn_vars
					)';
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':ipn_description', $ipn_description);
				$stmt->bindValue(':place_id'       , $place_id);
				$stmt->bindValue(':payer_email'    , $place_id);
				$stmt->bindValue(':txn_type'       , $txn_type);
				$stmt->bindValue(':payment_status' , $payment_status);
				$stmt->bindValue(':amount'         , $mc_gross);
				$stmt->bindValue(':subscr_id'      , $subscr_id);
				$stmt->bindValue(':ipn_response'   , $ipn_response);
				$stmt->bindValue(':ipn_vars'       , $ipn_vars);
				$stmt->execute();

				// update places, set paid to 0
				if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
					$query = 'UPDATE places SET paid = 0 WHERE place_id = :place_id';
					$stmt = $conn->prepare($query);
					$stmt->bindValue(':place_id', $place_id);
					$stmt->execute();
				}

				// email user informing subscr_eot
				$query = "SELECT * FROM email_templates WHERE type = 'subscr_eot'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
				$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

				$email_body = str_replace('%username%', $place_user_firstname, $email_body);
				$email_body = str_replace('%place_link%', $place_link, $email_body);

				$message = Swift_Message::newInstance()
					->setSubject($email_subject)
					->setFrom(array($admin_email => $site_name))
					->setTo($payer_email)
					->setBody($email_body)
					->setReplyTo($admin_email)
					->setReturnPath($admin_email)
					;

				// Send the message
				$mailer->send($message);
			break;

			case 'subscr_failed':
				$ipn_description = 'subscr_failed';

				// insert into transactions table
				$query = 'INSERT INTO transactions(
					ipn_description,
					place_id,
					payer_email,
					txn_type,
					payment_status,
					amount,
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
					:subscr_id,
					:ipn_response,
					:ipn_vars
					)';
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':ipn_description', $ipn_description);
				$stmt->bindValue(':place_id'       , $place_id);
				$stmt->bindValue(':payer_email'    , $payer_email);
				$stmt->bindValue(':txn_type'       , $txn_type);
				$stmt->bindValue(':payment_status' , $payment_status);
				$stmt->bindValue(':amount'         , $mc_gross);
				$stmt->bindValue(':subscr_id'      , $subscr_id);
				$stmt->bindValue(':txn_id'         , $txn_id);
				$stmt->bindValue(':ipn_response'   , $ipn_response);
				$stmt->bindValue(':ipn_vars'       , $ipn_vars);
				$stmt->execute();

				// send email to user telling that his subscription payment failed
				$query = "SELECT * FROM email_templates WHERE type = 'subscr_failed'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$email_subject = $row['subject'];
				$email_body    = $row['body'];

				// make template substitutions
				$email_body = str_replace('%username%', $place_user_firstname, $email_body);

				$message = Swift_Message::newInstance()
					->setSubject($email_subject)
					->setFrom(array($admin_email => $site_name))
					->setTo($payer_email)
					->setBody($email_body)
					->setReplyTo($admin_email)
					->setReturnPath($admin_email)
					;

				$mailer->send($message);
			break;

			case 'subscr_modify':
				//
			break;

			// case = web_accept when plan is of type one_time or one_time_feat
			case 'web_accept':
				if(!empty($mc_gross) && $mc_gross == $plan_price) {
					$ipn_description = 'web_accept: success';

					// email user informing web_accept success
					$query = "SELECT * FROM email_templates WHERE type = 'web_accept'";
					$stmt = $conn->prepare($query);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
					$email_body    = (!empty($row['body']   )) ? $row['body']    : '';

					$email_body = str_replace('%username%', $place_user_firstname, $email_body);
					$email_body = str_replace('%place_link%', $place_link, $email_body);

					$message = Swift_Message::newInstance()
						->setSubject($email_subject)
						->setFrom(array($admin_email => $site_name))
						->setTo($payer_email)
						->setBody($email_body)
						->setReplyTo($admin_email)
						->setReturnPath($admin_email)
						;

					// Send the message
					$mailer->send($message);

					// populate 'transactions' table with web_accept info
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
					$stmt->bindValue(':ipn_description' , $ipn_description);
					$stmt->bindValue(':place_id'        , $place_id);
					$stmt->bindValue(':payer_email'     , $payer_email);
					$stmt->bindValue(':txn_type'        , $txn_type);
					$stmt->bindValue(':payment_status'  , $payment_status);
					$stmt->bindValue(':amount'          , $mc_gross);
					$stmt->bindValue(':txn_id'          , $txn_id);
					$stmt->bindValue(':ipn_response'    , $ipn_response);
					$stmt->bindValue(':ipn_vars'        , $ipn_vars);
					$stmt->execute();

					// update paid column in places table
					if($payment_status != 'Reversed') {
						$query = 'UPDATE places SET
									paid = 1,
									valid_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :valid_until DAY)
								WHERE place_id = :place_id';
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':place_id', $place_id);
						$stmt->bindValue(':valid_until', $plan_period);
						$stmt->execute();
					}

					// if it's a reversal
					if($payment_status == 'Reversed' || $payment_status == 'Refunded' ) {
						$query = 'UPDATE places SET
									paid = 0,
									valid_until = CURRENT_TIMESTAMP
								WHERE place_id = :place_id';
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':place_id', $place_id);
						$stmt->execute();
					}
				} //end if(!empty($mc_gross) && $mc_gross == $plan_price)

				// else mc_gross != $plan_price
				else {
					// transactions table
					$ipn_description = 'mc_gross != plan_price';
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
					$stmt->bindValue(':ipn_description' , $ipn_description);
					$stmt->bindValue(':place_id'        , $place_id);
					$stmt->bindValue(':payer_email'     , $payer_email);
					$stmt->bindValue(':txn_type'        , $txn_type);
					$stmt->bindValue(':payment_status'  , $payment_status);
					$stmt->bindValue(':amount'          , $mc_gross);
					$stmt->bindValue(':txn_id'          , $txn_id);
					$stmt->bindValue(':ipn_response'    , $ipn_response);
					$stmt->bindValue(':ipn_vars'        , $ipn_vars);
					$stmt->execute();
				}
			break;
			// end case 'web_accept'
		} // end switch txn_type
	}  // end if VERIFIED

	/*--------------------------------------------------
	PAYPAL RESPONDED WITH INVALID
	--------------------------------------------------*/
	else if(strcmp ($res, "INVALID") == 0) {
		$ipn_response    = 'INVALID';
		$ipn_description = 'invalid IPN';

		$place_id        = (!empty($place_id)       ) ? $place_id        : 0;
		$payer_email     = (!empty($payer_email)    ) ? $payer_email     : 'invalid';
		$txn_type        = (!empty($txn_type)       ) ? $txn_type        : 'invalid';
		$payment_status  = (!empty($payment_status) ) ? $payment_status  : 'invalid';
		$mc_gross        = (!empty($mc_gross)       ) ? $mc_gross        : 0;
		$txn_id          = (!empty($txn_id)         ) ? $txn_id          : 'invalid';
		$subscr_id       = (!empty($subscr_id)      ) ? $subscr_id       : 'invalid';
		$ipn_response    = (!empty($ipn_response)   ) ? $ipn_response    : 'invalid';
		$ipn_vars        = (!empty($ipn_vars)       ) ? $ipn_vars        : 'invalid';

		// transactions table
		$query = 'INSERT INTO transactions(
			ipn_description,
			place_id,
			payer_email,
			txn_type,
			payment_status,
			amount,
			txn_id,
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
			:subscr_id,
			:ipn_response,
			:ipn_vars
			)';

		$stmt = $conn->prepare($query);
		$stmt->bindValue(':ipn_description' , $ipn_description);
		$stmt->bindValue(':place_id'        , $place_id);
		$stmt->bindValue(':payer_email'     , $payer_email);
		$stmt->bindValue(':txn_type'        , $txn_type);
		$stmt->bindValue(':payment_status'  , $payment_status);
		$stmt->bindValue(':amount'          , $mc_gross);
		$stmt->bindValue(':txn_id'          , $txn_id);
		$stmt->bindValue(':subscr_id'       , $subscr_id);
		$stmt->bindValue(':ipn_response'    , $ipn_response);
		$stmt->bindValue(':ipn_vars'        , $ipn_vars);
		$stmt->execute();
	} // end if INVALID
	else { // IPN Problem
		$ipn_response = 'UNKNOWN';
		$ipn_description = 'UNKNOWN IPN';
	} // end else
}