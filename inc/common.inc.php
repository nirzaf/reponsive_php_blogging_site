<?php
require_once(__DIR__ . '/functions.php');

// error reporting
error_reporting(E_ALL);

// set exception handler
set_exception_handler('exception_handler');

//set error handler
set_error_handler('error_handler');

// fix ORIG_PATH_INFO
// some servers incorrectly define ORIG_PATH_INFO same as SCRIPT_NAME
$ORIG_PATH_INFO = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '';
$ORIG_PATH_INFO = str_replace($_SERVER['SCRIPT_NAME'], '', $ORIG_PATH_INFO);
$_SERVER['ORIG_PATH_INFO'] = $ORIG_PATH_INFO;

// get install path from baseurl
$parsed_url   = parse_url($baseurl);
$install_path = (empty($parsed_url['path']) ? '/' : $parsed_url['path']);

// plugin dir
$plugin_dir = __DIR__ . '/../plugins';

// construct PDO dsn
$dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;

// Create PDO object
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4');
try {
	$conn = new PDO($dsn, $db_user, $db_user_pass, $options);
	// setAttribute(ATTRIBUTE, OPTION);
	// default is silent error mode. Changing to throw exceptions
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Leave column names as returned by the database driver. Some PDO extensions return them in uppercase
	$conn->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
	// This is so as to use native prepare, which doesn't have problems with numeric params in LIMIT clause
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
}
catch(PDOException $e) {
	echo "<h2>Error</h2>";
	echo nl2br(htmlspecialchars($e->getMessage()));
	exit();
}

// set sql_mode, disable ONLY_FULL_GROUP_BY
$stmt = $conn->prepare("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
$stmt->execute();

// get global settings
// first init vars
$admin_email                  = '';
$dev_email                    = '';
$smtp_server                  = '';
$smtp_user                    = '';
$smtp_pass                    = '';
$smtp_port                    = '';
$google_key                   = '';
$items_per_page               = 0;
$site_name                    = '';
$country_name                 = '';
$default_country_code         = '';
$default_city_slug            = '';
$default_loc_id               = '';
$timezone                     = '';
$default_lat                  = '';
$default_lng                  = '';
$html_lang                    = '';
$max_pics                     = 0;
$mail_after_post              = '';
$paypal_merchant_id           = '';
$paypal_bn                    = '';
$paypal_checkout_logo_url     = '';
$currency_code                = '';
$currency_symbol              = '';
$paypal_locale                = '';
$notify_url                   = '';
$paypal_mode                  = '';
$paypal_sandbox_merch_id      = '';
$facebook_key                 = '';
$facebook_secret              = '';
$twitter_key                  = '';
$twitter_secret               = '';
$_2checkout_sid               = '';
$_2checkout_currency_code     = '';
$_2checkout_currency_symbol   = '';
$_2checkout_sandbox_sid       = '';
$_2checkout_secret            = '';
$_2checkout_mode              = '';
$_2checkout_lang              = '';
$_2checkout_notify_url        = '';
$mercadopago_mode             = '';
$mercadopago_client_id        = '';
$mercadopago_client_secret    = '';
$mercadopago_currency_id      = '';
$mercadopago_notification_url = '';
$stripe_mode                  = '';
$stripe_test_secret_key       = '';
$stripe_test_publishable_key  = '';
$stripe_live_secret_key       = '';
$stripe_live_publishable_key  = '';
$stripe_data_currency         = '';
$stripe_currency_symbol       = '';
$stripe_data_image            = '';
$stripe_data_description      = '';

$query = "SELECT * FROM config LIMIT 100";
$stmt  = $conn->prepare($query);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

	switch ($row['property']) {

		case 'admin_email':
			$admin_email = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'dev_email':
			$dev_email = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'smtp_server':
			$smtp_server = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'smtp_user':
			$smtp_user = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'smtp_pass':
			$smtp_pass = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'smtp_port':
			$smtp_port = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'google_key':
			$google_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'items_per_page':
			$items_per_page = (!empty($row['value'])) ? $row['value'] : 30;
			break;

		case 'site_name':
			$site_name = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'country_name':
			$country_name = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'default_country_code':
			$default_country_code = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'default_city_slug':
			$default_city_slug = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'default_loc_id':
			$default_loc_id = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'timezone':
			$timezone = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'default_lat':
			$default_lat = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'default_lng':
			$default_lng = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'html_lang':
			$html_lang = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'max_pics':
			$max_pics = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'mail_after_post':
			$mail_after_post = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'paypal_merchant_id':
			$paypal_merchant_id = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'paypal_bn':
			$paypal_bn = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'paypal_checkout_logo_url':
			$paypal_checkout_logo_url = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'currency_code':
			$currency_code = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'currency_symbol':
			$currency_symbol = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'paypal_locale':
			$paypal_locale = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'notify_url':
			$notify_url = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'paypal_mode':
			$paypal_mode = (isset($row['value'])) ? $row['value'] : -1;
			break;

		case 'paypal_sandbox_merch_id':
			$paypal_sandbox_merch_id = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'facebook_key':
			$facebook_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'facebook_secret':
			$facebook_secret = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'twitter_key':
			$twitter_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'twitter_secret':
			$twitter_secret = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_mode':
			$_2checkout_mode = (isset($row['value'])) ? $row['value'] : -1;
			break;

		case '_2checkout_sid':
			$_2checkout_sid = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_sandbox_sid':
			$_2checkout_sandbox_sid = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_secret':
			$_2checkout_secret = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_currency_code':
			$_2checkout_currency_code = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_currency_symbol':
			$_2checkout_currency_symbol = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_lang':
			$_2checkout_lang = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case '_2checkout_notify_url':
			$_2checkout_notify_url = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'mercadopago_mode':
			$mercadopago_mode = (isset($row['value'])) ? $row['value'] : -1;
			break;

		case 'mercadopago_client_id':
			$mercadopago_client_id = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'mercadopago_client_secret':
			$mercadopago_client_secret = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'mercadopago_currency_id':
			$mercadopago_currency_id = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'mercadopago_notification_url':
			$mercadopago_notification_url = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_mode':
			$stripe_mode = (isset($row['value'])) ? $row['value'] : -1;
			break;

		case 'stripe_test_secret_key':
			$stripe_test_secret_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_test_publishable_key':
			$stripe_test_publishable_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_live_secret_key':
			$stripe_live_secret_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_live_publishable_key':
			$stripe_live_publishable_key = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_data_currency':
			$stripe_data_currency = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_currency_symbol':
			$stripe_currency_symbol = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_data_image':
			$stripe_data_image = (!empty($row['value'])) ? $row['value'] : '';
			break;

		case 'stripe_data_description':
			$stripe_data_description = (!empty($row['value'])) ? $row['value'] : '';
			break;
	}
} // end while

