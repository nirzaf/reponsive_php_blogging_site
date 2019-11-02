<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-singup-confirm">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper">
	<div class="msg-block">
		<h1><?= $txt_main_title; ?></h1>

		<p class="sub-heading"></p>

		<?php
		if(!empty($user_confirmed)) {
			?>
			<div class="msg">
				<?= $txt_confirmation_success; ?>

				<?= $txt_confirmation_success_msg; ?>
			</div>
			<?php
		}

		else {
			?>
			<div class="msg">
				<?= $txt_confirmation_fail; ?>

				<?= $txt_confirmation_fail_msg; ?>
			</div>
			<?php
		}
		?>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>