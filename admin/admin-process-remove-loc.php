<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-remove-loc.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$loc_id    = $_POST['loc_id'];
$loc_type = $_POST['loc_type'];

if($loc_type == 'city') {
	$table = 'cities';
	$id_col = 'city_id';
}
else if($loc_type == 'state') {
	$table = 'states';
	$id_col = 'state_id';
}
else if($loc_type == 'country') {
	$table = 'countries';
	$id_col = 'country_id';
}
else {
	$table = '';
}

if(!empty($table)) {
	$query = "DELETE FROM $table WHERE $id_col = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	echo $txt_loc_removed;
}
else {
	echo $txt_loc_remove_problem;
}