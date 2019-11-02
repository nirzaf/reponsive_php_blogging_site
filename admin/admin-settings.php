<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/iso-639-1.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-settings.php');

// valid timezones
$timezone_identifiers = DateTimeZone::listIdentifiers();

// valid paypal locales
$paypal_locale_identifiers = array(
	'AU', 'AT', 'BE', 'BR', 'CA', 'CH', 'CN', 'DE', 'ES', 'GB', 'FR', 'IT', 'NL', 'PL', 'PT', 'RU', 'US', 'da_DK', 'he_IL', 'id_ID', 'ja_JP', 'no_NO', 'pt_BR', 'ru_RU', 'sv_SE', 'th_TH', 'tr_TR', 'zh_CN', 'zh_HK', 'zh_TW');

// radio buttons for paypal
$checked_live     = '';
$checked_sandbox  = '';
$checked_disabled = '';
if($paypal_mode == 1)  $checked_live     = 'checked';
if($paypal_mode == 0)  $checked_sandbox  = 'checked';
if($paypal_mode == -1) $checked_disabled = 'checked';

// radio buttons for 2checkout
$_2checkout_checked_live     = '';
$_2checkout_checked_sandbox  = '';
$_2checkout_checked_disabled = '';

if($_2checkout_mode == 1)  $_2checkout_checked_live     = 'checked';
if($_2checkout_mode == 0)  $_2checkout_checked_sandbox  = 'checked';
if($_2checkout_mode == -1) $_2checkout_checked_disabled = 'checked';

// radio buttons for mercadopago
$mercadopago_checked_live     = '';
$mercadopago_checked_sandbox  = '';
$mercadopago_checked_disabled = '';

if($mercadopago_mode == 1)  $mercadopago_checked_live     = 'checked';
if($mercadopago_mode == 0)  $mercadopago_checked_sandbox  = 'checked';
if($mercadopago_mode == -1) $mercadopago_checked_disabled = 'checked';

// radio buttons for stripe
$stripe_checked_live     = '';
$stripe_checked_sandbox  = '';
$stripe_checked_disabled = '';
if($stripe_mode == 1)  $stripe_checked_live     = 'checked';
if($stripe_mode == 0)  $stripe_checked_sandbox  = 'checked';
if($stripe_mode == -1) $stripe_checked_disabled = 'checked';

// translation var check if exists, if not, set default
// v. 1.06
$txt_paypal_header         = (!empty($txt_paypal_header        )) ? $txt_paypal_header          : "Paypal Settings";
$txt_gateway_mode          = (!empty($txt_gateway_mode         )) ? $txt_gateway_mode           : "Mode";
$txt_gateway_currency      = (!empty($txt_gateway_currency     )) ? $txt_gateway_currency       : "Currency ";
$txt_2checkout_header      = (!empty($txt_2checkout_header     )) ? $txt_2checkout_header       : "2Checkout Settings";
$txt_2checkout_sid         = (!empty($txt_2checkout_sid        )) ? $txt_2checkout_sid          : "2checkout SID";
$txt_2checkout_sandbox_sid = (!empty($txt_2checkout_sandbox_sid)) ? $txt_2checkout_sandbox_sid  : "2checkout Sandbox SID";
$txt_2checkout_secret      = (!empty($txt_2checkout_secret     )) ? $txt_2checkout_secret       : "2checkout Secret Word";
$txt_2checkout_lang        = (!empty($txt_2checkout_lang       )) ? $txt_2checkout_lang         : "2Checkout Language";
$txt_2checkout_notify_url  = (!empty($txt_2checkout_notify_url )) ? $txt_2checkout_notify_url   : "2Checkout Global URL (Notifications)";
$txt_mercadopago_header    = (!empty($txt_mercadopago_header   )) ? $txt_mercadopago_header     : "MercadoPago Settings";

