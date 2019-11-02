<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once('_html_head.php'); ?>
</head>
<body class="tpl-contact">
<?php require_once('_header.php'); ?>
<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<div class="form-wrapper">
			<form action="process-contact.php" method="post" id="contact_form">
				<input type="hidden" name="submit_check" value="1" />

				<div class="form-row">
					<div><label class="desc" id="title1" for="nome"><?= $txt_name; ?></label></div>
					<div><input id="name" name="name" type="text" class="field text fn" value="" size="8" tabindex="1"></div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div><label class="desc" id="title3" for="email"><?= $txt_email; ?></label></div>
					<div><input id="email" name="email" type="email" spellcheck="false" value="" maxlength="255" tabindex="3"></div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div><label class="desc" id="title4" for="mensagem"><?= $txt_message; ?></label></div>
					<div><textarea id="message" name="message" rows="10" cols="50" tabindex="4"></textarea></div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div>&nbsp;</div>
					<div><input id="submit" name="submit" type="submit" value="submit" class="btn btn-blue" /></div>
					<div class="clear"></div>
				</div>

				<div class="clear"></div>
			</form>
		</div>
	</div><!-- .full-block .contact -->
</div><!-- .wrapper -->

<?php
require_once('_footer.php');
?>
</body>
</html>