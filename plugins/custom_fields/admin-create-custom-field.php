<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');

// get all categories
$cats_arr = array();
$query = "SELECT * FROM cats WHERE cat_status = 1 ORDER BY plural_name";
$stmt = $conn->prepare($query);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$cur_loop_arr = array(
		'cat_id'       => $row['id'],
		'plural_name'  => $row['plural_name'],
		'parent_id'    => $row['parent_id']
	);
	$cats_arr[] = $cur_loop_arr;

	// if is empty
}

// store total number of cats in a variable
$total_cats = count($cats_arr);
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_create_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../admin/_admin_html_head.php'); ?>
<style>
#cat-checkboxes ul {
	list-style-type: none;
}

#cat-checkboxes label {
	font-weight: 400;
}
</style>
</head>
<body>
<?php require_once(__DIR__ . '/../../admin/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/../../admin/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_create_main_title; ?></h2>

		<div class="padding create-custom-field">
			<form class="form-create-custom-field" method="post">
				<div class="block">
					<label class="label" for="field_name"><?= $txt_field_name; ?></label><br>
					<input type="text" id="field_name" name="field_name" class="form-control" required>
				</div>

				<div class="block">
					<label class="label" for="field_type"><?= $txt_field_type; ?></label><br>
					<select id="field_type" name="field_type" class="form-control" required>
						<option value="text"><?= $txt_type_text; ?></option>
						<option value="radio"><?= $txt_type_radio; ?></option>
						<option value="select"><?= $txt_type_select; ?></option>
						<option value="checkbox"><?= $txt_type_check; ?></option>
						<option value="multiline"><?= $txt_type_multiline; ?></option>
						<option value="url"><?= $txt_type_url; ?></option>
					</select>
				</div>

				<div class="block">
					<label class="label" for="tooltip"><?= $txt_tooltip; ?></label><br>
					<input type="text" id="tooltip" name="tooltip" class="form-control">
				</div>

				<div class="block">
					<label class="label" for="icon"><?= $txt_icon; ?></label><br>
					<input type="text" id="icon" name="icon" class="form-control">
				</div>

				<div class="block">
					<label class="label" for="icon"><?= $txt_options; ?></label><br>
					<input type="checkbox" id="required" name="required" value="1"> <?= $txt_required; ?>
					<input type="checkbox" id="searchable" name="searchable" value="1"> <?= $txt_searchable; ?>
				</div>

				<div class="block">
					<label class="label" for="values_list"><?= $txt_values_list; ?></label><br>
					<input type="text" id="values_list" name="values_list" class="form-control">
				</div>

				<div class="block">
					<label class="label" for="field_order"><?= $txt_field_order; ?></label><br>
					<input type="number" id="field_order" name="field_order" class="form-control">
				</div>

				<div class="block">
					<label class="label"><?= $txt_categories; ?></label><br>

					<input type="checkbox" id="select_all" name="select_all"> Select All<br>

					<?php
					// group by parents
					$cats_grouped_by_parent = group_cats_by_parent($cats_arr);
					// send bogus non empty array so that the show_cats() function returns checkboxes not checked
					$empty_arr = array('bogus');
					show_cats($cats_grouped_by_parent, 0, $empty_arr, 1);
					?>
				</div><!-- .block (show categories tree) -->

				<div class="block">
					<input type="submit" id="submit" name="submit" class="btn btn-blue">
				</div>
			</form>
		</div><!-- .padding .edit-custom-field -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/../../admin/_admin_footer.php'); ?>

<script>
$(document).ready(function() {
	// toggle categories checkboxes
	$('#select_all').click(function(e){
		var checkedStatus = this.checked;
		$('#cat-checkboxes').find(':checkbox').each(function() {
			$(this).prop('checked', checkedStatus);
		});
	});

	// submit form
    $('#submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/plugins/custom_fields/admin-process-create-custom-field.php';

		$.post(post_url, {
			params: $('form.form-create-custom-field').serialize(),
			},
			function(data) {
				$('.create-custom-field').empty().html(data);
			}
		);
    });
});
</script>
</body>
</html>