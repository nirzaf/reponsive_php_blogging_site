<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-tools.php');
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body>
<?php require_once(__DIR__ . '/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<a href="#"
				data-toggle="modal"
				data-target="#deactivate-listings-modal">
				<i class="fa fa-trash"></i> <?= $txt_deactivate_expired; ?></a>
			</a>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- modal deactivate listings -->
<div class="modal fade" id="deactivate-listings-modal" tabindex="-1" role="dialog" aria-labelledby="Deactivate Listings Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_deactivate_expired; ?></h3>
			</div>
			<div class="modal-body">

			</div><!-- modal body -->
			<div class="modal-footer">
				<a href="#" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_close; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end modal -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<script>
$(document).ready(function(){
	// show edit plan modal
	$('#deactivate-listings-modal').on('show.bs.modal', function (event) {
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-deactivate-listings.php';

		$.post(post_url, { },
			function(data) {
				modal.find('.modal-body').html(data);
			}
		);
	});
});
</script>
</body>
</html>