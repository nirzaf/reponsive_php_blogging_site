<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>

</head>
<body class="tpl-logoff">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper">
	<div id="login-block">
		<h1><?= $txt_main_title; ?></h1>

		<p><?= $txt_message; ?></p>

		<script>
			window.location.href = "<?= $baseurl; ?>";
		</script>
	</div><!-- .full-block .login-page -->
</div><!-- #wrapper -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>