// translation var check if exists, if not, set default
// v. 1.08
$txt_stripe_header         = (!empty($txt_stripe_header        )) ? $txt_stripe_header          : "Stripe Settings";
$txt_stripe_test_mode      = (!empty($txt_stripe_test_mode     )) ? $txt_stripe_test_mode       : "Test";
$txt_test_secret_key       = (!empty($txt_test_secret_key      )) ? $txt_test_secret_key        : "Test Secret Key";
$txt_test_publishable_key  = (!empty($txt_test_publishable_key )) ? $txt_test_publishable_key   : "Test Publishable Key";
$txt_live_secret_key       = (!empty($txt_live_secret_key      )) ? $txt_live_secret_key        : "Live Secret Key";
$txt_live_publishable_key  = (!empty($txt_live_publishable_key )) ? $txt_live_publishable_key   : "Live Publishable Key";
$txt_stripe_currency_code  = (!empty($txt_stripe_currency_code )) ? $txt_stripe_currency_code   : "3-Letter ISO Code";
$txt_mail_after_post       = (!empty($txt_mail_after_post      )) ? $txt_mail_after_post        : "Receive notification on post/edit listing?";
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
<body class="admin-settings">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once('_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<ul class="nav nav-tabs" role="tablist" id="settingsTabs">
					<li role="presentation" class="active">
						<a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?= $txt_tab_general; ?></a>
					</li>
					<li role="presentation">
						<a href="#email" aria-controls="email" role="tab" data-toggle="tab"><?= $txt_tab_email; ?></a>
					</li>
					<li role="presentation">
						<a href="#apis" aria-controls="apis" role="tab" data-toggle="tab"><?= $txt_tab_apis; ?></a>
					</li>
					<li role="presentation" class="dropdown">
						<a href="#" aria-controls="payment-dropdown-list" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents"><?= $txt_tab_payment; ?><span class="caret"></span></a>
						<ul class="dropdown-menu" aria-labelledby="myTabDrop1" id="payment-dropdown-list">
							<li class="">
								<a href="#payment-paypal" role="tab" data-toggle="tab" aria-controls="payment-paypal" aria-expanded="false">Paypal</a>
							</li>
							<li class="">
								<a href="#payment-stripe" role="tab" data-toggle="tab" aria-controls="payment-stripe" aria-expanded="false">Stripe</a>
							</li>
							<li class="">
								<a href="#payment-2checkout" role="tab" data-toggle="tab" aria-controls="payment-2checkout" aria-expanded="false">2Checkout</a>
							</li>
							<li class="">
								<a href="#payment-mercadopago" role="tab" data-toggle="tab" aria-controls="payment-mercadopago" aria-expanded="false">MercadoPago</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>

			<!-- Tab panes -->
			<form method="post" action="admin-process-settings.php">
				<input type="hidden" name="csrf_token" value="<?= session_id(); ?>">
				<div class="block tab-content">
					<!-- GENERAL SETTINGS PANEL -->
					<div role="tabpanel" class="tab-pane active" id="general">
						<div class="form-row">
							<strong><?= $txt_site_name; ?></strong>
							<input type="text" id="site_name" name="site_name" class="form-control" value="<?= $site_name; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_html_lang; ?></strong><br>
							<?= $txt_html_lang_explain; ?>

							<select id="html_lang" name="html_lang" class="form-control">
								<?php
								foreach($iso_639_1 as $v) {
									$selected = '';
									if($v == $html_lang) $selected = 'selected'
									?>
									<option value="<?= $v; ?>" <?= $selected; ?>><?= $v; ?></option>
									<?php
								}
								?>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_country_name; ?></strong><br>
							<?= $txt_country_name_explain; ?>
							<input type="text" id="country_name" name="country_name" class="form-control" value="<?= $country_name; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_country_code; ?></strong><br>
							<a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2" target="_blank"><?= $txt_country_code_explain; ?></a>
							<input type="text" id="default_country_code" name="default_country_code" class="form-control" value="<?= $default_country_code; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_default_city_id; ?></strong><br>
							<?= $txt_default_city_id_explain; ?>
							<input type="text" id="default_loc_id" name="default_loc_id" class="form-control" value="<?= $default_loc_id; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_default_city_slug; ?></strong><br>
							<?= $txt_default_city_slug_explain; ?>
							<input type="text" id="default_city_slug" name="default_city_slug" class="form-control" value="<?= $default_city_slug; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_items_per_page; ?></strong><br>
							<?= $txt_items_per_page_explain; ?>
							<input type="text" id="items_per_page" name="items_per_page" class="form-control" value="<?= $items_per_page; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_max_pics; ?></strong><br>
							<?= $txt_max_pics_explain; ?>
							<input type="text" id="max_pics" name="max_pics" class="form-control" value="<?= $max_pics; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_mail_after_post; ?></strong><br>
							<select id="mail_after_post" name="mail_after_post" class="form-control">
								<option value="1" <?php if ($mail_after_post == 1) echo 'selected'; ?>><?= $txt_yes; ?></option>
								<option value="0" <?php if ($mail_after_post == 0) echo 'selected'; ?>><?= $txt_no; ?></option>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_timezone; ?></strong><br>
							<?= $txt_timezone_explain; ?>
							<select id="timezone" name="timezone" class="form-control">
								<?php
								foreach($timezone_identifiers as $v) {
									$selected = '';
									if($v == $timezone) $selected = 'selected'
									?>
									<option value="<?= $v; ?>" <?= $selected; ?>><?= $v; ?></option>
									<?php
								}
								?>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_default_lat; ?></strong><br>
							<input type="text" id="default_lat" name="default_lat" class="form-control" value="<?= $default_lat; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_default_lng; ?></strong><br>
							<input type="text" id="default_lng" name="default_lng" class="form-control" value="<?= $default_lng; ?>">
						</div>
					</div><!-- <div role="tabpanel" class="tab-pane active" id="general"> -->

					<!-- EMAIL SETTINGS PANEL -->
					<div role="tabpanel" class="tab-pane" id="email">
						<div class="form-row">
							<strong><?= $txt_admin_email; ?></strong>
							<input type="text" id="admin_email" name="admin_email" class="form-control" value="<?= $admin_email; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_dev_email; ?></strong>
							<input type="text" id="dev_email" name="dev_email" class="form-control" value="<?= $dev_email; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_smtp_server; ?></strong>
							<input type="text" id="smtp_server" name="smtp_server" class="form-control" value="<?= $smtp_server; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_smtp_user; ?></strong>
							<input type="text" id="smtp_user" name="smtp_user" class="form-control" value="<?= $smtp_user; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_smtp_pass; ?></strong>
							<input type="password" id="smtp_pass" name="smtp_pass" class="form-control" value="<?= $smtp_pass; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_smtp_port; ?></strong>
							<input type="text" id="smtp_port" name="smtp_port" class="form-control" value="<?= $smtp_port; ?>">
						</div>
					</div>

					<!-- APIS SETTINGS PANEL -->
					<div role="tabpanel" class="tab-pane" id="apis">
						<div class="form-row">
							<strong><?= $txt_gmaps_key; ?></strong><br>
							<?= $txt_gmaps_key_explain; ?>
							<input type="password" id="google_key" name="google_key" class="form-control" value="<?= $google_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_facebook_key; ?></strong><br>
							<?= $txt_facebook_key_explain; ?>
							<input type="text" id="facebook_key" name="facebook_key" class="form-control" value="<?= $facebook_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_facebook_secret; ?></strong><br>
							<input type="password" id="facebook_secret" name="facebook_secret" class="form-control" value="<?= $facebook_secret; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_twitter_key; ?></strong><br>
							<?= $txt_twitter_key_explain; ?>
							<input type="text" id="twitter_key" name="twitter_key" class="form-control" value="<?= $twitter_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_twitter_secret; ?></strong><br>
							<input type="password" id="twitter_secret" name="twitter_secret" class="form-control" value="<?= $twitter_secret; ?>">
						</div>
					</div><!-- .tab-pane -->

					<!-- PAYMENT TABS -->
					<!-- PAYPAL PANEL -->
					<div role="tabpanel" class="tab-pane" id="payment-paypal">
						<p><strong><?= $txt_paypal_header; ?></strong></p>
						<div class="form-row">
							<strong><?= $txt_paypal_mode; ?></strong><br>
							<input type="radio" id="paypal_mode_live" name="paypal_mode" value="1" <?= $checked_live; ?>>
							<label for="paypal_mode_live"><?= $txt_live; ?></label><br>
							<input type="radio" id="paypal_mode_sandbox" name="paypal_mode" value="0" <?= $checked_sandbox; ?>>
							<label for="paypal_mode_sandbox"><?= $txt_sandbox; ?></label>
							<br>
							<input type="radio" id="paypal_mode_disabled" name="paypal_mode" value="-1" <?= $checked_disabled; ?>>
							<label for="paypal_mode_disabled"><?php echo ucfirst($txt_disabled); ?></label>
						</div>

						<div class="form-row">
							<strong><?= $txt_paypal_merchant_id; ?></strong><br>
							<input type="text" id="paypal_merchant_id" name="paypal_merchant_id" class="form-control" value="<?= $paypal_merchant_id; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_paypal_sandbox_merch_id; ?></strong><br>
							<input type="text" id="paypal_sandbox_merch_id" name="paypal_sandbox_merch_id" class="form-control" value="<?= $paypal_sandbox_merch_id; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_paypal_bn; ?></strong><br>
							<?= $txt_paypal_bn_explain; ?>
							<input type="text" id="paypal_bn" name="paypal_bn" class="form-control" value="<?= $paypal_bn; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_paypal_checkout_logo_url; ?></strong><br>
							<?= $txt_paypal_checkout_logo_url_explain; ?>
							<input type="text" id="paypal_checkout_logo_url" name="paypal_checkout_logo_url" class="form-control" value="<?= $paypal_checkout_logo_url; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_currency_code; ?></strong><br>
							<?= $txt_currency_code_explain; ?>
							<input type="text" id="currency_code" name="currency_code" class="form-control" value="<?= $currency_code; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_currency_symbol; ?></strong><br>
							<input type="text" id="currency_symbol" name="currency_symbol" class="form-control" value="<?= $currency_symbol; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_paypal_locale; ?></strong><br>
							<select id="paypal_locale" name="paypal_locale" class="form-control">
								<?php
								foreach($paypal_locale_identifiers as $v) {
									$selected = '';
									if($v == $paypal_locale) $selected = 'selected'
									?>
									<option value="<?= $v; ?>" <?= $selected; ?>><?= $v; ?></option>
									<?php
								}
								?>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_notify_url; ?></strong><br>
							<?= $txt_notify_url_explain; ?><br>
							<input type="text" id="notify_url" name="notify_url" class="form-control" value="<?= $notify_url; ?>">
						</div>
					</div><!-- .tab-pane #payment-paypal -->

					<!-- STRIPE PANEL -->
					<div role="tabpanel" class="tab-pane" id="payment-stripe">
						<p><strong><?= $txt_stripe_header; ?></strong></p>
						<div class="form-row">
							<strong><?= $txt_gateway_mode; ?></strong><br>
							<input type="radio" id="stripe_mode_live" name="stripe_mode" value="1" <?= $stripe_checked_live; ?>>
							<label for="stripe_mode_live"><?= $txt_live; ?></label><br>
							<input type="radio" id="stripe_mode_test" name="stripe_mode" value="0" <?= $stripe_checked_sandbox; ?>>
							<label for="stripe_mode_test"><?= $txt_stripe_test_mode; ?></label><br>
							<input type="radio" id="stripe_mode_disabled" name="stripe_mode" value="-1" <?= $stripe_checked_disabled; ?>>
							<label for="stripe_mode_disabled"><?php echo ucfirst($txt_disabled); ?></label>
						</div>

						<div class="form-row">
							<strong><?= $txt_test_secret_key; ?></strong><br>
							<input type="text" id="stripe_test_secret_key" name="stripe_test_secret_key" class="form-control" value="<?= $stripe_test_secret_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_test_publishable_key; ?></strong><br>
							<input type="text" id="stripe_test_publishable_key" name="stripe_test_publishable_key" class="form-control" value="<?= $stripe_test_publishable_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_live_secret_key; ?></strong><br>
							<input type="text" id="stripe_live_secret_key" name="stripe_live_secret_key" class="form-control" value="<?= $stripe_live_secret_key; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_live_publishable_key; ?></strong><br>
							<input type="text" id="stripe_live_publishable_key" name="stripe_live_publishable_key" class="form-control" value="<?= $stripe_live_publishable_key; ?>">
						</div>

						<div class="form-row">
							<strong>(data-currency) [<a href="https://support.stripe.com/questions/which-currencies-does-stripe-support" target="_blank"><?= $txt_stripe_currency_code; ?></a>]</strong><br>
							<input type="text" id="stripe_data_currency" name="stripe_data_currency" class="form-control" value="<?= $stripe_data_currency; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_currency_symbol; ?></strong><br>
							<input type="text" id="stripe_currency_symbol" name="stripe_currency_symbol" class="form-control" value="<?= $stripe_currency_symbol; ?>">
						</div>

						<div class="form-row">
							<strong>(data-image)</strong><br>
							<input type="text" id="stripe_data_image" name="stripe_data_image" class="form-control" value="<?= $stripe_data_image; ?>">
						</div>

						<div class="form-row">
							<strong>(data-description)</strong><br>
							<input type="text" id="stripe_data_description" name="stripe_data_description" class="form-control" value="<?= $stripe_data_description; ?>">
						</div>
					</div><!-- .tab-pane #payment-stripe -->

					<!-- 2CHECKOUT PANEL -->
					<div role="tabpanel" class="tab-pane" id="payment-2checkout">
						<p><strong><?= $txt_2checkout_header; ?></strong></p>
						<div class="form-row">
							<strong><?= $txt_gateway_mode; ?></strong><br>
							<input type="radio" id="_2checkout_mode_live" name="_2checkout_mode" value="1"
							<?= $_2checkout_checked_live; ?>>
							<label for="_2checkout_mode_live"><?= $txt_live; ?></label>
							<br>
							<input type="radio" id="_2checkout_mode_sandbox" name="_2checkout_mode" value="0" <?= $_2checkout_checked_sandbox; ?>>
							<label for="_2checkout_mode_sandbox"><?= $txt_sandbox; ?></label>
							<br>
							<input type="radio" id="_2checkout_mode_disabled" name="_2checkout_mode" value="-1" <?= $_2checkout_checked_disabled; ?>>
							<label for="_2checkout_mode_sandbox"><?php echo ucfirst($txt_disabled); ?></label>
						</div>

						<div class="form-row">
							<strong><?= $txt_2checkout_sid; ?></strong><br>
							<input type="text" id="_2checkout_sid" name="_2checkout_sid" class="form-control" value="<?= $_2checkout_sid; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_2checkout_sandbox_sid; ?></strong><br>
							<input type="text" id="_2checkout_sandbox_sid" name="_2checkout_sandbox_sid" class="form-control" value="<?= $_2checkout_sandbox_sid; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_2checkout_secret; ?></strong><br>
							<input type="text" id="_2checkout_secret" name="_2checkout_secret" class="form-control" value="<?= $_2checkout_secret; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_currency_code; ?></strong><br>
							<select name="_2checkout_currency_code" class="form-control">
								<option value="AED" <?php echo ($_2checkout_currency_code == 'AED') ? 'selected' : ''; ?>>AED</option>
								<option value="AFN" <?php echo ($_2checkout_currency_code == 'AFN') ? 'selected' : ''; ?>>AFN</option>
								<option value="ALL" <?php echo ($_2checkout_currency_code == 'ALL') ? 'selected' : ''; ?>>ALL</option>
								<option value="ARS" <?php echo ($_2checkout_currency_code == 'ARS') ? 'selected' : ''; ?>>ARS</option>
								<option value="AUD" <?php echo ($_2checkout_currency_code == 'AUD') ? 'selected' : ''; ?>>AUD</option>
								<option value="AZN" <?php echo ($_2checkout_currency_code == 'AZN') ? 'selected' : ''; ?>>AZN</option>
								<option value="BBD" <?php echo ($_2checkout_currency_code == 'BBD') ? 'selected' : ''; ?>>BBD</option>
								<option value="BDT" <?php echo ($_2checkout_currency_code == 'BDT') ? 'selected' : ''; ?>>BDT</option>
								<option value="BGN" <?php echo ($_2checkout_currency_code == 'BGN') ? 'selected' : ''; ?>>BGN</option>
								<option value="BMD" <?php echo ($_2checkout_currency_code == 'BMD') ? 'selected' : ''; ?>>BMD</option>
								<option value="BND" <?php echo ($_2checkout_currency_code == 'BND') ? 'selected' : ''; ?>>BND</option>
								<option value="BOB" <?php echo ($_2checkout_currency_code == 'BOB') ? 'selected' : ''; ?>>BOB</option>
								<option value="BRL" <?php echo ($_2checkout_currency_code == 'BRL') ? 'selected' : ''; ?>>BRL</option>
								<option value="BSD" <?php echo ($_2checkout_currency_code == 'BSD') ? 'selected' : ''; ?>>BSD</option>
								<option value="BWP" <?php echo ($_2checkout_currency_code == 'BWP') ? 'selected' : ''; ?>>BWP</option>
								<option value="BZD" <?php echo ($_2checkout_currency_code == 'BZD') ? 'selected' : ''; ?>>BZD</option>
								<option value="CAD" <?php echo ($_2checkout_currency_code == 'CAD') ? 'selected' : ''; ?>>CAD</option>
								<option value="CHF" <?php echo ($_2checkout_currency_code == 'CHF') ? 'selected' : ''; ?>>CHF</option>
								<option value="CLP" <?php echo ($_2checkout_currency_code == 'CLP') ? 'selected' : ''; ?>>CLP</option>
								<option value="CNY" <?php echo ($_2checkout_currency_code == 'CNY') ? 'selected' : ''; ?>>CNY</option>
								<option value="COP" <?php echo ($_2checkout_currency_code == 'COP') ? 'selected' : ''; ?>>COP</option>
								<option value="CRC" <?php echo ($_2checkout_currency_code == 'CRC') ? 'selected' : ''; ?>>CRC</option>
								<option value="CZK" <?php echo ($_2checkout_currency_code == 'CZK') ? 'selected' : ''; ?>>CZK</option>
								<option value="DKK" <?php echo ($_2checkout_currency_code == 'DKK') ? 'selected' : ''; ?>>DKK</option>
								<option value="DOP" <?php echo ($_2checkout_currency_code == 'DOP') ? 'selected' : ''; ?>>DOP</option>
								<option value="DZD" <?php echo ($_2checkout_currency_code == 'DZD') ? 'selected' : ''; ?>>DZD</option>
								<option value="EGP" <?php echo ($_2checkout_currency_code == 'EGP') ? 'selected' : ''; ?>>EGP</option>
								<option value="EUR" <?php echo ($_2checkout_currency_code == 'EUR') ? 'selected' : ''; ?>>EUR</option>
								<option value="FJD" <?php echo ($_2checkout_currency_code == 'FJD') ? 'selected' : ''; ?>>FJD</option>
								<option value="GBP" <?php echo ($_2checkout_currency_code == 'GBP') ? 'selected' : ''; ?>>GBP</option>
								<option value="GTQ" <?php echo ($_2checkout_currency_code == 'GTQ') ? 'selected' : ''; ?>>GTQ</option>
								<option value="HKD" <?php echo ($_2checkout_currency_code == 'HKD') ? 'selected' : ''; ?>>HKD</option>
								<option value="HNL" <?php echo ($_2checkout_currency_code == 'HNL') ? 'selected' : ''; ?>>HNL</option>
								<option value="HRK" <?php echo ($_2checkout_currency_code == 'HRK') ? 'selected' : ''; ?>>HRK</option>
								<option value="HUF" <?php echo ($_2checkout_currency_code == 'HUF') ? 'selected' : ''; ?>>HUF</option>
								<option value="IDR" <?php echo ($_2checkout_currency_code == 'IDR') ? 'selected' : ''; ?>>IDR</option>
								<option value="ILS" <?php echo ($_2checkout_currency_code == 'ILS') ? 'selected' : ''; ?>>ILS</option>
								<option value="INR" <?php echo ($_2checkout_currency_code == 'INR') ? 'selected' : ''; ?>>INR</option>
								<option value="JMD" <?php echo ($_2checkout_currency_code == 'JMD') ? 'selected' : ''; ?>>JMD</option>
								<option value="JPY" <?php echo ($_2checkout_currency_code == 'JPY') ? 'selected' : ''; ?>>JPY</option>
								<option value="KES" <?php echo ($_2checkout_currency_code == 'KES') ? 'selected' : ''; ?>>KES</option>
								<option value="KRW" <?php echo ($_2checkout_currency_code == 'KRW') ? 'selected' : ''; ?>>KRW</option>
								<option value="KZT" <?php echo ($_2checkout_currency_code == 'KZT') ? 'selected' : ''; ?>>KZT</option>
								<option value="LAK" <?php echo ($_2checkout_currency_code == 'LAK') ? 'selected' : ''; ?>>LAK</option>
								<option value="LBP" <?php echo ($_2checkout_currency_code == 'LBP') ? 'selected' : ''; ?>>LBP</option>
								<option value="LKR" <?php echo ($_2checkout_currency_code == 'LKR') ? 'selected' : ''; ?>>LKR</option>
								<option value="LRD" <?php echo ($_2checkout_currency_code == 'LRD') ? 'selected' : ''; ?>>LRD</option>
								<option value="MAD" <?php echo ($_2checkout_currency_code == 'MAD') ? 'selected' : ''; ?>>MAD</option>
								<option value="MMK" <?php echo ($_2checkout_currency_code == 'MMK') ? 'selected' : ''; ?>>MMK</option>
								<option value="MOP" <?php echo ($_2checkout_currency_code == 'MOP') ? 'selected' : ''; ?>>MOP</option>
								<option value="MRO" <?php echo ($_2checkout_currency_code == 'MRO') ? 'selected' : ''; ?>>MRO</option>
								<option value="MUR" <?php echo ($_2checkout_currency_code == 'MUR') ? 'selected' : ''; ?>>MUR</option>
								<option value="MVR" <?php echo ($_2checkout_currency_code == 'MVR') ? 'selected' : ''; ?>>MVR</option>
								<option value="MXN" <?php echo ($_2checkout_currency_code == 'MXN') ? 'selected' : ''; ?>>MXN</option>
								<option value="MYR" <?php echo ($_2checkout_currency_code == 'MYR') ? 'selected' : ''; ?>>MYR</option>
								<option value="NIO" <?php echo ($_2checkout_currency_code == 'NIO') ? 'selected' : ''; ?>>NIO</option>
								<option value="NOK" <?php echo ($_2checkout_currency_code == 'NOK') ? 'selected' : ''; ?>>NOK</option>
								<option value="NPR" <?php echo ($_2checkout_currency_code == 'NPR') ? 'selected' : ''; ?>>NPR</option>
								<option value="NZD" <?php echo ($_2checkout_currency_code == 'NZD') ? 'selected' : ''; ?>>NZD</option>
								<option value="PEN" <?php echo ($_2checkout_currency_code == 'PEN') ? 'selected' : ''; ?>>PEN</option>
								<option value="PGK" <?php echo ($_2checkout_currency_code == 'PGK') ? 'selected' : ''; ?>>PGK</option>
								<option value="PHP" <?php echo ($_2checkout_currency_code == 'PHP') ? 'selected' : ''; ?>>PHP</option>
								<option value="PKR" <?php echo ($_2checkout_currency_code == 'PKR') ? 'selected' : ''; ?>>PKR</option>
								<option value="PLN" <?php echo ($_2checkout_currency_code == 'PLN') ? 'selected' : ''; ?>>PLN</option>
								<option value="QAR" <?php echo ($_2checkout_currency_code == 'QAR') ? 'selected' : ''; ?>>QAR</option>
								<option value="RON" <?php echo ($_2checkout_currency_code == 'RON') ? 'selected' : ''; ?>>RON</option>
								<option value="RUB" <?php echo ($_2checkout_currency_code == 'RUB') ? 'selected' : ''; ?>>RUB</option>
								<option value="SAR" <?php echo ($_2checkout_currency_code == 'SAR') ? 'selected' : ''; ?>>SAR</option>
								<option value="SBD" <?php echo ($_2checkout_currency_code == 'SBD') ? 'selected' : ''; ?>>SBD</option>
								<option value="SCR" <?php echo ($_2checkout_currency_code == 'SCR') ? 'selected' : ''; ?>>SCR</option>
								<option value="SEK" <?php echo ($_2checkout_currency_code == 'SEK') ? 'selected' : ''; ?>>SEK</option>
								<option value="SGD" <?php echo ($_2checkout_currency_code == 'SGD') ? 'selected' : ''; ?>>SGD</option>
								<option value="SYP" <?php echo ($_2checkout_currency_code == 'SYP') ? 'selected' : ''; ?>>SYP</option>
								<option value="THB" <?php echo ($_2checkout_currency_code == 'THB') ? 'selected' : ''; ?>>THB</option>
								<option value="TOP" <?php echo ($_2checkout_currency_code == 'TOP') ? 'selected' : ''; ?>>TOP</option>
								<option value="TRY" <?php echo ($_2checkout_currency_code == 'TRY') ? 'selected' : ''; ?>>TRY</option>
								<option value="TTD" <?php echo ($_2checkout_currency_code == 'TTD') ? 'selected' : ''; ?>>TTD</option>
								<option value="TWD" <?php echo ($_2checkout_currency_code == 'TWD') ? 'selected' : ''; ?>>TWD</option>
								<option value="UAH" <?php echo ($_2checkout_currency_code == 'UAH') ? 'selected' : ''; ?>>UAH</option>
								<option value="USD" <?php echo ($_2checkout_currency_code == 'USD') ? 'selected' : ''; ?>>USD</option>
								<option value="VND" <?php echo ($_2checkout_currency_code == 'VND') ? 'selected' : ''; ?>>VND</option>
								<option value="VUV" <?php echo ($_2checkout_currency_code == 'VUV') ? 'selected' : ''; ?>>VUV</option>
								<option value="WST" <?php echo ($_2checkout_currency_code == 'WST') ? 'selected' : ''; ?>>WST</option>
								<option value="XCD" <?php echo ($_2checkout_currency_code == 'XCD') ? 'selected' : ''; ?>>XCD</option>
								<option value="XOF" <?php echo ($_2checkout_currency_code == 'XOF') ? 'selected' : ''; ?>>XOF</option>
								<option value="YER" <?php echo ($_2checkout_currency_code == 'YER') ? 'selected' : ''; ?>>YER</option>
								<option value="ZAR" <?php echo ($_2checkout_currency_code == 'ZAR') ? 'selected' : ''; ?>>ZAR</option>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_currency_symbol; ?></strong><br>
							<input type="text" id="_2checkout_currency_symbol" name="_2checkout_currency_symbol" class="form-control" value="<?= $_2checkout_currency_symbol; ?>">
						</div>

						<div class="form-row">
							<strong><?= $txt_2checkout_lang; ?></strong><br>
							<select id="_2checkout_lang" name="_2checkout_lang" class="form-control">
								<option value="en"    <?php echo ($_2checkout_lang == 'en')    ? 'selected' : ''; ?>>en</option>
								<option value="zh"    <?php echo ($_2checkout_lang == 'zh')    ? 'selected' : ''; ?>>zh</option>
								<option value="da"    <?php echo ($_2checkout_lang == 'da')    ? 'selected' : ''; ?>>da</option>
								<option value="nl"    <?php echo ($_2checkout_lang == 'nl')    ? 'selected' : ''; ?>>nl</option>
								<option value="fr"    <?php echo ($_2checkout_lang == 'fr')    ? 'selected' : ''; ?>>fr</option>
								<option value="gr"    <?php echo ($_2checkout_lang == 'gr')    ? 'selected' : ''; ?>>gr</option>
								<option value="el"    <?php echo ($_2checkout_lang == 'el')    ? 'selected' : ''; ?>>el</option>
								<option value="it"    <?php echo ($_2checkout_lang == 'it')    ? 'selected' : ''; ?>>it</option>
								<option value="jp"    <?php echo ($_2checkout_lang == 'jp')    ? 'selected' : ''; ?>>jp</option>
								<option value="no"    <?php echo ($_2checkout_lang == 'no')    ? 'selected' : ''; ?>>no</option>
								<option value="pt"    <?php echo ($_2checkout_lang == 'pt')    ? 'selected' : ''; ?>>pt</option>
								<option value="sl"    <?php echo ($_2checkout_lang == 'sl')    ? 'selected' : ''; ?>>sl</option>
								<option value="es_ib" <?php echo ($_2checkout_lang == 'es_ib') ? 'selected' : ''; ?>>es_ib</option>
								<option value="es_la" <?php echo ($_2checkout_lang == 'es_la') ? 'selected' : ''; ?>>es_la</option>
								<option value="sv"    <?php echo ($_2checkout_lang == 'sv')    ? 'selected' : ''; ?>>sv</option>
							</select>
						</div>

						<div class="form-row">
							<strong><?= $txt_2checkout_notify_url; ?></strong><br>
							<input type="text" id="_2checkout_notify_url" name="_2checkout_notify_url" class="form-control" value="<?= $_2checkout_notify_url; ?>">
						</div>
					</div><!-- .tab-pane #payment-2checkout -->

					<!-- MERCADOPAGO PANEL -->
					<div role="tabpanel" class="tab-pane" id="payment-mercadopago">
						<p><strong><?= $txt_mercadopago_header; ?></strong></p>
						<div class="form-row">
							<strong><?= $txt_gateway_mode; ?></strong><br>
							<input type="radio" id="mercadopago_mode_live" name="mercadopago_mode" value="1" <?= $mercadopago_checked_live; ?>>
							<label for="mercadopago_mode_live"><?= $txt_live; ?></label><br>
							<input type="radio" id="mercadopago_mode_disabled" name="mercadopago_mode" value="-1" <?= $mercadopago_checked_disabled; ?>>
							<label for="mercadopago_mode_disabled"><?php echo ucfirst($txt_disabled); ?></label>
						</div>

						<div class="form-row">
							<strong>CLIENT_ID</strong><br>
							<input type="text" id="mercadopago_client_id" name="mercadopago_client_id" class="form-control" value="<?= $mercadopago_client_id; ?>">
						</div>

						<div class="form-row">
							<strong>CLIENT_SECRET</strong><br>
							<input type="text" id="mercadopago_client_secret" name="mercadopago_client_secret" class="form-control" value="<?= $mercadopago_client_secret; ?>">
						</div>

						<div class="form-row">
							<strong>currency_id</strong><br>
							<select id="mercadopago_currency_id" name="mercadopago_currency_id" class="form-control">
								<option value="PAB" <?php echo ($mercadopago_currency_id == 'PAB') ? 'selected' : '' ?>>Balboa                  </option>
								<option value="BOB" <?php echo ($mercadopago_currency_id == 'BOB') ? 'selected' : '' ?>>Boliviano               </option>
								<option value="VEF" <?php echo ($mercadopago_currency_id == 'VEF') ? 'selected' : '' ?>>Bolivar fuerte          </option>
								<option value="NIO" <?php echo ($mercadopago_currency_id == 'NIO') ? 'selected' : '' ?>>Córdoba                 </option>
								<option value="CRC" <?php echo ($mercadopago_currency_id == 'CRC') ? 'selected' : '' ?>>Colones                 </option>
								<option value="USD" <?php echo ($mercadopago_currency_id == 'USD') ? 'selected' : '' ?>>Dollar                  </option>
								<option value="EUR" <?php echo ($mercadopago_currency_id == 'EUR') ? 'selected' : '' ?>>Euro                    </option>
								<option value="PYG" <?php echo ($mercadopago_currency_id == 'PYG') ? 'selected' : '' ?>>Guaraní                 </option>
								<option value="HNL" <?php echo ($mercadopago_currency_id == 'HNL') ? 'selected' : '' ?>>Lempira                 </option>
								<option value="ARS" <?php echo ($mercadopago_currency_id == 'ARS') ? 'selected' : '' ?>>Peso argentino          </option>
								<option value="CLP" <?php echo ($mercadopago_currency_id == 'CLP') ? 'selected' : '' ?>>Peso Chileno            </option>
								<option value="COP" <?php echo ($mercadopago_currency_id == 'COP') ? 'selected' : '' ?>>Peso Colombiano         </option>
								<option value="CUC" <?php echo ($mercadopago_currency_id == 'CUC') ? 'selected' : '' ?>>Peso Cubano Convertible </option>
								<option value="CUP" <?php echo ($mercadopago_currency_id == 'CUP') ? 'selected' : '' ?>>Peso Cubano             </option>
								<option value="DOP" <?php echo ($mercadopago_currency_id == 'DOP') ? 'selected' : '' ?>>Peso Dominicano         </option>
								<option value="MXN" <?php echo ($mercadopago_currency_id == 'MXN') ? 'selected' : '' ?>>Peso Mexicano           </option>
								<option value="UYU" <?php echo ($mercadopago_currency_id == 'UYU') ? 'selected' : '' ?>>Peso Uruguayo           </option>
								<option value="GTQ" <?php echo ($mercadopago_currency_id == 'GTQ') ? 'selected' : '' ?>>Quetzal Guatemalteco    </option>
								<option value="BRL" <?php echo ($mercadopago_currency_id == 'BRL') ? 'selected' : '' ?>>Real                    </option>
								<option value="PEN" <?php echo ($mercadopago_currency_id == 'PEN') ? 'selected' : '' ?>>Soles                   </option>
								<option value="CLF" <?php echo ($mercadopago_currency_id == 'CLF') ? 'selected' : '' ?>>Unidad de Fomento       </option>
							</select>
						</div>

						<div class="form-row">
							<strong>notification_url</strong><br>
							<input type="text" id="mercadopago_notification_url" name="mercadopago_notification_url" class="form-control" value="<?= $mercadopago_notification_url; ?>">
						</div>
					</div><!-- .tab-pane #payment-mercadopago -->

				</div><!-- .block .tab-content -->

				<div class="form-row submit-row">
					<input type="submit" id="submit" name="submit" class="btn btn-blue btn-less-padding">
				</div>
			</form>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>
<script>
$('#settingsTabs a:first').click(function (e) {
	e.preventDefault();
	$(this).tab('show');
})
</script>
</body>
</html>