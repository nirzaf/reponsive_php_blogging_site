<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title_404; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once('_html_head.php'); ?>
</head>
<body class="tpl-place-404">
<?php require_once('_header.php'); ?>
<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_h1_404; ?></h1>

		<p><?= $txt_404; ?></p>
	</div><!-- .full-block .contact -->
</div><!-- .wrapper -->

<?php
require_once('_footer.php');
?>
</body>
</html>