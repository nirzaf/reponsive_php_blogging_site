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
$field_id = (!empty($frags[3])) ? $frags[3] : 0;

if(empty($field_id)) {
	throw new Exception('Field id cannot be empty');
}

// get custom field data
$query = "SELECT * FROM custom_fields WHERE field_id = :field_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':field_id', $field_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$field_name  = (!empty($row['field_name'] )) ? $row['field_name']  : 'undefined';
$field_type  = (!empty($row['field_type'] )) ? $row['field_type']  : 'text';
$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
$tooltip     = (!empty($row['tooltip']    )) ? $row['tooltip']     : '';
$icon        = (!empty($row['icon']       )) ? $row['icon']        : '';
$required    = ($row['required'] == 1)       ? 'checked'           : '';
$searchable  = ($row['searchable'] == 1)     ? 'checked'           : '';
$field_order = (!empty($row['field_order'])) ? $row['field_order'] : 0;

// sanitize
$icon = e($icon);

// get categories with this custom field
$query = "SELECT cat_id FROM rel_cat_custom_fields WHERE field_id = :field_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':field_id', $field_id);
$stmt->execute();
$checked_cats = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$checked_cats[] = $row['cat_id'];
}

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

$cats_grouped_by_parent = group_cats_by_parent($cats_arr);
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_edit_html_title; ?> - <?= $site_name; ?></title>
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
		<h2><?= $txt_edit_main_title; ?> (id:<?= $field_id; ?>)</h2>

		<div class="padding edit-custom-field">
			<form class="form-edit-custom-field" method="post">
				<input type="hidden" id="field_id" name="field_id" value="<?= $field_id; ?>">

				<div class="block">
					<label class="label" for="field_name"><?= $txt_field_name; ?></label><br>
					<input type="text" id="field_name" name="field_name" class="form-control" value="<?= $field_name; ?>" required>
				</div>

				<div class="block">
					<label class="label" for="field_type"><?= $txt_field_type; ?></label><br>
					<select id="field_type" name="field_type" class="form-control" required>
						<option value="text" <?= ($field_type == 'text') ? 'selected' : ''; ?>><?= $txt_type_text; ?></option>
						<option value="radio" <?= ($field_type == 'radio') ? 'selected' : ''; ?>><?= $txt_type_radio; ?></option>
						<option value="select" <?= ($field_type == 'select') ? 'selected' : ''; ?>><?= $txt_type_select; ?></option>
						<option value="checkbox" <?= ($field_type == 'checkbox') ? 'selected' : ''; ?>><?= $txt_type_check; ?></option>
						<option value="multiline" <?= ($field_type == 'multiline') ? 'selected' : ''; ?>><?= $txt_type_multiline; ?></option>
						<option value="url" <?= ($field_type == 'url') ? 'selected' : ''; ?>><?= $txt_type_url; ?></option>
					</select>
				</div>

				<div class="block">
					<label class="label" for="tooltip"><?= $txt_tooltip; ?></label><br>
					<input type="text" id="tooltip" name="tooltip" class="form-control" value="<?= $tooltip; ?>">
				</div>

				<div class="block">
					<label class="label" for="icon"><?= $txt_icon; ?></label><br>
					<input type="text" id="icon" name="icon" class="form-control" value="<?= $icon; ?>">
				</div>

				<div class="block">
					<label class="label" for="icon"><?= $txt_options; ?></label><br>
					<input type="checkbox" id="required" name="required" value="1" <?= $required; ?>> <?= $txt_required; ?>
					<input type="checkbox" id="searchable" name="searchable" value="1" <?= $searchable; ?>> <?= $txt_searchable; ?>
				</div>

				<div class="block">
					<label class="label" for="values_list"><?= $txt_values_list; ?></label><br>
					<input type="text" id="values_list" name="values_list" class="form-control" value="<?= $values_list; ?>">
				</div>

				<div class="block">
					<label class="label" for="field_order"><?= $txt_field_order; ?></label><br>
					<input type="number" id="field_order" name="field_order" class="form-control" value="<?= $field_order; ?>">
				</div>

				<div class="block">
					<label class="label"><?= $txt_categories; ?></label><br>

					<input type="checkbox" id="select_all" name="select_all"> Select All<br>

					<?php
					show_cats($cats_grouped_by_parent, 0, $checked_cats, 1);
					?>

					<ul class="no-margin" id="cat-checkboxes">

					</ul>
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
		var post_url = '<?= $baseurl; ?>' + '/plugins/custom_fields/admin-process-edit-custom-field.php';

		$.post(post_url, {
			params: $('form.form-edit-custom-field').serialize(),
			},
			function(data) {
				$('.edit-custom-field').empty().html(data);
			}
		);
    });
});
</script>
</body>
</html>