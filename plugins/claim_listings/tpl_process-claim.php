<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../templates/_html_head.php'); ?>
<style>
.col-md-4:nth-child(3n+1) {
	clear: both;
}

.stripe-block button[type="submit"] {
    padding: 0;
}
</style>
</head>
<body class="tpl-select-plan">
<?php require_once(__DIR__ . '/../../templates/_header.php'); ?>

<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<div class="block">
			<p><?= $txt_confirm_claim; ?></p>
			<?= $txt_selected_plan; ?>: <strong><?= $plan_name; ?></strong><br>
			<?= $txt_plan_price; ?>: <strong><?= $currency_symbol; ?> <?= $plan_price_display; ?></strong>
		</div>

		<?php
		// paypal form
		if($paypal_mode != -1) {
			?>
			<h3><?= $txt_pay_with_paypal; ?></h3>

			<div class="block clearfix">
				<form action="<?= $paypal_url; ?>" method="post" id="<?= $plan_type; ?>-<?= $plan_id; ?>">
					<input type="hidden" name="cmd"           value="<?= $cmd; ?>">
					<input type="hidden" name="notify_url"    value="<?= $claim_notify_url; ?>">
					<input type="hidden" name="bn"            value="<?= $bn; ?>">
					<input type="hidden" name="business"      value="<?= $paypal_merchant_id; ?>">
					<input type="hidden" name="item_name"     value="<?= $plan_name; ?> - <?= $site_name; ?>">
					<input type="hidden" name="currency_code" value="<?= $currency_code; ?>">
					<input type="hidden" name="custom"        value="<?= $place_id; ?>,<?= $plan_id; ?>,<?= $userid; ?>">
					<input type="hidden" name="image_url"     value="<?= $paypal_checkout_logo_url; ?>">
					<input type="hidden" name="lc"            value="<?= $paypal_locale; ?>">
					<input type="hidden" name="return"        value="<?= $baseurl; ?>/user/thanks">
					<input type="hidden" name="charset"       value="utf-8">

					<!-- amounts -->
					<?php
					if($plan_type == 'monthly' || $plan_type == 'monthly_feat') {
						?>
						<input type="hidden" name="a3"  value="<?= $a3; ?>">
						<input type="hidden" name="p3"  value="<?= $p3; ?>">
						<input type="hidden" name="t3"  value="<?= $t3; ?>">
						<input type="hidden" name="src" value="1">
						<?php
					}
					if($plan_type == 'one_time' || $plan_type == 'one_time_feat') {
						?>
						<input type="hidden" name="amount" value="<?= $amount; ?>">
						<?php
					}
					?>

					<!-- the submit button -->
					<input type="submit" id="submit" name="submit" value="<?= $txt_btn_submit; ?>" class="btn btn-blue">
					<div class="form-row">
						<img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg" width="120" />
					</div>
				</form>
			</div>
		<?php
		} // end paypal

		// stripe form
		if($stripe_mode != -1) {
			?>
			<h3><?= $txt_pay_with_stripe; ?></h3>

			<div class="block stripe-block">
				<form action="<?= $baseurl; ?>/_msg.php" method="POST">
					<input type="hidden" name="plan_type" value="<?= $plan_type; ?>">
					<input type="hidden" name="plan_id" value="<?= $plan_id; ?>">
					<input type="hidden" name="place_id" value="<?= $place_id; ?>">
					<input type="hidden" name="payer_id" value="<?= $userid; ?>">
					<input type="hidden" name="ref" value="stripe">
					<script
						src="https://checkout.stripe.com/checkout.js" class="stripe-button"
						data-key         = "<?= $stripe_key; ?>"
						data-amount      = "<?= $stripe_amount; ?>"
						data-currency    = "<?= $stripe_data_currency; ?>"
						data-name        = "<?= $plan_name; ?>"
						data-description = "<?= $stripe_data_description; ?>"
						data-image       = "<?= $stripe_data_image; ?>"
						data-locale      = "auto">
					</script>
				</form>
			</div>
			<?php
		} // end stripe

		// mercadopago form
		if($mercadopago_mode != -1) {
			?>
			<h3><?= $txt_pay_with_mercadopago; ?></h3>

			<div class="block mercadopago-block">
				<a href="<?= $button_link['response']['init_point']; ?>" class="btn btn-blue" style="padding: 4px 24px; font-weight: 700"><?= $txt_pay_with_mercadopago; ?></a><br>
				<img src="https://secure.mlstatic.com/components/resources/mp/images/mercadopago-logo-166x44.png">
			</div>
		<?php
		} // end mercadopago
		?>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/../../templates/_footer.php'); ?>
</body>
</html>