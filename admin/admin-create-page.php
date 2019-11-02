<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-create-page.php');
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>
.note-btn.btn {
	padding: 4px 8px;
}

.note-group-select-from-files {
	display: none;
}
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

		<div class="padding create-page">
			<div class="alert alert-info" role="alert">
				<p><?= $txt_about_group; ?></p>
			</div>

			<form class="form-create-page">
				<div class="block">
					<label class="label" for="page_title"><?= $txt_page_title; ?></label><br>
					<input type="text" id="page_title" name="page_title" class="form-control" required>
				</div>

				<div class="block col-left">
					<label class="label" for="meta_desc"><?= $txt_meta_desc; ?></label><br>
					<input type="text" id="meta_desc" name="meta_desc" class="form-control">
				</div>

				<div class="block col-left">
					<label class="label" for="page_group"><?= $txt_group_name; ?></label><br>
					<input type="text" id="page_group" name="page_group" class="form-control">
				</div>

				<div class="block col-right">
					<label class="label" for="page_order"><?= $txt_order; ?></label><br>
					<input type="number" id="page_order" name="page_order" class="form-control">
				</div>

				<div class="clear"></div>

				<div class="block">
					<textarea name="page_contents" id="summernote"></textarea>
				</div>

				<div class="block">
					<input type="submit" id="submit" name="submit" class="btn btn-blue">
				</div>
			</form>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>
<!-- include summernote css/js-->
<link href="<?= $baseurl; ?>/lib/summernote/dist/summernote.css" rel="stylesheet">
<script src="<?= $baseurl; ?>/lib/summernote/dist/summernote.min.js"></script>
<script>
$(document).ready(function() {
	$('#summernote').summernote({
		height: 450,
		styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
		toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video']],
				['view', ['fullscreen', 'codeview', 'help']]
			  ]
	});

	// create page form submitted
    $('#submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-create-page.php';

		$.post(post_url, {
			params: $('form.form-create-page').serialize(),
			},
			function(data) {
				$('.create-page').empty().html(data);

			}
		);
    });
});

</script>
</body>
</html>