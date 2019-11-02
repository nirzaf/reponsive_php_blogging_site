<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-remove-cat.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$cat_id = $_POST['cat_id'];

$query = "UPDATE cats SET cat_status = 0 WHERE id = :cat_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();

echo $txt_cat_removed;