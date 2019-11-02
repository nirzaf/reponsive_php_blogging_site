<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-password-request">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper wrapper-600">
	<h1><?= $txt_main_title; ?></h1>

	<?php
	if(!$form_submitted) {
		?>
		<div class="form-block">
			<form method="post" action="<?= $baseurl; ?>/user/resend-confirmation">
				<div class="form-row">
					<div><label class="desc" id="title1" for="email"><?= $txt_label_email; ?></label></div>
					<div><input id="email" name="email" type="text" class="field text fn" value="" size="8" tabindex="1" /></div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div>&nbsp;</div>
					<div><input id="submit" name="submit" type="submit" class="btn btn-blue" /></div>
					<div class="clear"></div>
				</div>
			</form>
		</div><!-- .form-block -->
		<?php
	}
	if($request_sent) {
		?>
		<div class="msg-block">
			<?= $txt_confirmation_sent; ?>

			<p><a href="<?= $baseurl; ?>/user/login.php"><?= $txt_link_log_in; ?></a></p>
		</div><!-- .msg-block -->
		<?php
	}
	if(($invalid_email || !$user_exists) && $form_submitted) {
		?>
		<div class="msg-block">
			<?= $txt_invalid_email; ?>

			<p><a href="<?= $baseurl; ?>/user/resend-confirmation"><?= $txt_link_try_again; ?></a></p>
		</div><!-- .msg-block -->
		<?php
	}
	?>
</div><!-- .wrapper .wrapper-600 -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>