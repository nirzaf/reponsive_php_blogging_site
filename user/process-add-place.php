<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/smart_resize_image.php');
require_once(__DIR__ . '/user_area_inc.php');
require_once(__DIR__ . '/../vendor/swiftmailer/swiftmailer/lib/swift_required.php');

// csrf check
require_once(__DIR__ . '/_user_inc_request_with_php.php');

/*--------------------------------------------------
init vars
--------------------------------------------------*/
$errors = array();
$amount = 0;

// has errors
$has_errors = false;

// default assume place submitted successfully
$result_message = '';

/*--------------------------------------------------
Post vars
--------------------------------------------------*/
$latlng              = (!empty($_POST['latlng']             )) ? $_POST['latlng']              : '';
$plan_id             = (!empty($_POST['plan_id']            )) ? $_POST['plan_id']             : NULL;
$place_name          = (!empty($_POST['place_name']         )) ? $_POST['place_name']          : '';
$address             = (!empty($_POST['address']            )) ? $_POST['address']             : '';
$postal_code         = (!empty($_POST['postal_code']        )) ? $_POST['postal_code']         : '';
$cross_street        = (!empty($_POST['cross_street']       )) ? $_POST['cross_street']        : '';
$neighborhood        = (!empty($_POST['neighborhood']       )) ? $_POST['neighborhood']        : NULL;
$city_id             = (!empty($_POST['city_id']            )) ? $_POST['city_id']             : $default_loc_id;
$inside              = (!empty($_POST['inside']             )) ? $_POST['inside']              : '';
$area_code           = (!empty($_POST['area']               )) ? $_POST['area']                : NULL;
$phone               = (!empty($_POST['phone']              )) ? $_POST['phone']               : '';
$twitter             = (!empty($_POST['twitter']            )) ? $_POST['twitter']             : '';
$facebook            = (!empty($_POST['facebook']           )) ? $_POST['facebook']            : '';
$foursq_id           = (!empty($_POST['foursq_id']          )) ? $_POST['foursq_id']           : '';
$website             = (!empty($_POST['website']            )) ? $_POST['website']             : '';
$description         = (!empty($_POST['description']        )) ? $_POST['description']         : '';
$category_id         = (!empty($_POST['category_id']        )) ? $_POST['category_id']         : '';
$business_hours      = (!empty($_POST['business_hours']     )) ? $_POST['business_hours']      : array();
$business_hours_info = (!empty($_POST['business_hours_info'])) ? $_POST['business_hours_info'] : '';
$uploads             = (!empty($_POST['uploads']            )) ? $_POST['uploads']             : array();
$delete_temp_pics    = (!empty($_POST['delete_temp_pics']   )) ? $_POST['delete_temp_pics']    : array();
$custom_fields_ids   = (!empty($_POST['custom_fields_ids']  )) ? $_POST['custom_fields_ids']   : '';

/*--------------------------------------------------
prepare vars
--------------------------------------------------*/
// trim
$latlng              = is_string($latlng)              ? trim($latlng)              : $latlng;
$plan_id             = is_string($plan_id)             ? trim($plan_id)             : $plan_id ;
$place_name          = is_string($place_name)          ? trim($place_name)          : $place_name;
$address             = is_string($address)             ? trim($address)             : $address;
$postal_code         = is_string($postal_code)         ? trim($postal_code)         : $postal_code;
$cross_street        = is_string($cross_street)        ? trim($cross_street)        : $cross_street;
$neighborhood        = is_string($neighborhood)        ? trim($neighborhood)        : $neighborhood;
$inside              = is_string($inside)              ? trim($inside)              : $inside;
$area_code           = is_string($area_code)           ? trim($area_code)           : $area_code;
$phone               = is_string($phone)               ? trim($phone)               : $phone;
$twitter             = is_string($twitter)             ? trim($twitter)             : $twitter;
$facebook            = is_string($facebook)            ? trim($facebook)            : $facebook;
$foursq_id           = is_string($foursq_id)           ? trim($foursq_id)           : $foursq_id;
$website             = is_string($website)             ? trim($website)             : $website;
$description         = is_string($description)         ? trim($description)         : $description;
$business_hours      = is_string($business_hours)      ? trim($business_hours)      : $business_hours;
$business_hours_info = is_string($business_hours_info) ? trim($business_hours_info) : $business_hours_info;
$uploads             = is_string($uploads)             ? trim($uploads)             : $uploads;
$delete_temp_pics    = is_string($delete_temp_pics)    ? trim($delete_temp_pics)    : $delete_temp_pics;

