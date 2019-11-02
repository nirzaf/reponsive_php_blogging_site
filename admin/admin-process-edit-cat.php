<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-edit-cat.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

$cat_id       = $params['cat_id'];
$cat_name     = $params['cat_name'];
$plural_name  = $params['plural_name'];
$cat_parent   = $params['cat_parent'];
$iconfont_tag = $params['iconfont_tag'];
$cat_order    = $params['cat_order'];

// trim
$cat_name     = trim($cat_name);
$plural_name  = trim($plural_name);
$cat_parent   = trim($cat_parent);
$iconfont_tag = trim($iconfont_tag);
$cat_order    = trim($cat_order);

// prepare vars
$cat_order    = (is_numeric($cat_order))  ? $cat_order  : 0;
$cat_parent   = (is_numeric($cat_parent)) ? $cat_parent : 0;
$iconfont_tag = htmlspecialchars_decode ($iconfont_tag);

if(!empty($cat_name)) {
	// insert into db
	$query = "UPDATE cats SET
		name         = :name,
		plural_name  = :plural_name,
		parent_id    = :parent_id,
		iconfont_tag = :iconfont_tag,
		cat_order    = :cat_order
		WHERE id = :cat_id";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':cat_id'       , $cat_id);
	$stmt->bindValue(':name'         , $cat_name);
	$stmt->bindValue(':plural_name'  , $plural_name);
	$stmt->bindValue(':parent_id'    , $cat_parent);
	$stmt->bindValue(':iconfont_tag' , $iconfont_tag);
	$stmt->bindValue(':cat_order'    , $cat_order);

	if($stmt->execute()) {
		echo $txt_cat_edited;
	}
}
else {
	echo $txt_cat_name_empty;
}