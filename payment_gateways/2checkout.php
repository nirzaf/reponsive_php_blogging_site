<?php
/**
 * Mercadopago listener
 * This file is a copy the file ipn-2checkout.php located in the root folder
 * The old ipn-2checkout.php located in the root folder is kept there for backwards compatibility
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

// 2checkout vars array
$ipn_message = array();
foreach($_POST as $k => $v) {
	$ipn_message[$k] = $v;
}

// json version
$ipn_json = (!empty($ipn_message)) ? json_encode($ipn_message) : 'empty json';

// prepare vars
$message_type        = (!empty($ipn_message['message_type']))        ? $ipn_message['message_type']        : '';
$md5_hash            = (!empty($ipn_message['md5_hash']))            ? $ipn_message['md5_hash']            : '';
$sale_id             = (!empty($ipn_message['sale_id']))             ? $ipn_message['sale_id']             : '';
$invoice_id          = (!empty($ipn_message['invoice_id']))          ? $ipn_message['invoice_id']          : '';
$invoice_status      = (!empty($ipn_message['invoice_status']))      ? $ipn_message['invoice_status']      : '';
$customer_email      = (!empty($ipn_message['customer_email']))      ? $ipn_message['customer_email']      : '';
$customer_first_name = (!empty($ipn_message['customer_first_name'])) ? $ipn_message['customer_first_name'] : '';
$customer_ip         = (!empty($ipn_message['customer_ip']))         ? $ipn_message['customer_ip']         : '';
$customer_last_name  = (!empty($ipn_message['customer_last_name']))  ? $ipn_message['customer_last_name']  : '';
$customer_name       = (!empty($ipn_message['customer_name']))       ? $ipn_message['customer_name']       : '';
$fraud_status        = (!empty($ipn_message['fraud_status']))        ? $ipn_message['fraud_status']        : '';
$invoice_list_amount = (!empty($ipn_message['invoice_list_amount'])) ? $ipn_message['invoice_list_amount'] : '0.00';
$item_id_1           = (!empty($ipn_message['item_id_1']))           ? $ipn_message['item_id_1']           : -1;
$item_list_amount_1  = (!empty($ipn_message['item_list_amount_1']))  ? $ipn_message['item_list_amount_1']  : '0.00';
$recurring           = (!empty($ipn_message['recurring ']))          ? $ipn_message['recurring ']          : '0';

if($recurring == 0) {
	$txn_type = 'payment';
}
else {
	$txn_type = 'subscr_payment ';
}

// Validate the Hash
// UPPERCASE(MD5_ENCRYPTED(sale_id + vendor_id + invoice_id + Secret Word))
$my_hash         = strtoupper(md5($sale_id . $_2checkout_sid . $invoice_id . $_2checkout_secret));
$my_hash_sandbox = strtoupper(md5($sale_id . $_2checkout_sandbox_sid . $invoice_id . $_2checkout_secret));

/*
ipn_description = ('2Checkout IPN');
txn_type = ('payment', 'subscr_payment');
payment_status = ('Amount Mismatch', 'Failed', 'Pass', 'Hash Mismatch');
ipn_response = $message_type ('ORDER_CREATED', 'FRAUD_STATUS_CHANGED', 'RECURRING_INSTALLMENT_SUCCESS', 'RECURRING_INSTALLMENT_FAILED', 'RECURRING_STOPPED', 'RECURRING_COMPLETE', 'RECURRING_RESTARTED')
*/