// check plan id selection
if(empty($plan_id) && !$is_admin) {
	trigger_error("Invalid plan selection", E_USER_ERROR);
}

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

// if not a free plan
if($plan_type != 'free' && $plan_type != 'free_feat') {
	// if it's a monthly plan
	if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
		// init vars
		$cmd = "_xclick-subscriptions";
		$p3  = '1';
		$t3  = 'M';
		$src = '1';
		$srt = '52';
		$a3 = $plan_price;
		$amount = $plan_price;
	}

	// if it's an annual plan
	if($plan_type == 'annual' || $plan_type == 'annual_feat') {
		// init vars
		$cmd = "_xclick-subscriptions";
		$p3  = '1';
		$t3  = 'Y';
		$src = '1';
		$srt = '52';
		$a3 = $plan_price;
		$amount = $plan_price;
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

// check if is featured;
$feat = 0;
if(	   $plan_type == 'free_feat'
	|| $plan_type == 'monthly_feat'
	|| $plan_type == 'one_time_feat'
	|| $plan_type == 'annual_feat') {
	$feat = 1;
}

// lat/lng
if(!empty($latlng)) {
	$latlng = str_replace('(', '', $latlng);
	$latlng = str_replace(')', '', $latlng);
	$latlng = explode(',', $latlng);
	$lat    = trim($latlng[0]);
	$lng    = trim($latlng[1]);

	settype($lat, 'float');
	settype($lng, 'float');
}
else {
	$lat = $default_lat;
	$lng = $default_lng;
}

// clean phone
$area     = preg_replace("/[^0-9]/", "", $area_code);
$phone    = preg_replace("/[^0-9]/", "", $phone);

// normalize twitter url
$twitter  = twitter_url(trim($twitter));

// normalize facebook url
$facebook = facebook_url(trim($facebook));

// clean and normalize website url
$website  = site_url(trim($website));

// check valid foursquare id
if(!validate_username($foursq_id)) {
	$errors[]  = 'Invalid Foursquare Id';
	$foursq_id = '';
}

// if city id is empty, try to guess
// use function to get city name using lat lng coords
$state_id = 0;
if(is_null($city_id)) {
	if(is_float($lat) && is_float($lng)) {
		$query = "SELECT * , (3959 * ACOS(COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS($lng)) + SIN(RADIANS($lat)) * SIN( RADIANS(lat)))) AS distance FROM cities ORDER BY distance ASC LIMIT 1";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$city_id  = $row['city_id'];
		$state_id = $row['state_id'];
	}
	else {
		die('wrong lat/lng value type');
	}
}

// if state_id empty
if(empty($state_id) && !empty($city_id)) {
	$query = "SELECT state_id FROM cities WHERE city_id = :city_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $city_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$state_id = $row['state_id'];
}

// initial status
$status = "pending";
$paid   = 0;

// is this user the admin?
if($is_admin == 1) {
	$status = "approved";
	$paid = 1;
}

// if free plan types, set paid to 1
if($plan_type == 'free' || $plan_type == 'free_feat') {
	$paid = 1;
}

/*--------------------------------------------------
Custom fields
--------------------------------------------------*/
$custom_fields_ids = explode(',', $custom_fields_ids);
$custom_fields = array();
foreach($custom_fields_ids as $v) {
	$field_key = 'field_' . $v;

	if(!empty($_POST[$field_key])) {
		if(!is_array($_POST[$field_key])) {
			$this_field_value = (!empty($_POST[$field_key])) ? $_POST[$field_key] : '';
		}
		else {
			$this_field_value = (!empty($_POST[$field_key])) ? $_POST[$field_key] : array();
		}

		$custom_fields[] = array(
			'field_id'    => $v,
			'field_value' => $this_field_value);
	}
}

/*--------------------------------------------------
Submit routine
--------------------------------------------------*/
// check if this page is refreshed/reloaded
// if $_SESSION['submit_token'] and submitted $_POST['submit_token'] match
// it means that the page has not been reloaded,
// process insert, then unset $_SESSION['submit_token'],
// so that if user reloads this page, it doesn't match, so it's not inserted
$post_token    = (!empty($_POST['submit_token']))   ? $_POST['submit_token']    : 'aaa';
$session_token = (isset($_SESSION['submit_token'])) ? $_SESSION['submit_token'] : '';

if($post_token == $session_token) {
	try {
		$conn->beginTransaction();

		// neighborhood logic
		if(!empty($neighborhood)) {
			$neighborhood_slug = to_slug($neighborhood);

			$query = "SELECT * FROM neighborhoods
						WHERE neighborhood_slug = :neighborhood_slug AND city_id = :city_id LIMIT 1";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':neighborhood_slug', $neighborhood_slug);
			$stmt->bindValue(':city_id', $city_id);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			// this neighborhood in this city already exists
			if($neighborhood_slug == $row['neighborhood_slug']) {
					$neighborhood_id = $row['neighborhood_id'];
			}
			else {
				$query = "INSERT INTO neighborhoods(neighborhood_slug, neighborhood_name, city_id)
							VALUES(:neighborhood_slug, :neighborhood_name, :city_id)";
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':neighborhood_slug', $neighborhood_slug);
				$stmt->bindValue(':neighborhood_name', $neighborhood);
				$stmt->bindValue(':city_id', $city_id);
				$stmt->execute();

				$neighborhood_id = $conn->lastInsertId();
			}
		}

		$neighborhood_id = (isset($neighborhood_id) && !empty($neighborhood_id)) ? $neighborhood_id : NULL;

		// insert into places table
		$query = "INSERT INTO places(
			userid,
			lat,
			lng,
			place_name,
			address,
			postal_code,
			cross_street,
			neighborhood,
			city_id,
			state_id,
			inside,
			area_code,
			phone,
			twitter,
			facebook,
			foursq_id,
			website,
			description,
			business_hours_info,
			feat,
			plan,
			valid_until,
			status,
			paid
		)
		VALUES(
			:userid,
			:lat,
			:lng,
			:place_name,
			:address,
			:postal_code,
			:cross_street,
			:neighborhood,
			:city_id,
			:state_id,
			:inside,
			:area_code,
			:phone,
			:twitter,
			:facebook,
			:foursq_id,
			:website,
			:description,
			:business_hours_info,
			:feat,
			:plan,
			DATE_ADD(CURRENT_TIMESTAMP, INTERVAL :valid_until DAY),
			:status,
			:paid
		)";

		// set valid until value which is just the number of days of the period
		$valid_until = ($plan_period == 0 || $plan_period > 9999) ? 9999 : $plan_period;

		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid'              , $userid);
		$stmt->bindValue(':lat'                 , $lat);
		$stmt->bindValue(':lng'                 , $lng);
		$stmt->bindValue(':place_name'          , $place_name);
		$stmt->bindValue(':address'             , $address);
		$stmt->bindValue(':postal_code'         , $postal_code);
		$stmt->bindValue(':cross_street'        , $cross_street);
		$stmt->bindValue(':neighborhood'        , $neighborhood_id);
		$stmt->bindValue(':city_id'             , $city_id);
		$stmt->bindValue(':state_id'            , $state_id);
		$stmt->bindValue(':inside'              , $inside);
		$stmt->bindValue(':area_code'           , $area_code);
		$stmt->bindValue(':phone'               , $phone);
		$stmt->bindValue(':twitter'             , $twitter);
		$stmt->bindValue(':facebook'            , $facebook);
		$stmt->bindValue(':foursq_id'           , $foursq_id);
		$stmt->bindValue(':website'             , $website);
		$stmt->bindValue(':description'         , $description);
		$stmt->bindValue(':business_hours_info' , $business_hours_info);
		$stmt->bindValue(':feat'                , $feat);
		$stmt->bindValue(':plan'                , $plan_id);
		$stmt->bindValue(':valid_until'         , $valid_until);
		$stmt->bindValue(':status'              , $status);
		$stmt->bindValue(':paid'                , $paid);
		$stmt->execute();

		$place_id = $conn->lastInsertId();
		$_SESSION['last_submitted_place_id'] = $place_id;

		// rel_place_cat
		if(!empty($category_id)) {
			if(is_numeric($category_id)) {
				$query = "INSERT INTO rel_place_cat(place_id, cat_id, city_id)
					VALUES(:place_id, :cat_id, :city_id)";
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':place_id', $place_id);
				$stmt->bindValue(':cat_id', $category_id);
				$stmt->bindValue(':city_id', $city_id);
				$stmt->execute();
			}
		}

		// business hours
		$allowed_hours = array("0000", "0030", "0100", "0130", "0200", "0230", "0300", "0330", "0400", "0430", "0500", "0530", "0600", "0630", "0700", "0730", "0800", "0830", "0900", "0930", "1000", "1030", "1100", "1130", "1200", "1230", "1300", "1330", "1400", "1430", "1500", "1530", "1600", "1630", "1700", "1730", "1800", "1830", "1900", "1930", "2000", "2030", "2100", "2130", "2200", "2230", "2300", "2330");
		$allowed_days = array("0", "1", "2", "3", "4", "5", "6");

		// now add submitted business hours
		$business_hours_count = 1; // counter to limit number of categories submitted

		foreach($business_hours as $k => $v) {
			$each_day = explode(',', $v);
			$day = $each_day[0];
			$open = $each_day[1];
			$close = $each_day[2];

			if(in_array($day, $allowed_days) && in_array($open, $allowed_hours) && in_array($close, $allowed_hours)) {
				$stmt = $conn->prepare('INSERT INTO business_hours(place_id, day, open, close)	VALUES(:place_id, :day, :open, :close)');
				$stmt->bindValue(':place_id', $place_id);
				$stmt->bindValue(':day', $day);
				$stmt->bindValue(':open', $open);
				$stmt->bindValue(':close', $close);
				$stmt->execute();
				$business_hours_count++;
			}
		}

		// photos

		// delete pics from temp folder that were deleted by user while posting
		if(!empty($delete_temp_pics)) {
			foreach($delete_temp_pics as $v) {
				$temp_pic_path = $pic_basepath . '/' . $place_tmp_folder . '/' . $v;
				if(is_file($temp_pic_path)) {
					unlink($temp_pic_path);
				}
			}
		}

		// uploaded images
		if(!empty($uploads)) {
			// define dirs
			$query = "SELECT photo_id FROM photos ORDER BY photo_id DESC LIMIT 1";
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$last_photo_id = $row['photo_id'];
			$dir_num = floor($last_photo_id / 1000) + 1;

			$dir_full  = $pic_basepath . '/' . $place_full_folder . '/' . $dir_num;
			$dir_thumb = $pic_basepath . '/' . $place_thumb_folder . '/' . $dir_num;

			if (!is_dir($dir_full)) {
				mkdir($dir_full, 0777, true);
			}

			if (!is_dir($dir_thumb)) {
				mkdir($dir_thumb, 0777, true);
			}

			// tmp folder
			$tmp_folder = $pic_basepath . '/' . $place_tmp_folder;

			if(!isset($global_thumb_width)) {
				$global_thumb_width = 250;
			}
			if(!isset($global_thumb_height)) {
				$global_thumb_height = 250;
			}

			foreach($uploads as $v) {
				// only insert into db if the move from temp to final destination folder is successful,
				// otherwise user could send custom uploads[] value and replace original(thus deleting) previous pics
				// from other ads
				$tmp_file = $tmp_folder . '/' . $v;

				if(copy($tmp_file, $dir_full . '/' . $v)) {
					// insert into photos table
					$stmt = $conn->prepare('
					INSERT INTO photos(place_id, dir, filename)
					VALUES(:place_id, :dir, :filename)');

					$stmt->bindValue(':place_id', $place_id);
					$stmt->bindValue(':dir'     , $dir_num);
					$stmt->bindValue(':filename', $v);
					$stmt->execute();
				}

				smart_resize_image($tmp_file, null, $global_thumb_width, $global_thumb_height, false, $dir_thumb . '/' . $v, true, false, 85);

				// delete pic from tmp_photos table
				$query = "DELETE FROM tmp_photos WHERE filename = :filename";
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':filename', $v);
				$stmt->execute();
			}
		} // end if(!empty($uploads))

		// custom fields
		foreach($custom_fields as $v) {
			if(!is_array($v['field_value'])) {
				if(!empty($v['field_value'])) {
					$query = "INSERT INTO rel_place_custom_fields(place_id, field_id, field_value)
						VALUES(:place_id, :field_id, :field_value)";
					$stmt = $conn->prepare($query);
					$stmt->bindValue(':place_id', $place_id);
					$stmt->bindValue(':field_id', $v['field_id']);
					$stmt->bindValue(':field_value', $v['field_value']);
					$stmt->execute();
				}
			}
			else {
				foreach($v['field_value'] as $v2) {
					if(!empty($v2)) {
						$query = "INSERT INTO rel_place_custom_fields(place_id, field_id, field_value)
							VALUES(:place_id, :field_id, :field_value)";
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':place_id', $place_id);
						$stmt->bindValue(':field_id', $v['field_id']);
						$stmt->bindValue(':field_value', $v2);
						$stmt->execute();
					}
				}
			}
		}

		$conn->commit();
		$has_errors = false;
		$txt_main_title = $txt_main_title_success;
		$result_message = $txt_checkout_msg;
	} // end try block
	catch(PDOException $e) {
		$conn->rollBack();
		$has_errors = true;
		$txt_main_title = $txt_main_title_error;
		$result_message = $e->getMessage();
	}

	// empty session submit token
	unset($_SESSION['submit_token']);
} // end if($post_token == $session_token)
else { // else probably user reloaded page
	$has_errors = false; // false so the paypal button is shown
	$txt_main_title = $txt_main_title_success;
	$result_message = $txt_checkout_msg;
}

