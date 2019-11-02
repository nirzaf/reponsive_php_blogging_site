<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-remove-page.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$page_id = $_POST['page_id'];

$query = "DELETE FROM pages WHERE page_id = :page_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_id', $page_id);
$stmt->execute();

echo $txt_page_removed;