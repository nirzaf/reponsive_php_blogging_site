<?php
require_once(__DIR__ . '/translation.php');
require_once(__DIR__ . '/../../user/user_area_inc.php');

/*
This file is requested by tpl_add-place.php and tpl_edit-place.php
It checks if there are global custom fields and generates the corresponding html code
It also inserts javascript which calls user-get-custom-fields when category field is changed
*/

// find global fields
if(empty($place_id)) {
	$query = "SELECT f.*
				FROM custom_fields f
				LEFT JOIN rel_cat_custom_fields rc
					ON f.field_id = rc.field_id
				WHERE rc.rel_id IS NULL AND field_status = 1
				ORDER BY f.field_order";
}
else {
	$query = "SELECT s.*, rp.field_value FROM
				(
				SELECT f.*
				FROM custom_fields f
				LEFT JOIN rel_cat_custom_fields rc
					ON f.field_id = rc.field_id
				WHERE rc.rel_id IS NULL AND f.field_status = 1
				ORDER BY f.field_id
				) s
			LEFT JOIN
				(
				SELECT place_id, field_id AS field_id2, GROUP_CONCAT(field_value SEPARATOR ':::') AS field_value
				FROM rel_place_custom_fields WHERE place_id = :place_id GROUP BY field_id
				) rp
				ON rp.field_id2 = s.field_id";
}

$stmt = $conn->prepare($query);
if(!empty($place_id)) {
	$stmt->bindValue(':place_id', $place_id);
}
$stmt->execute();

$custom_fields = array();
$custom_fields_ids = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = (!empty($row['field_id'   ])) ? $row['field_id'   ] : 0;
	$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
	$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : 'text';
	$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
	$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
	$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';
	$required    = (!empty($row['required'   ])) ? $row['required'   ] : 0;
	$searchable  = (!empty($row['searchable' ])) ? $row['searchable' ] : 0;
	$field_value = (!empty($row['field_value'])) ? $row['field_value'] : '';

	$custom_fields[] = array(
		'field_id'    => $field_id,
		'field_name'  => $field_name,
		'field_type'  => $field_type,
		'values_list' => $values_list,
		'tooltip'     => $tooltip,
		'icon'        => $icon,
		'required'    => $required,
		'searchable'  => $searchable,
		'field_value' => $field_value
	);

	$custom_fields_ids[] = $field_id;
}

// now get cat custom fields for this place's category
// (*)only when editing a place
$cat_fields = array();
if(!empty($place_id) && !empty($cat_id)) {
	$query = "SELECT f.*, GROUP_CONCAT(field_value SEPARATOR ':::') AS field_value
		FROM rel_cat_custom_fields r
		LEFT JOIN custom_fields f ON r.field_id = f.field_id
		LEFT JOIN rel_place_custom_fields rp ON rp.field_id = r.field_id AND rp.place_id = :place_id
		WHERE r.cat_id = :cat_id AND field_status = 1";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->bindValue(':cat_id'  , $cat_id);
	$stmt->execute();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$field_id    = (!empty($row['field_id'   ])) ? $row['field_id'   ] : 0;
		$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
		$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : 'text';
		$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
		$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
		$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';
		$required    = (!empty($row['required'   ])) ? $row['required'   ] : 0;
		$searchable  = (!empty($row['searchable' ])) ? $row['searchable' ] : 0;
		$field_value = (!empty($row['field_value'])) ? $row['field_value'] : '';

		if(!empty($field_id)) {
			$cat_fields[] = array(
				'field_id'    => $field_id,
				'field_name'  => $field_name,
				'field_type'  => $field_type,
				'values_list' => $values_list,
				'tooltip'     => $tooltip,
				'icon'        => $icon,
				'required'    => $required,
				'searchable'  => $searchable,
				'field_value' => $field_value
			);

			$custom_fields_ids[] = $field_id;
		}
	}
}

