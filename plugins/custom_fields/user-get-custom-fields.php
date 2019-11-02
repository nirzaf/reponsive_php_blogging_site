<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../user/user_area_inc.php');
require_once(__DIR__ . '/translation.php');

/*
This file is requested by plugins/custom_fields/user-form-block.php via ajax
*/

$cat_id   = $_POST['cat_id'];
$place_id = $_POST['place_id'];

// find global fields and get just the ids
// if called from edit place page, get field values too
$query = "SELECT f.*
			FROM custom_fields f
			LEFT JOIN rel_cat_custom_fields r
				ON f.field_id = r.field_id
			WHERE r.rel_id IS NULL AND field_status = 1
			ORDER BY f.field_order";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

$custom_fields_ids = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id = (!empty($row['field_id'])) ? $row['field_id'] : 0;
	$custom_fields_ids[] = $field_id;
}

// find fields for this cat
$custom_fields = array();
$query = "SELECT f.*
	FROM rel_cat_custom_fields r
	LEFT JOIN custom_fields f ON r.field_id = f.field_id
	WHERE r.cat_id = :cat_id AND field_status = 1
	ORDER BY f.field_order";
$stmt = $conn->prepare($query);
$stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = (!empty($row['field_id'   ])) ? $row['field_id'   ] : '';
	$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
	$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : '';
	$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
	$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
	$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';
	$required    = (!empty($row['required'   ])) ? 'required'          : '';
	$searchable  = (!empty($row['searchable' ])) ? $row['searchable' ] : 0;
	$field_order = (!empty($row['field_order'])) ? $row['field_order'] : 0;

	$custom_fields[] = array(
		'field_id'    => $field_id,
		'field_name'  => $field_name,
		'field_type'  => $field_type,
		'values_list' => $values_list,
		'tooltip'     => $tooltip,
		'icon'        => $icon,
		'required'    => $required,
		'searchable'  => $searchable,
		'field_order' => $field_order
	);

	$custom_fields_ids[] = $field_id;
}

// fields ids hidden field
if(!empty($custom_fields_ids)) {
	$custom_fields_ids = implode(',', $custom_fields_ids);
}
else {
	$custom_fields_ids = '';
}
?>
<input type="hidden" name="custom_fields_ids" id="custom_fields_ids" value="<?= $custom_fields_ids; ?>">
<ul id="cat-fields">
<?php
foreach($custom_fields as $v) {
	$field_id    = $v['field_id'];
	$field_name  = $v['field_name'];
	$field_type  = $v['field_type'];
	$values_list = $v['values_list'];
	$tooltip     = $v['tooltip'];
	$icon        = $v['icon'];
	$required    = $v['required'];
    $searchable  = $v['searchable'];

	// build tooltip
	if(!empty($tooltip)) {
		$tooltip = "<a class='the-tooltip' data-toggle='tooltip' data-placement='top' title='$tooltip'><i class='fa fa-question-circle'></i></a>";
	}
	else {
		$tooltip = '';
	}

	// explode values
	$values_arr = array();
	if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
		$values_arr = explode(';', $values_list);
	}

	?>
	<li id="li-field-<?= $field_id; ?>" class="block">
		<?php
		if($field_type == 'radio') {
			?>
			<label><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<?php
			foreach($values_arr as $v) {
				$v = e(trim($v));
				?>
				<label><input type="radio" name="field_<?= $field_id; ?>" value="<?= $v; ?>" <?= $required; ?>> <?= $v; ?></label>
				<?php
			}
		}
		if($field_type == 'select') {
			?>
			<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<select name="field_<?= $field_id; ?>" <?= $required; ?>>
			<?php
			foreach($values_arr as $v) {
				$v = e(trim($v));
				?>
				<option value="<?= $v; ?>"><?= $v; ?>
				<?php
			}
			?>
			</select>
			<?php
		}
		if($field_type == 'checkbox') {
			?>
			<label><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<?php
			foreach($values_arr as $v) {
				$v = e(trim($v));
				?>
				<label><input type="checkbox" name="field_<?= $field_id; ?>[]" value="<?= $v; ?>" <?= $required; ?>> <?= $v; ?></label>
				<?php
			}
		}
		if($field_type == 'text') {
			?>
			<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?>>
			<?php
		}
		if($field_type == 'multiline') {
			?>
			<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<textarea name="field_<?= $field_id; ?>" <?= $required; ?>></textarea>
			<?php
		}
		if($field_type == 'url') {
			?>
			<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
			<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?>>
			<?php
		}
		?>
	</li>
	<?php
}
?>
</ul>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

<?php
if(!empty($custom_fields)) {
	?>
	if($("#custom-fields-header").length == 0) {
		$("#custom-fields").prepend('<h3 id="custom-fields-header"><?= $txt_header_custom_fields; ?></h3>');
	}
<?php
}
?>
</script>