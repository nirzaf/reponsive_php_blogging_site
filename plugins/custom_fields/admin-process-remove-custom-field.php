<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');
require_once(__DIR__ . '/translation.php');

// csrf check
require_once(__DIR__ . '/../../admin/_admin_inc_request_with_ajax.php');

$field_id = $_POST['field_id'];

$query = "UPDATE custom_fields SET field_status = 0 WHERE field_id = :field_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':field_id', $field_id);
$stmt->execute();

echo $txt_field_removed;