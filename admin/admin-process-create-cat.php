<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-create-cat.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

$cat_name     = $params['cat_name'];
$plural_name  = $params['plural_name'];
$iconfont_tag = $params['iconfont_tag'];
$cat_order    = $params['cat_order'];
$cat_parent   = $params['cat_parent'];

// trim
$cat_name     = trim($cat_name);
$plural_name  = trim($plural_name);
$iconfont_tag = trim($iconfont_tag);
$cat_order    = trim($cat_order);
$cat_parent   = trim($cat_parent);

// prepare vars
$cat_order  = (is_numeric($cat_order))  ? $cat_order  : 0;
$cat_parent = (is_numeric($cat_parent)) ? $cat_parent : 0;

if(!empty($cat_name)) {
	// insert into db
	$query = "INSERT INTO cats(name, plural_name, parent_id, iconfont_tag, cat_order)
		VALUES(:name, :plural_name, :cat_parent, :iconfont_tag, :cat_order)";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':name'        , $cat_name);
	$stmt->bindValue(':plural_name' , $plural_name);
	$stmt->bindValue(':cat_parent'  , $cat_parent);
	$stmt->bindValue(':iconfont_tag', $iconfont_tag);
	$stmt->bindValue(':cat_order'   , $cat_order);

	if($stmt->execute()) {
		echo $txt_cat_created;
	}
}
else {
	echo $txt_cat_name_empty;
}