$admin_email                  = trim(e($admin_email                 ));
$dev_email                    = trim(e($dev_email                   ));
$smtp_server                  = trim(e($smtp_server                 ));
$smtp_user                    = trim(e($smtp_user                   ));
$smtp_pass                    = trim(e($smtp_pass                   ));
$smtp_port                    = trim(e($smtp_port                   ));
$google_key                   = trim(e($google_key                  ));
$items_per_page               = trim(e($items_per_page              ));
$site_name                    = trim(e($site_name                   ));
$country_name                 = trim(e($country_name                ));
$default_country_code         = trim(e($default_country_code        ));
$default_city_slug            = trim(e($default_city_slug           ));
$default_loc_id               = trim(e($default_loc_id              ));
$timezone                     = trim(e($timezone                    ));
$default_lat                  = trim(e($default_lat                 ));
$default_lng                  = trim(e($default_lng                 ));
$html_lang                    = trim(e($html_lang                   ));
$max_pics                     = trim(e($max_pics                    ));
$mail_after_post              = trim(e($mail_after_post             ));
$paypal_merchant_id           = trim(e($paypal_merchant_id          ));
$paypal_bn                    = trim(e($paypal_bn                   ));
$paypal_checkout_logo_url     = trim(e($paypal_checkout_logo_url    ));
$currency_code                = trim(e($currency_code               ));
$currency_symbol              = trim(e($currency_symbol             ));
$paypal_locale                = trim(e($paypal_locale               ));
$notify_url                   = trim(e($notify_url                  ));
$paypal_mode                  = trim(e($paypal_mode                 ));
$paypal_sandbox_merch_id      = trim(e($paypal_sandbox_merch_id     ));
$facebook_key                 = trim(e($facebook_key                ));
$facebook_secret              = trim(e($facebook_secret             ));
$twitter_key                  = trim(e($twitter_key                 ));
$twitter_secret               = trim(e($twitter_secret              ));
$_2checkout_sid               = trim(e($_2checkout_sid              ));
$_2checkout_currency_code     = trim(e($_2checkout_currency_code    ));
$_2checkout_currency_symbol   = trim(e($_2checkout_currency_symbol  ));
$_2checkout_sandbox_sid       = trim(e($_2checkout_sandbox_sid      ));
$_2checkout_secret            = trim(e($_2checkout_secret           ));
$_2checkout_mode              = trim(e($_2checkout_mode             ));
$_2checkout_lang              = trim(e($_2checkout_lang             ));
$_2checkout_notify_url        = trim(e($_2checkout_notify_url       ));
$mercadopago_mode             = trim(e($mercadopago_mode            ));
$mercadopago_client_id        = trim(e($mercadopago_client_id       ));
$mercadopago_client_secret    = trim(e($mercadopago_client_secret   ));
$mercadopago_currency_id      = trim(e($mercadopago_currency_id     ));
$mercadopago_notification_url = trim(e($mercadopago_notification_url));
$stripe_mode                  = trim(e($stripe_mode                 ));
$stripe_test_secret_key       = trim(e($stripe_test_secret_key      ));
$stripe_test_publishable_key  = trim(e($stripe_test_publishable_key ));
$stripe_live_secret_key       = trim(e($stripe_live_secret_key      ));
$stripe_live_publishable_key  = trim(e($stripe_live_publishable_key ));
$stripe_data_currency         = trim(e($stripe_data_currency        ));
$stripe_currency_symbol       = trim(e($stripe_currency_symbol      ));
$stripe_data_image            = trim(e($stripe_data_image           ));
$stripe_data_description      = trim(e($stripe_data_description     ));

