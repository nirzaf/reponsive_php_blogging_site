<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-password-reset">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper wrapper-600">
	<h1><?= $txt_main_title; ?></h1>

	<?php
	if(!$form_submitted && $valid_token) {
		?>
		<p class="sub-heading"><?= $txt_sub_heading; ?></p>

		<div class="form-block">
			<form method="post" action="<?= $baseurl; ?>/user/password-reset.php">
				<input id="user_id" name="user_id" type="hidden" value="<?= $user_id; ?>" />
				<input id="token" name="token" type="hidden" value="<?= $token; ?>" />
				<div class="form-row">
					<div><label class="desc" id="title1" for="email"><?= $txt_label_new_pass; ?></label></div>
					<div><input id="new_pass" name="new_pass" type="password" class="field text fn" value="" size="8" tabindex="1" /></div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div>&nbsp;</div>
					<div><input id="submit" name="submit" type="submit" value="<?= $txt_submit_btn; ?>" class="btn btn-blue" /></div>
					<div class="clear"></div>
				</div>
			</form>
		</div><!-- .form-block -->

		<?= $txt_or_login; ?>  <a href="login.php"><?= $txt_link_log_in; ?></a></p>
		<?php
	} // end if not form submitted, show form

	if(!$form_submitted && !$valid_token) {
		?>
		<div class="msg-block">
			<?= $txt_invalid_token; ?>

			<p><a href="<?= $baseurl; ?>/user/login.php"><?= $txt_link_log_in; ?></a></p>
		</div><!-- .msg-block -->
		<?php
	}

	if($form_submitted && !$valid_token) {
		?>
		<div class="msg-block">
			<?= $txt_invalid_token; ?>

			<p><a href="<?= $baseurl; ?>/user/login.php"><?= $txt_link_log_in; ?></a></p>
		</div><!-- .msg-block -->
		<?php
	}

	if($form_submitted && $update_success) {
		?>
		<div class="msg-block">
			<?= $txt_update_success; ?>

			<p><a href="<?= $baseurl; ?>/user/login.php"><?= $txt_link_log_in; ?></a></p>
		</div><!-- .msg-block -->
		<?php
	}
	?>
</div><!-- .wrapper .wrapper-600 -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>