if(!empty($custom_fields_ids)) {
	$custom_fields_ids = implode(',', $custom_fields_ids);
}
else {
	$custom_fields_ids = '';
}
?>
<input type="hidden" name="custom_fields_ids" id="custom_fields_ids" value="<?= $custom_fields_ids; ?>">
<div class="form-row">
	<div class="form-row-full" id="custom-fields">
		<?php
		// global custom fields
		if(!empty($custom_fields)) {
			?>
			<h3 id="custom-fields-header"><?= $txt_header_custom_fields; ?></h3>

			<ul id="global-fields">
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
					$field_value = $v['field_value'];
					$required    = ($required == 1) ? 'required' : '';

					if(!empty($tooltip)) {
						$tooltip = "<a class='the-tooltip' data-toggle='tooltip' data-placement='top' title='$tooltip'><i class='fa fa-question-circle'></i></a>";
					}
					else {
						$tooltip = '';
					}

					// explode values
					$values_arr      = array();
					$field_value_arr = array();
					if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
						$values_arr      = explode(';', $values_list);
						$field_value_arr = explode(':::', $field_value);
					}
					?>
					<li id="li-field-<?= $field_id; ?>" class="block">
						<?php
						if($field_type == 'radio') {
							?>
							<label><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<?php
							foreach($values_arr as $v) {
								$checked = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $checked = 'checked'; }
								?>
								<label><input type="radio" name="field_<?= $field_id; ?>" value="<?= $v; ?>" <?= $checked; ?> <?= $required; ?>> <?= $v; ?></label>
								<?php
							}
						}
						if($field_type == 'select') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<select name="field_<?= $field_id; ?>" <?= $required; ?>>
							<?php
							foreach($values_arr as $v) {
								$selected = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $selected = 'selected'; }
								?>
								<option value="<?= $v; ?>" <?= $selected; ?>><?= $v; ?>
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
								$checked = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $checked = 'checked'; }
								?>
								<label><input type="checkbox" name="field_<?= $field_id; ?>[]" value="<?= $v; ?>" <?= $checked; ?>> <?= $v; ?></label>
								<?php
							}
						}
						if($field_type == 'text') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?> value="<?= $field_value; ?>">
							<?php
						}
						if($field_type == 'multiline') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<textarea name="field_<?= $field_id; ?>" <?= $required; ?>><?= $field_value; ?></textarea>
							<?php
						}
						if($field_type == 'url') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?> value="<?= $field_value; ?>">
							<?php
						}
						?>
					</li>
					<?php
				}
				?>
			</ul><!-- end #global-fields -->

			<?php
		} // end if(!empty($custom_fields))

		if(!empty($cat_fields)) {
			if(empty($custom_fields)) { // show h3 if previous $custom_fields is empty
				?>
				<h3 id="custom-fields-header"><?= $txt_header_custom_fields; ?></h3>
				<?php
			}
			?>
			<ul id="cat-fields">
				<?php
				foreach($cat_fields as $v) {
					$field_id    = $v['field_id'];
					$field_name  = $v['field_name'];
					$field_type  = $v['field_type'];
					$values_list = $v['values_list'];
					$tooltip     = $v['tooltip'];
					$icon        = $v['icon'];
					$required    = $v['required'];
					$searchable  = $v['searchable'];
					$field_value = $v['field_value'];
					$required    = ($required == 1) ? 'required' : '';

					if(!empty($tooltip)) {
						$tooltip = "<a class='the-tooltip' data-toggle='tooltip' data-placement='top' title='$tooltip'><i class='fa fa-question-circle'></i></a>";
					}
					else {
						$tooltip = '';
					}

					// explode values
					$values_arr      = array();
					$field_value_arr = array();
					if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
						$values_arr      = explode(';', $values_list);
						$field_value_arr = explode(':::', $field_value);
					}
					?>
					<li id="li-field-<?= $field_id; ?>" class="block">
						<?php
						if($field_type == 'radio') {
							?>
							<label><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<?php
							foreach($values_arr as $v) {
								$checked = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $checked = 'checked'; }
								?>
								<label><input type="radio" name="field_<?= $field_id; ?>" value="<?= $v; ?>" <?= $checked; ?> <?= $required; ?>> <?= $v; ?></label>
								<?php
							}
						}
						if($field_type == 'select') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<select name="field_<?= $field_id; ?>" <?= $required; ?>>
							<?php
							foreach($values_arr as $v) {
								$checked = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $selected = 'selected'; }
								?>
								<option value="<?= $v; ?>" <?= $selected; ?>><?= $v; ?>
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
								$checked = '';
								$v = e(trim($v));
								if(in_array($v, $field_value_arr)) { $checked = 'checked'; }
								?>
								<label><input type="checkbox" name="field_<?= $field_id; ?>[]" value="<?= $v; ?>" <?= $checked; ?>> <?= $v; ?></label>
								<?php
							}
						}
						if($field_type == 'text') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?> value="<?= $field_value; ?>">
							<?php
						}
						if($field_type == 'multiline') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<textarea name="field_<?= $field_id; ?>" <?= $required; ?>><?= $field_value; ?></textarea>
							<?php
						}
						if($field_type == 'url') {
							?>
							<label for="field_<?= $field_id; ?>"><?= $field_name; ?> <?= $tooltip; ?></label><br>
							<input type="text" name="field_<?= $field_id; ?>" <?= $required; ?> value="<?= $field_value; ?>">
							<?php
						}
						?>
					</li>
					<?php
				}
				?>
			</ul><!-- end #cat-fields -->
			<?php
		} // end if(!empty($cat_fields))
		?>
	</div><!-- end #custom-fields -->
</div><!-- end .form-row -->

<script>
// add css
var css_file = baseurl + '/plugins/custom_fields/styles.css';
$('head').append('<link rel="stylesheet" type="text/css" href="' + css_file + '">');

// listener to category change event
$('#category_id').change(function(e) {
	e.preventDefault();
	var cat_id = $(this).val();
	var place_id =
		<?php
		if(!empty($place_id)) {
			echo $place_id;
		}
		else {
			echo '0';
		}
		?>
	;

	var post_url = baseurl + '/plugins/custom_fields/user-get-custom-fields.php';
	$.post(post_url, {
		cat_id: cat_id,
		place_id: place_id,
		custom_fields_ids: '<?= $custom_fields_ids; ?>'
		},
		function(data) {
			// remove #custom_fields_ids hidden input
			$('#custom_fields_ids').fadeOut(300, function() { $(this).remove(); });

			// remove previous #cat-fields
			$("#cat-fields").fadeOut(300, function() { $(this).remove(); });

			// check if header exists, if not, create it
			/*
			if($("#custom-fields-header").length == 0) {
				$("#custom-fields").prepend('<h3 id="custom-fields-header"><?= $txt_header_custom_fields; ?></h3>');
			}
			*/

			// append html response
			$('#custom-fields').append(data).hide().fadeIn();
		}
	);
});
// END CUSTOM FIELDS
</script>