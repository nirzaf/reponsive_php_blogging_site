<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$attribute = (!empty($_POST['attribute'])) ? $_POST['attribute'] : '';
$object    = (!empty($_POST['object']   )) ? $_POST['object']    : '';
$value     = (!empty($_POST['value']    )) ? $_POST['value']     : '';
// after edit, update email
$owner     = (!empty($_POST['owner']    )) ? $_POST['owner']     : '';

// trim
$attribute = trim($attribute);
$object    = trim($object);
$value     = trim($value);
$owner     = trim($owner);

// init response json str
$response = 'Invalid User ID';

if($attribute == 'owner') {
	if(is_numeric($value) && is_numeric($object)) {
		// check new user id
		$query = "SELECT email FROM users WHERE id = :userid";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid', $value);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$new_email = (!empty($row['email'])) ? $row['email'] : '';

		if(!empty($new_email)) {
			// update listing owner id
			$query = "UPDATE places SET userid = :userid WHERE place_id = :place_id";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':userid', $value);
			$stmt->bindValue(':place_id', $object);
			$stmt->execute();

			$response = $value;
		}
	}
}
else {
	if(is_numeric($owner)) {
		// try getting new email
		$query = "SELECT email FROM users WHERE id = :userid";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':userid', $owner);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$new_email = (!empty($row['email'])) ? $row['email'] : '';

		if(!empty($new_email)) {
			$response = $new_email;
		}
	}
}

echo $response;