// default lat lng
$default_latlng = "$default_lat,$default_lng";

// default timezone and locale
setlocale(LC_ALL, 'en_US');
date_default_timezone_set($timezone); // see http://php.net/manual/en/timezones.php

// if using sandbox
if($paypal_mode == 1) {
	$paypal_url         = 'https://www.paypal.com/cgi-bin/webscr';
}
// else is production site
else {
	$paypal_url         = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	$paypal_merchant_id = $paypal_sandbox_merch_id;
}

// start session
session_start();

// composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// city cookie management
if(!empty($_COOKIE['city_id']) && empty($_COOKIE['city_name'])) {
	$cookie_city_id = $_COOKIE['city_id'];

	// get city details
	$stmt = $conn->prepare('SELECT * FROM cities WHERE city_id = :city_id');
	$stmt->bindValue(':city_id', $cookie_city_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$cookie_city_name  = $row['city_name'];
	$cookie_city_slug  = $row['slug'];
	$cookie_state_id   = $row['state_id'];
	$cookie_state_abbr = $row['state'];

	// save city details in cookie
	// signature: setcookie(name, value, expire, path, domain, secure, httponly);
	setcookie('city_id',    $cookie_city_id,    time()+86400*90, $install_path);
	setcookie('city_name',  $cookie_city_name,  time()+86400*90, $install_path);
	setcookie('city_slug',  $cookie_city_slug,  time()+86400*90, $install_path);
	setcookie('state_id',   $cookie_state_id,   time()+86400*90, $install_path);
	setcookie('state_abbr', $cookie_state_abbr, time()+86400*90, $install_path);

	// instead of reloading, set cookie manually for this session
	$_COOKIE['city_id']    = $cookie_city_id;
	$_COOKIE['city_name']  = $cookie_city_name;
	$_COOKIE['city_slug']  = $cookie_city_slug;
	$_COOKIE['state_id']   = $cookie_state_id;
	$_COOKIE['state_abbr'] = $cookie_state_abbr;

	// reload so cookies work
	// header("Location: " . $_SERVER['PHP_SELF']);
}

if(!empty($_COOKIE['city_name'])) {
	$cookie_city_name = htmlspecialchars($_COOKIE['city_name']);
}

if(empty($_COOKIE['city_id'])) {
	// if no city_id cookie, delete all city related cookies
	// signature: setcookie(name, value, expire, path, domain, secure, httponly);
	setcookie('city_name',  '', time()-42000, $install_path);
	setcookie('city_slug',  '', time()-42000, $install_path);
	setcookie('state_id',   '', time()-42000, $install_path);
	setcookie('state_abbr', '', time()-42000, $install_path);
}

// user login session and cookie
$userid     = '';
$first_name = '';
$last_name  = '';

// if sessions user_connected and userid exist, query db to get user first name, last name and email.
if(!empty($_SESSION['user_connected']) && !empty($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];

	$stmt = $conn->prepare("SELECT first_name, email, last_name, hybridauth_provider_name FROM users WHERE id = :userid");
	$stmt->bindValue(':userid', $userid);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$first_name               = $row['first_name'];
	$email                    = $row['email'];
	$last_name                = $row['last_name'];
	$hybridauth_provider_name = $row['hybridauth_provider_name'];

	// has session but no corresponding row in users table
	// logout, destroy session and cookies
	if(empty($row)) {
		destroy_session_and_cookie();
	}
}
// if no session, check if it has loggedin cookie
elseif(!empty($_COOKIE['loggedin'])) {
	$_SESSION['user_connected'] = false;

	$loggedin_cookie      = $_COOKIE['loggedin'];
	$cookie_frags         = explode('-', $loggedin_cookie);
	$cookie_userid        = $cookie_frags[0];
	$cookie_provider_name = $cookie_frags[1];
	$cookie_token         = $cookie_frags[2];

	// now delete previous loggedin database entry so we can issue a new one
	$query = "SELECT COUNT(*) AS total_rows FROM loggedin WHERE userid = :userid AND token = :token";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':userid', $cookie_userid);
	$stmt->bindValue(':token', sha1($cookie_token));
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	// if pair cookie/token existed, then user is legit, retrieve name
	if($row['total_rows'] == 1) {
		// delete all tokens for this user
		$query = "DELETE FROM loggedin WHERE userid = :userid";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid', $cookie_userid);
		$stmt->execute();

		$stmt = $conn->prepare("SELECT first_name, email, last_name, hybridauth_provider_name FROM users WHERE id = :userid");
		$stmt->bindValue(':userid', $cookie_userid);
		$user_exist = $stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$first_name               = $row['first_name'];
		$email                    = $row['email'];
		$last_name                = $row['last_name'];
		$hybridauth_provider_name = $row['hybridauth_provider_name'];
		$userid                   = $cookie_userid;

		// create sessions
		$_SESSION['user_connected'] = true;
		$_SESSION['userid'] = $cookie_userid;

		// generate new token and insert userid and token pair into db
		$token = bin2hex(openssl_random_pseudo_bytes(16));
		record_tokens($cookie_userid, $cookie_provider_name, $token);

		// set cookie
		// signature: setcookie(name, value, expire, path, domain, secure, httponly);
		$cookie_val = "$cookie_userid-$cookie_provider_name-$token";
		setcookie('loggedin', $cookie_val, time()+86400*30, $install_path, '', '', true);
	}

	// else pair userid and token doesn't exist in db
	else {
		destroy_session_and_cookie();
	}
}
else {

}

