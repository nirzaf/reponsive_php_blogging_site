<?php
require_once(__DIR__ . '/inc/config.php');

$place_id = (!empty($_POST['place_id']))     ? $_POST['place_id']     : '';
$rating   = (!empty($_POST['review_score'])) ? $_POST['review_score'] : null;
$review   = (!empty($_POST['review']))       ? $_POST['review']       : '';

if(is_numeric($place_id)) {
	$stmt = $conn->prepare('
	INSERT INTO reviews(
		place_id,
		user_id,
		rating,
		text
		)
	VALUES(
		:place_id,
		:user_id,
		:rating,
		:text
		)
	');
}

$stmt->bindValue(':place_id', $place_id);
$stmt->bindValue(':user_id', $userid);
$stmt->bindValue(':rating', $rating);
$stmt->bindValue(':text', $review);
$stmt->execute();

echo 'Review submitted';
