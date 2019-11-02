<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-edit-pass">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_user_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<?= $msg; ?>
		</div>
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>