// sanitize user vars
if(!empty($first_name)) {
	$first_name = htmlspecialchars($first_name);
}
if(!empty($email)) {
	$email = htmlspecialchars($email);
}
if(!empty($last_name)) {
	$last_name = htmlspecialchars($last_name);
}

// check if is admin
$is_admin = 0;
if($userid == 1) {
	$is_admin = 1;
}

/*--------------------------------------------------
Pictures folders (legacy check)
--------------------------------------------------*/

if(file_exists(__DIR__ . '/../pictures') || is_dir(__DIR__ . '/../pictures')) {
	$pic_baseurl = $baseurl . '/pictures';
	$pic_basepath = __DIR__ . '/../pictures';

	// place pics
	$place_full_folder    = 'place-full';
	$place_thumb_folder   = 'place-thumb';
	$place_tmp_folder     = 'place-tmp';

	// profile pics
	$profile_full_folder  = 'profile-full';
	$profile_thumb_folder = 'profile-thumb';
	$profile_tmp_folder   = 'profile-tmp';
}

else {
	$pic_baseurl = $baseurl;
	$pic_basepath = __DIR__ . '/..';

	// place pics
	$place_full_folder    = 'place_pic_full';
	$place_thumb_folder   = 'place_pic_thumb';
	$place_tmp_folder     = 'place_pic_tmp';

	// profile pics
	$profile_full_folder  = 'profile_pic_full';
	$profile_thumb_folder = 'profile_pic_thumb';
	$profile_tmp_folder   = 'profile_pic_tmp';
}

