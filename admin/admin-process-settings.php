<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/iso-639-1.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-settings.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_php.php');

// post vars
$admin_email                  = $_POST['admin_email'];
$dev_email                    = $_POST['dev_email'];
$smtp_server                  = $_POST['smtp_server'];
$smtp_user                    = $_POST['smtp_user'];
$smtp_pass                    = $_POST['smtp_pass'];
$smtp_port                    = $_POST['smtp_port'];
$google_key                   = $_POST['google_key'];
$items_per_page               = $_POST['items_per_page'];
$site_name                    = $_POST['site_name'];
$country_name                 = $_POST['country_name'];
$default_country_code         = $_POST['default_country_code'];
$default_city_slug            = $_POST['default_city_slug'];
$default_loc_id               = $_POST['default_loc_id'];
$timezone                     = $_POST['timezone'];
$default_lat                  = $_POST['default_lat'];
$default_lng                  = $_POST['default_lng'];
$html_lang                    = $_POST['html_lang'];
$max_pics                     = $_POST['max_pics'];
$mail_after_post              = $_POST['mail_after_post'];
$paypal_merchant_id           = $_POST['paypal_merchant_id'];
$paypal_bn                    = $_POST['paypal_bn'];
$paypal_checkout_logo_url     = $_POST['paypal_checkout_logo_url'];
$currency_code                = $_POST['currency_code'];
$currency_symbol              = $_POST['currency_symbol'];
$paypal_locale                = $_POST['paypal_locale'];
$notify_url                   = $_POST['notify_url'];
$paypal_mode                  = (isset($_POST['paypal_mode'])) ? $_POST['paypal_mode'] : -1;
$paypal_sandbox_merch_id      = $_POST['paypal_sandbox_merch_id'];
$facebook_key                 = $_POST['facebook_key'];
$facebook_secret              = $_POST['facebook_secret'];
$twitter_key                  = $_POST['twitter_key'];
$twitter_secret               = $_POST['twitter_secret'];
$_2checkout_mode              = (isset($_POST['_2checkout_mode'])) ? $_POST['_2checkout_mode'] : -1;
$_2checkout_sid               = $_POST['_2checkout_sid'];
$_2checkout_sandbox_sid       = $_POST['_2checkout_sandbox_sid'];
$_2checkout_secret            = $_POST['_2checkout_secret'];
$_2checkout_currency_code     = $_POST['_2checkout_currency_code'];
$_2checkout_currency_symbol   = $_POST['_2checkout_currency_symbol'];
$_2checkout_lang              = $_POST['_2checkout_lang'];
$_2checkout_notify_url        = $_POST['_2checkout_notify_url'];
$_2checkout_notify_url        = $_POST['_2checkout_notify_url'];
$mercadopago_mode             = (isset($_POST['mercadopago_mode'])) ? $_POST['mercadopago_mode'] : -1;
$mercadopago_client_id        = $_POST['mercadopago_client_id'];
$mercadopago_client_secret    = $_POST['mercadopago_client_secret'];
$mercadopago_currency_id      = $_POST['mercadopago_currency_id'];
$mercadopago_notification_url = $_POST['mercadopago_notification_url'];
$stripe_mode                  = (isset($_POST['stripe_mode'])) ? $_POST['stripe_mode'] : -1;
$stripe_test_secret_key       = $_POST['stripe_test_secret_key'];
$stripe_test_publishable_key  = $_POST['stripe_test_publishable_key'];
$stripe_live_secret_key       = $_POST['stripe_live_secret_key'];
$stripe_live_publishable_key  = $_POST['stripe_live_publishable_key'];
$stripe_data_currency         = $_POST['stripe_data_currency'];
$stripe_currency_symbol       = $_POST['stripe_currency_symbol'];
$stripe_data_image            = $_POST['stripe_data_image'];
$stripe_data_description      = $_POST['stripe_data_description'];

