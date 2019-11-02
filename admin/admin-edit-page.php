<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-edit-page.php');

// path info
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}
// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}
$frags = explode("/", $frags);

$page_id = $frags[1];

$query = "SELECT * FROM pages WHERE page_id = :page_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_id', $page_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$page_id       = $row['page_id'];
$page_title    = $row['page_title'];
$page_slug     = $row['page_slug'];
$meta_desc     = $row['meta_desc'];
$page_contents = $row['page_contents'];
$page_group    = $row['page_group'];
$page_order    = $row['page_order'];

// sanitize
$page_title    = e(trim($page_title));
$page_slug     = e(trim($page_slug));
$meta_desc     = e(trim($meta_desc));
$page_contents = e(trim($page_contents));
$page_group    = e(trim($page_group));
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

		<div class="padding edit-page">
			<div class="alert alert-info" role="alert">
				<p><?= $txt_about_group; ?></p>
			</div>

			<form class="form-edit-page" method="post">
				<input type="hidden" id="page_id" name="page_id" value="<?= $page_id; ?>">

				<div class="block">
					<label class="label" for="page_title"><?= $txt_page_title; ?></label><br>
					<input type="text" id="page_title" name="page_title" class="form-control" value="<?= $page_title; ?>" required>
				</div>

				<div class="block col-left">
					<label class="label" for="meta_desc"><?= $txt_meta_desc; ?></label><br>
					<input type="text" id="meta_desc" name="meta_desc" class="form-control" value="<?= $meta_desc; ?>">
				</div>

				<div class="block col-left">
					<label class="label" for="page_group"><?= $txt_group_name; ?></label><br>
					<input type="text" id="page_group" name="page_group" class="form-control" value="<?= $page_group; ?>">
				</div>

				<div class="block col-right">
					<label class="label" for="page_order"><?= $txt_order; ?></label><br>
					<input type="text" id="page_order" name="page_order" class="form-control" value="<?= $page_order; ?>">
				</div>

				<div class="clear"></div>

				<div class="block">
					<textarea name="page_contents" id="summernote"><?= $page_contents; ?></textarea>
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

	// edit page form submitted
    $('#submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-edit-page.php';

		$.post(post_url, {
			params: $('form.form-edit-page').serialize(),
			},
			function(data) {
				$('.edit-page').empty().html(data);

			}
		);
    });
});
</script>
</body>
</html>