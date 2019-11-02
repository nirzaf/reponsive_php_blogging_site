<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/translation.php');

// find global fields
$query = "SELECT f.*
			FROM custom_fields f
			LEFT JOIN rel_cat_custom_fields r
			ON f.field_id = r.field_id
			WHERE r.rel_id IS NULL AND f.searchable = 1 AND f.field_status = 1
			ORDER BY f.field_order DESC";

// instead find all custom fields
$query = "SELECT f.*
			FROM custom_fields f
			WHERE f.searchable = 1 AND f.field_status = 1
			ORDER BY f.field_order DESC";
$stmt = $conn->prepare($query);
$stmt->execute();

$custom_fields = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = $row['field_id'];
	$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
	$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : '';
	$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
	$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
	$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';

	if(!empty($field_name) && !empty($field_type)) {
		$custom_fields[] = array(
			'field_id'    => $field_id,
			'field_name'  => $field_name,
			'field_type'  => $field_type,
			'values_list' => $values_list,
			'tooltip'     => $tooltip,
			'icon'        => $icon
		);
	}
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

/*--------------------------------------------------
include template file
--------------------------------------------------*/
require_once(__DIR__ . '/tpl_advanced-search.php');
