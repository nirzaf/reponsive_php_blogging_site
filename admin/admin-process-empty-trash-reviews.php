<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$query = "DELETE FROM reviews WHERE status = 'trashed'";
$stmt = $conn->prepare($query);
$stmt->execute();