// trim
$admin_email                  = trim($admin_email);
$dev_email                    = trim($dev_email);
$smtp_server                  = trim($smtp_server);
$smtp_user                    = trim($smtp_user);
$smtp_pass                    = trim($smtp_pass);
$smtp_port                    = trim($smtp_port);
$google_key                   = trim($google_key);
$items_per_page               = trim($items_per_page);
$site_name                    = trim($site_name);
$country_name                 = trim($country_name);
$default_country_code         = trim($default_country_code);
$default_city_slug            = trim($default_city_slug);
$default_loc_id               = trim($default_loc_id);
$timezone                     = trim($timezone);
$default_lat                  = trim($default_lat);
$default_lng                  = trim($default_lng);
$html_lang                    = trim($html_lang);
$max_pics                     = trim($max_pics);
$mail_after_post              = trim($mail_after_post);
$paypal_merchant_id           = trim($paypal_merchant_id);
$paypal_bn                    = trim($paypal_bn);
$paypal_checkout_logo_url     = trim($paypal_checkout_logo_url);
$currency_code                = trim($currency_code);
$currency_symbol              = trim($currency_symbol);
$paypal_locale                = trim($paypal_locale);
$notify_url                   = trim($notify_url);
$paypal_mode                  = trim($paypal_mode);
$paypal_sandbox_merch_id      = trim($paypal_sandbox_merch_id);
$facebook_key                 = trim($facebook_key);
$facebook_secret              = trim($facebook_secret);
$twitter_key                  = trim($twitter_key);
$twitter_secret               = trim($twitter_secret);
$_2checkout_mode              = trim($_2checkout_mode);
$_2checkout_sid               = trim($_2checkout_sid);
$_2checkout_sandbox_sid       = trim($_2checkout_sandbox_sid);
$_2checkout_secret            = trim($_2checkout_secret);
$_2checkout_currency_code     = trim($_2checkout_currency_code);
$_2checkout_currency_symbol   = trim($_2checkout_currency_symbol);
$_2checkout_lang              = trim($_2checkout_lang);
$_2checkout_notify_url        = trim($_2checkout_notify_url);
$mercadopago_mode             = trim($mercadopago_mode);
$mercadopago_client_id        = trim($mercadopago_client_id);
$mercadopago_client_secret    = trim($mercadopago_client_secret);
$mercadopago_currency_id      = trim($mercadopago_currency_id);
$mercadopago_notification_url = trim($mercadopago_notification_url);
$stripe_mode                  = trim($stripe_mode);
$stripe_test_secret_key       = trim($stripe_test_secret_key);
$stripe_test_publishable_key  = trim($stripe_test_publishable_key);
$stripe_live_secret_key       = trim($stripe_live_secret_key);
$stripe_live_publishable_key  = trim($stripe_live_publishable_key);
$stripe_data_currency         = trim($stripe_data_currency);
$stripe_currency_symbol       = trim($stripe_currency_symbol);
$stripe_data_image            = trim($stripe_data_image);
$stripe_data_description      = trim($stripe_data_description);

