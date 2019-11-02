<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_main_title; ?> - <?= $site_name; ?></title>
<?php require_once('_html_head.php'); ?>
</head>
<body class="tpl-msg">
<?php require_once('_header.php'); ?>

<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<?= $txt_msg; ?>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once('_footer.php'); ?>
</body>
</html>