// valid image extensions
$img_exts = array('jpg', 'jpeg', 'png', 'gif', 'tif', 'tiff', 'bmp', 'webp');

/*--------------------------------------------------
Language files
--------------------------------------------------*/
$lang_folder = (file_exists(__DIR__ . '/../language/' . $html_lang) || is_dir(__DIR__ . '/../language/' . $html_lang)) ? __DIR__ . '/../language/' . $html_lang : __DIR__ . '/../translations';

require_once($lang_folder . '/__trans-global.php');

switch(basename($_SERVER['SCRIPT_NAME'])) {
	case '_contact.php':
		require_once($lang_folder . '/_trans-contact.php');
		break;

	case '_msg.php':
		require_once($lang_folder . '/_trans-msg.php');
		break;

	case '_privacy.php':
		require_once($lang_folder . '/_trans-privacy.php');
		break;

	case '_searchresults.php':
		require_once($lang_folder . '/_trans-searchresults.php');
		break;

	case 'all-categories.php':
		require_once($lang_folder . '/trans-all-categories.php');
		break;

	case 'index.php':
		require_once($lang_folder . '/trans-index.php');
		break;

	case 'list.php':
		require_once($lang_folder . '/trans-list.php');
		break;

	case 'place.php':
		require_once($lang_folder . '/trans-place.php');
		break;

	// user folder
	case 'login.php':
		require_once($lang_folder . '/user_translations/trans-login.php');
		break;

	case 'logoff.php':
		require_once($lang_folder . '/user_translations/trans-logoff.php');
		break;

	case 'sign-up.php':
		require_once($lang_folder . '/user_translations/trans-sign-up.php');
		break;

	case 'password-request.php':
		require_once($lang_folder . '/user_translations/trans-password-request.php');
		break;

	case 'password-reset.php':
		require_once($lang_folder . '/user_translations/trans-password-reset.php');
		break;

	case 'logoff.php':
		require_once($lang_folder . '/user_translations/trans-logoff.php');
		break;

	case 'profile.php':
		require_once($lang_folder . '/trans-profile.php');
		break;

	case 'signup-confirm.php':
		require_once($lang_folder . '/user_translations/trans-signup-confirm.php');
		break;

	case 'my-places.php':
		require_once($lang_folder . '/user_translations/trans-my-places.php');
		break;

	case 'edit-place.php':
		require_once($lang_folder . '/user_translations/trans-edit-place.php');
		break;

	case 'process-edit-place.php':
		require_once($lang_folder . '/user_translations/trans-process-edit-place.php');
		break;

	case 'my-reviews.php':
		require_once($lang_folder . '/user_translations/trans-my-reviews.php');
		break;

	case 'my-profile.php':
		require_once($lang_folder . '/user_translations/trans-my-profile.php');
		break;

	case 'edit-pass.php':
		require_once($lang_folder . '/user_translations/trans-edit-pass.php');
		break;

	case 'process-edit-pass.php':
		require_once($lang_folder . '/user_translations/trans-process-edit-pass.php');
		break;

	case 'select-plan.php':
		require_once($lang_folder . '/user_translations/trans-select-plan.php');
		break;

	case 'add-place.php':
		require_once($lang_folder . '/user_translations/trans-add-place.php');
		break;

	case 'process-add-place.php':
		require_once($lang_folder . '/user_translations/trans-process-add-place.php');
		break;

	case 'thanks.php':
		require_once($lang_folder . '/user_translations/trans-thanks.php');
		break;
}

// admin folder
if(strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false) {
	require_once($lang_folder . '/admin_translations/_trans-global.php');
}

// v.1.06
if(basename($_SERVER['SCRIPT_NAME']) == 'resend-confirmation.php') {
	if(file_exists($lang_folder . '/user_translations/trans-resend-confirmation.php')) {
		require_once($lang_folder . '/user_translations/trans-resend-confirmation.php');
	}
}

// v.1.08d
if(file_exists(__DIR__ . '/my.functions.php')) {
	require_once(__DIR__ . '/my.functions.php');
}

// v.1.09
$txt_current_location = (!empty($txt_current_location)) ? $txt_current_location : "Use current location";