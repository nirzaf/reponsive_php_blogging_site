<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');
require_once(__DIR__ . '/translation.php');

// csrf check
require_once(__DIR__ . '/../../admin/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

// posted vars
$field_name  = (!empty($params['field_name']))  ? $params['field_name']  : '';
$field_type  = (!empty($params['field_type']))  ? $params['field_type']  : '';
$tooltip     = (!empty($params['tooltip']))     ? $params['tooltip']     : '';
$icon        = (!empty($params['icon']))        ? $params['icon']        : '';
$required    = (!empty($params['required']))    ? $params['required']    : 0;
$searchable  = (!empty($params['searchable']))  ? $params['searchable']  : 0;
$values_list = (!empty($params['values_list'])) ? $params['values_list'] : '';
$field_order = (!empty($params['field_order'])) ? $params['field_order'] : 0;
$categories  = (!empty($params['cats']))        ? $params['cats']        : array();

// trim
$field_name  = trim($field_name);
$field_type  = trim($field_type);
$tooltip     = trim($tooltip);
$icon        = trim($icon);

// convert to integers
$required    = intval($required);
$searchable  = intval($searchable);
$field_order = intval($field_order);

// allowed field types
$allowed_types = array('radio', 'checkbox', 'select', 'text', 'multiline', 'url');

// field types that ignore values_list
$ignore_values_list = array('text', 'multiline', 'url');

if(in_array($field_type, $ignore_values_list)) {
	$values_list = '';
}

// count total cats
$query = "SELECT COUNT(*) AS total_cats FROM cats WHERE cat_status = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_cats = $row['total_cats'];

// init result message
$result_message = '';

// check if field type submitted is allowed
if(!in_array($field_type, $allowed_types)) {
	$result_message .= '- Field type is not allowed<br>';
}

// check if this field is set to show on all cats
$is_global_field = 0;
if($total_cats == count($categories)) {
	$is_global_field = 1;
}

if(empty($result_message)) {
	try {
		$conn->beginTransaction();

		// update table 'custom_fields'
		$query = "INSERT INTO custom_fields(field_name, field_type, values_list, tooltip, icon, required, searchable, field_order) VALUES(
			:field_name,
			:field_type,
			:values_list,
			:tooltip,
			:icon,
			:required,
			:searchable,
			:field_order
			)";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':field_name', $field_name);
		$stmt->bindValue(':field_type', $field_type);
		$stmt->bindValue(':values_list', $values_list);
		$stmt->bindValue(':tooltip', $tooltip);
		$stmt->bindValue(':icon', $icon);
		$stmt->bindValue(':required', $required);
		$stmt->bindValue(':searchable', $searchable);
		$stmt->bindValue(':field_order', $field_order);
		$stmt->execute();

		$field_id = $conn->lastInsertId();

		// isnert into table 'rel_cat_custom_fields'
		if(!empty($categories)) {
			// now insert
			// only if it's not global field
			if(!$is_global_field) {
				$query = "INSERT INTO rel_cat_custom_fields(cat_id, field_id) VALUES";
				foreach($categories as $k => $v) {
					$v = intval($v);
					if($k == 0) {
						$query .= "( $v, $field_id)";
					}
					else {
						$query .= ",( $v, $field_id)";
					}
				}
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':field_id', $field_id);
				$stmt->execute();
			}
		}

		$conn->commit();
		$result_message = $txt_field_created;
	} // end try block ()
	catch(PDOException $e) {
		$conn->rollBack();
		$result_message = $e->getMessage();
	}
} // end if(empty($result_message))
?>
<div class="block"><?= $result_message; ?></div>
<div class="block"><a href="<?= $baseurl; ?>/admin/plugin/custom_fields"><?= $txt_goto; ?></a></div>