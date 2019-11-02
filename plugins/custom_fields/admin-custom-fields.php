<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');

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

// count how many fields exist
$query = "SELECT COUNT(*) AS total_rows FROM custom_fields WHERE field_status = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

// get all custom fields and their values
$custom_fields = array();
if($total_rows > 0) {
	$query = "SELECT field_id, field_name, field_type, required, searchable
	FROM custom_fields WHERE field_status = 1";
	$stmt = $conn->prepare($query);
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$custom_fields[] = array(
			'field_id'   => $row['field_id'],
			'field_name' => $row['field_name'],
			'field_type' => $row['field_type']
		);
	}
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_show_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../admin/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="admin-cats">
<?php require_once(__DIR__ . '/../../admin/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/../../admin/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_show_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<strong><?= $txt_action; ?>:</strong><br>
				<a href="<?= $baseurl; ?>/admin/plugin/custom_fields/create" class="create-field-btn btn btn-blue btn-less-padding">
					<?= $txt_show_create_field; ?>
				</a>
			</div>

			<?php
			if(!empty($custom_fields)) {
				?>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th>id</th>
							<th><?= $txt_field_name; ?></th>
							<th><?= $txt_field_type; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($custom_fields as $k => $v) {
							$field_id   = $v['field_id'];
							$field_name = $v['field_name'];
							$field_type = $v['field_type'];
							?>
							<tr id="field-<?= $field_id; ?>">
								<td><?= $field_id; ?></td>
								<td><?= $field_name; ?></td>
								<td><?= $field_type; ?></td>
								<td class="nowrap">
									<span data-toggle="tooltip" title="<?= $txt_show_edit_field; ?>">
										<a href="<?= $baseurl; ?>/admin/plugin/custom_fields/edit/<?= $field_id; ?>" class="btn btn-default btn-less-padding edit-field-btn">
											<i class="fa fa-pencil"></i>
										</a>
									</span>

									<span data-toggle="tooltip" title="<?= $txt_show_remove_field; ?>">
										<a href="#" class="btn btn-default btn-less-padding remove-field"
											data-field-id="<?= $field_id; ?>">
											&nbsp;<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
										</a>
									</span>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</div><!-- .table-responsive -->
			<?php
			}
			else {
				?>
				<div class="block"><?= $txt_no_results; ?></div>
				<?php
			}
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->


<?php require_once(__DIR__ . '/../../admin/_admin_footer.php'); ?>

<!-- javascript -->
<script src="<?= $baseurl; ?>/lib/jinplace/jinplace.min.js"></script>
<script>
$(document).ready(function(){
	// remove field
	$('.remove-field').click(function(e){
		e.preventDefault();
		var field_id = $(this).data('field-id');
		var post_url = '<?= $baseurl; ?>' + '/plugins/custom_fields/admin-process-remove-custom-field.php';
		var wrapper = '#field-' + field_id;
		$.post(post_url, {
			field_id: field_id
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