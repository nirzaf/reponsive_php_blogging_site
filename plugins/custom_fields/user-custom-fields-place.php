<?php
// custom fields
$query = "SELECT
			r.field_value,
			f.*
			FROM rel_place_custom_fields r
			LEFT JOIN custom_fields f ON r.field_id = f.field_id
			WHERE r.place_id = :place_id
			ORDER BY f.field_order";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

$custom_fields = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = $row['field_id'];
	$field_value = $row['field_value'];
	$field_name  = $row['field_name'];
	$field_type  = $row['field_type'];
	$tooltip     = $row['tooltip'];
	$icon        = $row['icon'];
	$required    = $row['required'];
	$field_order = $row['field_order'];

	// sanitize
	$field_value = e($field_value);

	$this_loop_array = array(
		'field_id'    => $field_id,
		'field_name'  => $field_name,
		'field_value' => $field_value,
		'field_type'  => $field_type,
		'tooltip'     => $tooltip,
		'icon'        => $icon,
		'required'    => $required,
		'field_order' => $field_order
	);

	$custom_fields[] = $this_loop_array;
}

$custom_fields_grouped = array();
foreach($custom_fields as $v) {
	$custom_fields_grouped[$v['field_name']][] = array(
		'field_id'    => $v['field_id'],
		'field_value' => $v['field_value'],
		'field_type'  => $v['field_type'],
		'tooltip'     => $v['tooltip'],
		'icon'        => $v['icon'],
		'required'    => $v['required'],
		'field_order' => $v['field_order']
	);
}

// display custom fields
$display_custom_fields = '';
foreach($custom_fields_grouped as $k => $v) {
	$display_custom_fields .= '<div class="custom-field custom-field-' . $v[0]['field_id'] . '">';
	$display_custom_fields .= '<strong>';
	$display_custom_fields .= $k;
	$display_custom_fields .= ':</strong> ';
	$j = 1;
	foreach($v as $k2 => $v2) {
		if(!empty($v2['field_value'])) {
			if($v2['field_type'] == 'url' && filter_var($v2['field_value'], FILTER_VALIDATE_URL)) {
				$display_custom_fields .= "<a href='" . $v2['field_value'] . "'>";
			}

			if($j > 1) {
				$display_custom_fields .= ', ';
			}
			$display_custom_fields .= $v2['field_value'];
			$j++;

			if($v2['field_type'] == 'url' && filter_var($v2['field_value'], FILTER_VALIDATE_URL)) {
				$display_custom_fields .= "</a>";
			}
		}
		else {
			$display_custom_fields .= '';
		}
	}
	$display_custom_fields .= '</div>';
}