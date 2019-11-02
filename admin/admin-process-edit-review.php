<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$review_id   = $_POST['attribute'];
$review_text = $_POST['value'];

$query = "UPDATE reviews SET text = :review_text WHERE review_id = :review_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':review_id', $review_id);
$stmt->bindValue(':review_text', $review_text);
$stmt->execute();

echo $review_text;