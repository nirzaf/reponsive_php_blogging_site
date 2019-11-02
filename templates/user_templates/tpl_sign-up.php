<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-sign-up">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper wrapper-720">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<p class="sub-heading"></p>

		<?php
		if(empty($form_submitted)) {
			?>
			<div class="form-block">
				<div class="login-form">
					<form id="the_form" method="post" action="<?= $baseurl; ?>/user/sign-up">
						<?php
						$referrer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
						?>
						<input type="hidden" id="referrer" name="referrer" value="<?php echo $referrer; ?>" />

						<div class="form-row">
							<input type="text" id="fname" class="" name="fname" tabindex="0" placeholder="<?= $txt_label_fname; ?>">
							<div class="alert-bubble" id="validate-fname">First Name</div>
						</div>

						<div class="form-row">
							<input type="text" id="lname" class="" name="lname" tabindex="0" placeholder="<?= $txt_label_lname; ?>">
							<div class="alert-bubble" id="validate-lname">Last Name</div>
						</div>

						<div class="form-row">
							<input type="email" id="email" class="field text fn" name="email" tabindex="0" placeholder="<?= $txt_label_email; ?>">
							<div class="alert-bubble" id="validate-email">Email</div>
						</div>

						<div class="form-row">
							<input type="password" id="password" class="field text fn" name="password" tabindex="0" placeholder="<?= $txt_label_passw; ?>" autocomplete="false">
							<div class="alert-bubble" id="validate-password">Password</div>
						</div>

						<div class="form-row" style="text-align: center">
							<input type="button" id="submit-btn" class="btn btn-blue btn-less-padding" name="submit-btn" value="<?= $txt_submit_btn; ?>" tabindex="0" />
						</div>
					</form>
				</div><!-- .login-form -->

				<div class="social-login">
					<div class="social-login-button">
						<a class="facebook-icon" href="<?= $baseurl; ?>/user/login.php?provider=facebook"><i class="fa fa-facebook"></i> <?= $txt_btn_facebook ; ?></a>
					</div>

					<div class="social-login-button">
						<a class="twitter-icon" href="<?= $baseurl; ?>/user/login.php?provider=twitter"><i class="fa fa-twitter"></i> <?= $txt_btn_twitter ; ?></a>
					</div>
				</div>

				<div class="clear"></div>
			</div><!-- .form-block -->

			<?= $txt_has_account; ?>  <a href="login.php"><?= $txt_link_log_in; ?></a></p>
			<?php
		}
		?>

		<?php
		if(!empty($user_exists)) {
			?>
			<div class="msg alert alert-danger">
				<p><strong><?= $txt_email_exists; ?></strong></p>

				<p><?= $txt_email_exists_explain; ?></p>
			</div>
			<?php
		}
		?>

		<?php
		if($invalid_email) {
			?>
			<div class="msg alert alert-danger">
				<p><strong><?= $txt_invalid_email; ?></strong></p>

				<p><?= $txt_invalid_email_explain; ?></p>
			</div>
			<?php
		}
		?>

		<?php
		if($empty_fields == 1 && $form_submitted) {
			?>
			<div class="msg alert alert-danger">
				<p><strong><?= $txt_missing_fields; ?></strong></p>

				<p><?= $txt_missing_fields_explain; ?></p>

			</div>
			<?php
		}
		?>

		<?php
		if($user_created == 1) {
			?>
			<div class="msg">
				<p><strong><?= $txt_acct_created; ?></strong></p>

				<p><?= $txt_acct_created_explain; ?></p>
			</div>
			<?php
		}
		?>

		<?php
		if($form_submitted && ($empty_fields == 1 || $invalid_email || !empty($user_exists))) {
			?>
			<div class="msg">
				<p><?= $txt_submit_again; ?></p>
			</div>
			<?php
		}
		?>
	</div><!-- .full-block -->
</div><!-- .wrapper .wrapper-720 -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>

<script>
$(document).ready(function() {
	// form submit
	$("#submit-btn").click(function() {
		// hide validations
		$('#validate-fname').hide();
		$('#validate-email').hide();
		$('#validate-password').hide();

		// validate
		if(!checkRequired("fname")) {
			$(window).scrollTop($('#validate-fname').offset().top -100);
			return false;
		}

		if(!checkRequired("email")) {
			$(window).scrollTop($('#validate-email').offset().top -100);
			return false;
		}

		if(!checkRequired("password")) {
			$(window).scrollTop($('#validate-password').offset().top -100);
			return false;
		}

		// if ok, submit
		$('#the_form').submit();
	});
});

function checkRequired(id) {
	if($("#" + id).val() == null || $("#" + id).val() == '') {
		$('#validate-' + id).show();
		return false;
	}
	else {
		return true;
	}
}
</script>
</body>
</html>