//defaults
$admin_email                  = (!empty($admin_email                 )) ? $admin_email                  : 'admin@email.com';
$dev_email                    = (!empty($dev_email                   )) ? $dev_email                    : 'dev@email.com';
$smtp_server                  = (!empty($smtp_server                 )) ? $smtp_server                  : '';
$smtp_user                    = (!empty($smtp_user                   )) ? $smtp_user                    : '';
$smtp_pass                    = (!empty($smtp_pass                   )) ? $smtp_pass                    : '';
$smtp_port                    = (!empty($smtp_port                   )) ? $smtp_port                    : '';
$google_key                   = (!empty($google_key                  )) ? $google_key                   : '';
$items_per_page               = (!empty($items_per_page              )) ? $items_per_page               : '30';
$site_name                    = (!empty($site_name                   )) ? $site_name                    : 'Business Directory';
$country_name                 = (!empty($country_name                )) ? $country_name                 : 'America';
$default_country_code         = (!empty($default_country_code        )) ? $default_country_code         : 'us';
$default_city_slug            = (!empty($default_city_slug           )) ? $default_city_slug            : 'city-slug';
$default_loc_id               = (!empty($default_loc_id              )) ? $default_loc_id               : '1';
$timezone                     = (!empty($timezone                    )) ? $timezone                     : 'America/Los_Angeles';
$default_lat                  = (!empty($default_lat                 )) ? $default_lat                  : '37.3002752813443';
$default_lng                  = (!empty($default_lng                 )) ? $default_lng                  : '-94.482421875';
$html_lang                    = (in_array($html_lang, $iso_639_1     )) ? $html_lang                    : 'en';
$max_pics                     = (!empty($max_pics                    )) ? $max_pics                     : '15';
$mail_after_post              = (!empty($mail_after_post             )) ? $mail_after_post              : '0';
$paypal_merchant_id           = (!empty($paypal_merchant_id          )) ? $paypal_merchant_id           : '';
$paypal_bn                    = (!empty($paypal_bn                   )) ? $paypal_bn                    : '';
$paypal_checkout_logo_url     = (!empty($paypal_checkout_logo_url    )) ? $paypal_checkout_logo_url     : '';
$currency_code                = (!empty($currency_code               )) ? $currency_code                : 'USD';
$currency_symbol              = (!empty($currency_symbol             )) ? $currency_symbol              : '$';
$paypal_locale                = (!empty($paypal_locale               )) ? $paypal_locale                : 'US';
$notify_url                   = (!empty($notify_url                  )) ? $notify_url                   : "$baseurl/ipn-handler.php";
$paypal_sandbox_merch_id      = (!empty($paypal_sandbox_merch_id     )) ? $paypal_sandbox_merch_id      : '';
$facebook_key                 = (!empty($facebook_key                )) ? $facebook_key                 : '';
$facebook_secret              = (!empty($facebook_secret             )) ? $facebook_secret              : '';
$twitter_key                  = (!empty($twitter_key                 )) ? $twitter_key                  : '';
$twitter_secret               = (!empty($twitter_secret              )) ? $twitter_secret               : '';
$_2checkout_sid               = (!empty($_2checkout_sid              )) ? $_2checkout_sid               : '';
$_2checkout_sandbox_sid       = (!empty($_2checkout_sandbox_sid      )) ? $_2checkout_sandbox_sid       : '';
$_2checkout_secret            = (!empty($_2checkout_secret           )) ? $_2checkout_secret            : '';
$_2checkout_currency_code     = (!empty($_2checkout_currency_code    )) ? $_2checkout_currency_code     : '';
$_2checkout_currency_symbol   = (!empty($_2checkout_currency_symbol  )) ? $_2checkout_currency_symbol   : '$';
$_2checkout_lang              = (!empty($_2checkout_lang             )) ? $_2checkout_lang              : '';
$_2checkout_notify_url        = (!empty($_2checkout_notify_url       )) ? $_2checkout_notify_url        : $baseurl . '/ins-2checkout.php';
$mercadopago_client_id        = (!empty($mercadopago_client_id       )) ? $mercadopago_client_id        : '';
$mercadopago_client_secret    = (!empty($mercadopago_client_secret   )) ? $mercadopago_client_secret    : '';
$mercadopago_currency_id      = (!empty($mercadopago_currency_id     )) ? $mercadopago_currency_id      : '';
$mercadopago_notification_url = (!empty($mercadopago_notification_url)) ? $mercadopago_notification_url : '';
$stripe_mode                  = (!empty($stripe_mode                 )) ? $stripe_mode                  : '0';
$stripe_test_secret_key       = (!empty($stripe_test_secret_key      )) ? $stripe_test_secret_key       : '';
$stripe_test_publishable_key  = (!empty($stripe_test_publishable_key )) ? $stripe_test_publishable_key  : '';
$stripe_live_secret_key       = (!empty($stripe_live_secret_key      )) ? $stripe_live_secret_key       : '';
$stripe_live_publishable_key  = (!empty($stripe_live_publishable_key )) ? $stripe_live_publishable_key  : '';
$stripe_data_currency         = (!empty($stripe_data_currency        )) ? $stripe_data_currency         : 'USD';
$stripe_currency_symbol       = (!empty($stripe_currency_symbol      )) ? $stripe_currency_symbol       : '$';
$stripe_data_image            = (!empty($stripe_data_image           )) ? $stripe_data_image            : '';
$stripe_data_description      = (!empty($stripe_data_description     )) ? $stripe_data_description      : '';

// cast type
$smtp_port      = (int)$smtp_port;
$items_per_page = (int)$items_per_page;
$default_loc_id = (int)$default_loc_id;
$max_pics       = (int)$max_pics;

