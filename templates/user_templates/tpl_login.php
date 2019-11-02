<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>

</head>
<body class="tpl-login">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper wrapper-720">
	<h1><?= $txt_main_title; ?></h1>

	<div class="form-block">
		<?php
		if(!empty($from)) {
			?>
			<div class="alert alert-info" role="alert"><?= $txt_from_select_plan; ?></div>
			<?php
		}
		?>
		<div class="login-form">
			<?php
			if($status == 'approved') {
				?>
				<form method="post" action="login.php">
					<input type="hidden" id="referrer" name="referrer" value="<?= $referrer; ?>" />

					<?php
					if($wrong_pass) {
						?>
						<div class="alert alert-danger" role="alert"><?= $txt_wrong_pass; ?></div>
						<?php
					}
					?>

					<?php
					if(!empty($email_already_used)) {
						?>
						<div class="alert alert-danger" role="alert"><?= $txt_email_used; ?></div>
						<?php
					}
					?>

					<div class="form-row">
						<div><input id="email" name="email" type="text" class="field text fn" value="" size="8" tabindex="1" placeholder="<?= $txt_email; ?>" /></div>
					</div>

					<div class="form-row">
						<div><input id="password" name="password" type="password" spellcheck="false" value="" maxlength="255" tabindex="3" placeholder="<?= $txt_passw; ?>"></div>
					</div>

					<div class="form-row">
						<button id="submit" name="submit" class="btn btn-blue btn-login-submit"><?= $txt_submit_btn; ?></button>
						<a href="password-request.php"><?= $txt_link_forgot_pass; ?></a>
					</div>
				</form>
				<?php
			}
			else {
				?>
				<div class="alert alert-info" role="alert"> status not approved
				<a href="<?= $baseurl; ?>/user/resend-confirmation.php">Resend confirmation email</a>
				</div>
				<?php
			}
			?>
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

	<?= $txt_new_to_site; ?> <a href="<?= $baseurl; ?>/user/sign-up.php"><?= $txt_link_create_account; ?></a>
</div><!-- .wrapper .wrapper-720 -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>

</body>
</html>