// thanks messages
$txt_thanks = (!$is_admin) ? $txt_thanks_msg : $txt_thanks_admin;

// payment gateway vars
// if paypal live
if($paypal_mode == 1) {
	$paypal_url         = 'https://www.paypal.com/cgi-bin/webscr';
}
// else is paypal sandbox
else {
	$paypal_url         = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	$paypal_merchant_id = $paypal_sandbox_merch_id;
}
// if 2checkout live
if($_2checkout_mode == 1) {
	$_2checkout_url = 'https://www.2checkout.com/checkout/purchase';
}
// else is 2checkout sandbox
else {
	$_2checkout_url = 'https://sandbox.2checkout.com/checkout/purchase';
	$_2checkout_sid = $_2checkout_sandbox_sid;
}

// place id, in case page is refreshed, $conn->lastInsertId() is lost, so get place_id from SESSION
if(empty($place_id) && isset($_SESSION['last_submitted_place_id'])) {
	$place_id = $_SESSION['last_submitted_place_id'];
}

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
	// init
	$button_link = null;

	// get user email
	$query = "SELECT email FROM users WHERE id = :userid";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':userid', $userid);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$payer_email = (!empty($row['email'])) ? $row['email'] : '';

	// validate email
	if(Swift_Validate::email($payer_email)) {
		$valid_email = true;
	} else {
		$valid_email = false;
	}

	// init create payment preference
	$mp     = new MP($mercadopago_client_id, $mercadopago_client_secret);
	$amount = (!empty($amount)) ? $amount : 0;

	if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
		// preference data buy now
		$preference_data = array(
			'items' => array(
				array(
					'id'          => $place_id,
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

	if($plan_type == 'monthly' || $plan_type == 'monthly_feat' || $plan_type == 'annual' || $plan_type == 'annual_feat') {
		// frequency
		$frequency = 1;

		// if annual
		if($plan_type == 'annual' || $plan_type = 'annual_feat') {
			$frequency = 12;
		}

		// check if user has valid email
		if($valid_email) {
			// preapproval data (recurring)
			$preapproval_data = array(
				"payer_email" => $payer_email,
				"back_url" => "$baseurl/user/thanks",
				"reason" => "$plan_name - $site_name",
				"external_reference" => "PLACE-" . $place_id,
				"auto_recurring" => array(
					"frequency"          => $frequency,
					"frequency_type"     => 'months',
					"transaction_amount" => (float)$plan_price,
					"currency_id"        => $mercadopago_currency_id
				)
			);

			$button_link = $mp->create_preapproval_payment($preapproval_data);
			$action      = 'subscribe';
		}
	}
}

/*--------------------------------------------------
translation var check if exists
--------------------------------------------------*/
// v. 1.06
$txt_btn_submit_2checkout = (!empty($txt_btn_submit_2checkout)) ? $txt_btn_submit_2checkout : "Pay with 2Checkout";
$txt_btn_mercadopago      = (!empty($txt_btn_mercadopago))      ? $txt_btn_mercadopago      : "Pagar com MercadoPago";

// v.1.08
$txt_invalid_email = (!empty($txt_invalid_email)) ? $txt_invalid_email : "Para pagar com MercadoPago é necessário configurar um email em <a href='$baseurl/user/my-profile'>seu painel</a>";

/*--------------------------------------------------
email admin
--------------------------------------------------*/
if(!empty($admin_receive_notification)) {
	// initialize swiftmailer
	$transport_smtp = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port)
		->setUsername($smtp_user)
		->setPassword($smtp_pass);

	$mailer = Swift_Mailer::newInstance($transport_smtp);

	$query = "SELECT * FROM email_templates WHERE type = 'process_add_place'";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$email_subject = (!empty($row['subject'])) ? $row['subject'] : '';
	$email_body    = (!empty($row['body']   )) ? $row['body'] : '';

	if(!empty($email_subject)) {
		$message = Swift_Message::newInstance()
			->setSubject($email_subject)
			->setFrom(array($admin_email => $site_name))
			->setTo($admin_email)
			->setBody($email_body)
			->setReplyTo($admin_email)
			->setReturnPath($admin_email)
			;

		$mailer->send($message);
	}
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_process-add-place.php');