try {
	$conn->beginTransaction();

	// first delete all
	$query = "DELETE FROM config WHERE type <> 'plugin';";
	$stmt = $conn->prepare($query);
	$stmt->execute();

	// reinsert values
	$query = "INSERT INTO config(type, property, value) VALUES
		('email'  , 'admin_email'                 , :admin_email                 ),
		('email'  , 'dev_email'                   , :dev_email                   ),
		('email'  , 'smtp_server'                 , :smtp_server                 ),
		('email'  , 'smtp_user'                   , :smtp_user                   ),
		('email'  , 'smtp_pass'                   , :smtp_pass                   ),
		('email'  , 'smtp_port'                   , :smtp_port                   ),
		('api'    , 'google_key'                  , :google_key                  ),
		('display', 'items_per_page'              , :items_per_page              ),
		('display', 'site_name'                   , :site_name                   ),
		('display', 'country_name'                , :country_name                ),
		('display', 'default_country_code'        , :default_country_code        ),
		('display', 'default_city_slug'           , :default_city_slug           ),
		('display', 'default_loc_id'              , :default_loc_id              ),
		('display', 'timezone'                    , :timezone                    ),
		('maps'   , 'default_lat'                 , :default_lat                 ),
		('maps'   , 'default_lng'                 , :default_lng                 ),
		('display', 'html_lang'                   , :html_lang                   ),
		('display', 'max_pics'                    , :max_pics                    ),
		('email'  , 'mail_after_post'             , :mail_after_post             ),
		('display', 'paypal_merchant_id'          , :paypal_merchant_id          ),
		('display', 'paypal_bn'                   , :paypal_bn                   ),
		('display', 'paypal_checkout_logo_url'    , :paypal_checkout_logo_url    ),
		('payment', 'currency_code'               , :currency_code               ),
		('payment', 'currency_symbol'             , :currency_symbol             ),
		('payment', 'paypal_locale'               , :paypal_locale               ),
		('payment', 'notify_url'                  , :notify_url                  ),
		('payment', 'paypal_mode'                 , :paypal_mode                 ),
		('payment', 'paypal_sandbox_merch_id'     , :paypal_sandbox_merch_id     ),
		('api'    , 'facebook_key'                , :facebook_key                ),
		('api'    , 'facebook_secret'             , :facebook_secret             ),
		('api'    , 'twitter_key'                 , :twitter_key                 ),
		('api'    , 'twitter_secret'              , :twitter_secret              ),
		('payment', '_2checkout_mode'             , :_2checkout_mode             ),
		('payment', '_2checkout_sid'              , :_2checkout_sid              ),
		('payment', '_2checkout_sandbox_sid'      , :_2checkout_sandbox_sid      ),
		('payment', '_2checkout_secret'           , :_2checkout_secret           ),
		('payment', '_2checkout_currency_code'    , :_2checkout_currency_code    ),
		('payment', '_2checkout_currency_symbol'  , :_2checkout_currency_symbol  ),
		('payment', '_2checkout_lang'             , :_2checkout_lang             ),
		('payment', '_2checkout_notify_url'       , :_2checkout_notify_url       ),
		('payment', 'mercadopago_mode'            , :mercadopago_mode            ),
		('payment', 'mercadopago_client_id'       , :mercadopago_client_id       ),
		('payment', 'mercadopago_client_secret'   , :mercadopago_client_secret   ),
		('payment', 'mercadopago_currency_id'     , :mercadopago_currency_id     ),
		('payment', 'mercadopago_notification_url', :mercadopago_notification_url),
		('payment', 'stripe_mode'                 , :stripe_mode                 ),
		('payment', 'stripe_test_secret_key'      , :stripe_test_secret_key      ),
		('payment', 'stripe_test_publishable_key' , :stripe_test_publishable_key ),
		('payment', 'stripe_live_secret_key'      , :stripe_live_secret_key      ),
		('payment', 'stripe_live_publishable_key' , :stripe_live_publishable_key ),
		('payment', 'stripe_data_currency'        , :stripe_data_currency        ),
		('payment', 'stripe_currency_symbol'      , :stripe_currency_symbol      ),
		('payment', 'stripe_data_image'           , :stripe_data_image           ),
		('payment', 'stripe_data_description'     , :stripe_data_description     )
		";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':admin_email'                 , $admin_email);
	$stmt->bindValue(':dev_email'                   , $dev_email);
	$stmt->bindValue(':smtp_server'                 , $smtp_server);
	$stmt->bindValue(':smtp_user'                   , $smtp_user);
	$stmt->bindValue(':smtp_pass'                   , $smtp_pass);
	$stmt->bindValue(':smtp_port'                   , $smtp_port);
	$stmt->bindValue(':google_key'                  , $google_key);
	$stmt->bindValue(':items_per_page'              , $items_per_page);
	$stmt->bindValue(':site_name'                   , $site_name);
	$stmt->bindValue(':country_name'                , $country_name);
	$stmt->bindValue(':default_country_code'        , $default_country_code);
	$stmt->bindValue(':default_city_slug'           , $default_city_slug);
	$stmt->bindValue(':default_loc_id'              , $default_loc_id);
	$stmt->bindValue(':timezone'                    , $timezone);
	$stmt->bindValue(':default_lat'                 , $default_lat);
	$stmt->bindValue(':default_lng'                 , $default_lng);
	$stmt->bindValue(':html_lang'                   , $html_lang);
	$stmt->bindValue(':max_pics'                    , $max_pics);
	$stmt->bindValue(':mail_after_post'             , $mail_after_post);
	$stmt->bindValue(':paypal_merchant_id'          , $paypal_merchant_id);
	$stmt->bindValue(':paypal_bn'                   , $paypal_bn);
	$stmt->bindValue(':paypal_checkout_logo_url'    , $paypal_checkout_logo_url);
	$stmt->bindValue(':currency_code'               , $currency_code);
	$stmt->bindValue(':currency_symbol'             , $currency_symbol);
	$stmt->bindValue(':paypal_locale'               , $paypal_locale);
	$stmt->bindValue(':notify_url'                  , $notify_url);
	$stmt->bindValue(':paypal_mode'                 , $paypal_mode);
	$stmt->bindValue(':paypal_sandbox_merch_id'     , $paypal_sandbox_merch_id);
	$stmt->bindValue(':facebook_key'                , $facebook_key);
	$stmt->bindValue(':facebook_secret'             , $facebook_secret);
	$stmt->bindValue(':twitter_key'                 , $twitter_key);
	$stmt->bindValue(':twitter_secret'              , $twitter_secret);
	$stmt->bindValue(':_2checkout_mode'             , $_2checkout_mode);
	$stmt->bindValue(':_2checkout_sid'              , $_2checkout_sid);
	$stmt->bindValue(':_2checkout_sandbox_sid'      , $_2checkout_sandbox_sid);
	$stmt->bindValue(':_2checkout_secret'           , $_2checkout_secret);
	$stmt->bindValue(':_2checkout_currency_code'    , $_2checkout_currency_code);
	$stmt->bindValue(':_2checkout_currency_symbol'  , $_2checkout_currency_symbol);
	$stmt->bindValue(':_2checkout_lang'             , $_2checkout_lang);
	$stmt->bindValue(':_2checkout_notify_url'       , $_2checkout_notify_url);
	$stmt->bindValue(':mercadopago_mode'            , $mercadopago_mode);
	$stmt->bindValue(':mercadopago_client_id'       , $mercadopago_client_id);
	$stmt->bindValue(':mercadopago_client_secret'   , $mercadopago_client_secret);
	$stmt->bindValue(':mercadopago_currency_id'     , $mercadopago_currency_id);
	$stmt->bindValue(':mercadopago_notification_url', $mercadopago_notification_url);
	$stmt->bindValue(':stripe_mode'                 , $stripe_mode);
	$stmt->bindValue(':stripe_test_secret_key'      , $stripe_test_secret_key);
	$stmt->bindValue(':stripe_test_publishable_key' , $stripe_test_publishable_key);
	$stmt->bindValue(':stripe_live_secret_key'      , $stripe_live_secret_key);
	$stmt->bindValue(':stripe_live_publishable_key' , $stripe_live_publishable_key);
	$stmt->bindValue(':stripe_data_currency'        , $stripe_data_currency);
	$stmt->bindValue(':stripe_currency_symbol'      , $stripe_currency_symbol);
	$stmt->bindValue(':stripe_data_image'           , $stripe_data_image);
	$stmt->bindValue(':stripe_data_description'     , $stripe_data_description);
	$stmt->execute();

	$conn->commit();
	$result_message = $txt_update_success;
}
catch(PDOException $e) {
	$conn->rollBack();
	$result_message =  $e->getMessage();
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body>
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>
	<div class="main-container">
		<h2><span><?= $txt_main_title; ?></span></h2>

		<div class="padding">
			<p><?= $result_message; ?> </p>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

</body>
</html>