// hashes don't match
if ($my_hash != $md5_hash && $my_hash_sandbox != $md5_hash) {
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
	$stmt->bindValue(':ipn_description' , '2Checkout IPN');
	$stmt->bindValue(':place_id'        , $item_id_1);
	$stmt->bindValue(':payer_email'     , $customer_email);
	$stmt->bindValue(':txn_type'        , $txn_type);
	$stmt->bindValue(':payment_status'  , 'Hash Mismatch');
	$stmt->bindValue(':amount'          , $invoice_list_amount);
	$stmt->bindValue(':txn_id'          , $invoice_id);
	$stmt->bindValue(':ipn_response'    , $message_type);
	$stmt->bindValue(':ipn_vars'        , $ipn_json);
	$stmt->execute();
}
// else hashes match
else {
	// get place details and build link to place to use in emails
	$query = "SELECT p.place_name, p.userid, c.city_name
		FROM places p
		LEFT JOIN cities c ON p.city_id = c.city_id
		WHERE place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $item_id_1);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$place_name      = (!empty($row['place_name'])) ? $row['place_name'] : '';
	$city_name       = (!empty($row['city_name'] )) ? $row['city_name']  : '';
	$place_userid    = (!empty($row['userid'] ))    ? $row['userid']     : '';
	$place_city_slug = to_slug($city_name);
	$place_name_slug = to_slug($place_name);
	$place_link      = $baseurl . '/' . $place_city_slug . '/place/' . $item_id_1 . '/' . $place_name_slug;

	// get plan details associated with this place
	$query = "SELECT plans.* FROM places
		RIGHT JOIN plans ON places.plan = plans.plan_id
		WHERE places.place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $item_id_1);
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

	/*--------------------------------------------------
	SWITCH IPN TRANSACTION TYPES
	--------------------------------------------------*/
	switch ($message_type) {
		case 'FRAUD_STATUS_CHANGED':
			if ($fraud_status == 'pass') {
				// get email template from db
				if($recurring == 0) {
					$query = "SELECT * FROM email_templates WHERE type = 'web_accept'";
				}
				else {
					$query = "SELECT * FROM email_templates WHERE type = 'subscr_signup'";
				}
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

				// insert into transactions table
				try {
					// use transaction
					$conn->beginTransaction();

					// update paid column in places table
					if($item_list_amount_1 == $plan_price) {
						// send email to user
						$message = Swift_Message::newInstance()
							->setSubject($email_subject)
							->setFrom(array($admin_email => $site_name))
							->setTo($customer_email)
							->setBody($email_body)
							->setReplyTo($admin_email)
							->setReturnPath($admin_email)
							;

						$mailer->send($message);

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
						$stmt->bindValue(':ipn_description' , '2Checkout IPN');
						$stmt->bindValue(':place_id'        , $item_id_1);
						$stmt->bindValue(':payer_email'     , $customer_email);
						$stmt->bindValue(':txn_type'        , $txn_type);
						$stmt->bindValue(':payment_status'  , 'Pass');
						$stmt->bindValue(':amount'          , $invoice_list_amount);
						$stmt->bindValue(':txn_id'          , $invoice_id);
						$stmt->bindValue(':ipn_response'    , 'FRAUD_STATUS_CHANGED');
						$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
						$stmt->execute();

						$query = 'UPDATE places SET
									paid = 1,
									valid_until = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :valid_until DAY)
								WHERE place_id = :place_id';
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':place_id', $item_id_1);
						$stmt->bindValue(':valid_until', $plan_period);
						$stmt->execute();
					}
					// else amount mismatch
					else {
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
						$stmt->bindValue(':ipn_description' , '2Checkout IPN');
						$stmt->bindValue(':place_id'        , $item_id_1);
						$stmt->bindValue(':payer_email'     , $customer_email);
						$stmt->bindValue(':txn_type'        , $txn_type);
						$stmt->bindValue(':payment_status'  , 'Amount Mismatch');
						$stmt->bindValue(':amount'          , $invoice_list_amount);
						$stmt->bindValue(':txn_id'          , $invoice_id);
						$stmt->bindValue(':ipn_response'    , 'FRAUD_STATUS_CHANGED');
						$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
						$stmt->execute();
					} // end else amount mismatch

					$conn->commit();
				} // end try block
				catch(PDOException $e) {
					$conn->rollBack();
					$result_message = $e->getMessage();

					// log transaction rollBack in db
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
					$stmt->bindValue(':ipn_description' , '2Checkout IPN');
					$stmt->bindValue(':place_id'        , $item_id_1);
					$stmt->bindValue(':payer_email'     , $customer_email);
					$stmt->bindValue(':txn_type'        , $txn_type);
					$stmt->bindValue(':payment_status'  , 'Rollback');
					$stmt->bindValue(':amount'          , $invoice_list_amount);
					$stmt->bindValue(':txn_id'          , $invoice_id);
					$stmt->bindValue(':ipn_response'    , 'FRAUD_STATUS_CHANGED');
					$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
					$stmt->execute();
				}
			} // end if ($fraud_status == 'pass')

			// else didn't pass fraud status
			else {
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
				$stmt->bindValue(':ipn_description' , '2Checkout IPN');
				$stmt->bindValue(':place_id'        , $item_id_1);
				$stmt->bindValue(':payer_email'     , $customer_email);
				$stmt->bindValue(':txn_type'        , $txn_type);
				$stmt->bindValue(':payment_status'  , 'Fraud Check Failed');
				$stmt->bindValue(':amount'          , $invoice_list_amount);
				$stmt->bindValue(':txn_id'          , $invoice_id);
				$stmt->bindValue(':ipn_response'    , 'FRAUD_STATUS_CHANGED');
				$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
				$stmt->execute();
			}
		break;
		case 'RECURRING_INSTALLMENT_FAILED':
		case 'REFUND_ISSUED':
		case 'RECURRING_STOPPED':
			// disable listing
			$query = 'UPDATE places SET paid = 0 WHERE place_id = :place_id';
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':place_id', $item_id_1);
			$stmt->execute();

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
			$stmt->bindValue(':ipn_description' , '2Checkout IPN');
			$stmt->bindValue(':place_id'        , $item_id_1);
			$stmt->bindValue(':payer_email'     , $customer_email);
			$stmt->bindValue(':txn_type'        , $txn_type);
			$stmt->bindValue(':payment_status'  , 'Recurring Failed or Stopped or Refund');
			$stmt->bindValue(':amount'          , $invoice_list_amount);
			$stmt->bindValue(':txn_id'          , $invoice_id);
			$stmt->bindValue(':ipn_response'    , 'RECURRING_INSTALLMENT_FAILED, REFUND_ISSUED, RECURRING_STOPPED');
			$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
			$stmt->execute();

		break;
		case 'RECURRING_INSTALLMENT_SUCCESS':
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
			$stmt->bindValue(':ipn_description' , '2Checkout IPN');
			$stmt->bindValue(':place_id'        , $item_id_1);
			$stmt->bindValue(':payer_email'     , $customer_email);
			$stmt->bindValue(':txn_type'        , $txn_type);
			$stmt->bindValue(':payment_status'  , 'Completed');
			$stmt->bindValue(':amount'          , $invoice_list_amount);
			$stmt->bindValue(':txn_id'          , $invoice_id);
			$stmt->bindValue(':ipn_response'    , 'RECURRING_INSTALLMENT_SUCCESS');
			$stmt->bindValue(':ipn_vars'        , json_encode($ipn_message));
			$stmt->execute();
		break;
	} // end switch ($message_type)
} // end hash match