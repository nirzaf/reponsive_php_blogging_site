<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
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
			<?php
			if($hybridauth_provider_name == 'local' || $is_admin) {
				?>
				<form method="post" action="process-edit-pass.php">
					<input type="hidden" name="csrf_token" value="<?= session_id(); ?>">
					<div class="form-row">
						<div class="label-col">
							<label for="cur_pass"><?= $txt_label_cur_pass; ?></label>
						</div>
						<div class="field-col"><input type="password" id="cur_pass" name="cur_pass" class="form-control" /></div>
						<div class="clear"></div>
					</div>

					<div class="form-row">
						<div class="label-col">
							<label for="new_pass"><?= $txt_label_new_pass; ?></label>
						</div>
						<div class="field-col"><input type="password" id="new_pass" name="new_pass" class="form-control" /></div>
						<div class="clear"></div>
					</div>

					<div class="form-row submit-row">
						<div><input type="submit" id="submit" name="submit" value="<?= $txt_btn_submit; ?>" class="btn btn-blue"></div>
					</div>
				</form>
				<?php
			}
			else {
				echo $txt_social_user;
			}
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>