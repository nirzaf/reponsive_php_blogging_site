<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-pages.php');

$query = "SELECT COUNT(*) AS total_pages FROM pages";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_pages = $row['total_pages'];

if($total_pages > 0) {
	$pages_arr = array();

	$query = "SELECT page_id, page_title, page_slug, page_group, page_order FROM pages";
	$stmt = $conn->prepare($query);
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$page_id    = $row['page_id'];
		$page_title = $row['page_title'];
		$page_slug  = $row['page_slug'];
		$page_group = $row['page_group'];
		$page_order = $row['page_order'];

		// sanitize
		$page_title = e(trim($page_title));
		$page_slug  = e(trim($page_slug));
		$page_group = e(trim($page_group));
		$page_order = e(trim($page_order));

		$page_link = "$baseurl/p/$page_id/$page_slug";

		$cur_lop_arr = array(
			'page_id'    => $page_id,
			'page_title' => $page_title,
			'page_link'  => $page_link,
			'page_group' => $page_group,
			'page_order' => $page_order
		);

		$pages_arr[] = $cur_lop_arr;
	}
}
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
<body class="admin-pages">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<strong><?= $txt_action; ?>:</strong><br>
				<a href="<?= $baseurl; ?>/admin/admin-create-page" class="create-cat-btn btn btn-blue btn-less-padding"><?= $txt_create_page; ?></a>
			</div>
			<?php
			if(!empty($pages_arr)) {
				?>
				<span><?= $txt_total_rows; ?>: <strong><?= $total_pages ?></strong></span>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th><?= $txt_id; ?></th>
							<th><?= $txt_page_title; ?></th>
							<th><?= $txt_link; ?></th>
							<th><?= $txt_group; ?></th>
							<th><?= $txt_order; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>

						<?php
						foreach($pages_arr as $k => $v) {
							$page_id    = $v['page_id'];
							$page_title = $v['page_title'];
							$page_link  = $v['page_link'];
							$page_group = $v['page_group'];
							$page_order = $v['page_order'];
							?>
							<tr id="page-<?= $page_id; ?>">
								<td><?= $page_id; ?></td>
								<td><?= $page_title; ?></td>
								<td class="break-all"><a href="<?= $page_link; ?>" target="_blank"><?= $txt_view; ?></a></td>
								<td><?= $page_group; ?></td>
								<td><?= $page_order; ?></td>
								<td class="nowrap">
									<span data-toggle="tooltip" title="<?= $txt_edit_page; ?>">
										<a href="<?= $baseurl; ?>/admin/admin-edit-page/<?= $page_id; ?>" class="btn btn-default btn-less-padding edit-page-btn"
											data-id="<?= $page_id; ?>">
											<i class="fa fa-pencil"></i>
										</a>
									</span>

									<span data-toggle="tooltip"	title="<?= $txt_remove_page; ?>">
										<a href="#" class="btn btn-less-padding btn-default remove-page"
											data-id="<?= $page_id; ?>">
											<i class="fa fa-trash"></i>
										</a>
									</span>
								</td>
							</tr>
						<?php
						}
					?>
					</table>
				</div>
				<?php
			}
			else {
				?>
				<p><?= $txt_msg_no_page; ?></p>
				<?php
			}
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<!-- javascript -->
<script src="<?= $baseurl; ?>/lib/jinplace/jinplace.min.js"></script>
<script>
$(document).ready(function(){
	// remove page
	$('.remove-page').click(function(e) {
		e.preventDefault();
		var page_id = $(this).data('id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-page.php';
		var wrapper = '#page-' + page_id;
		$.post(post_url, {
			page_id: page_id
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
				}
			}
		);
	});

});
</script>
</body>
</html>