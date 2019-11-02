<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$attribute = $_POST['attribute'];
$object    = $_POST['object'];
$value     = $_POST['value'];

// sanitize
$attribute = trim($attribute);
$object    = trim($object);
$value     = trim($value);

// translate vars from jinplace
$loc_type = $attribute;
$loc_id   = $object;

if($loc_type == 'city') {
	// update city name
	$slug = to_slug($value);

	$query = "UPDATE cities SET city_name = :value, slug = :slug WHERE city_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':value', $value);
	$stmt->bindValue(':slug', $slug);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($value)) ? $value : '';
}
if($loc_type == 'city_state') {
	// get state abbr
	$query = "SELECT state_abbr FROM states WHERE state_id = :state_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':state_id', $value);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$state_abbr = $row['state_abbr'];

	// update state country
	$query = "UPDATE cities SET state = :state_abbr, state_id = :state_id WHERE city_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':state_abbr', $state_abbr);
	$stmt->bindValue(':state_id', $value);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($state_abbr)) ? $state_abbr : '';
}
if($loc_type == 'state') {
	// update state name
	$slug = to_slug($value);

	$query = "UPDATE states SET state_name = :value, slug = :slug WHERE state_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':value', $value);
	$stmt->bindValue(':slug', $slug);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($value)) ? $value : '';
}
if($loc_type == 'state_country') {
	// get country abbr
	$query = "SELECT country_abbr FROM countries WHERE country_id = :country_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':country_id', $value);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$country_abbr = $row['country_abbr'];

	// update state country
	$query = "UPDATE states SET country_abbr = :country_abbr, country_id = :country_id WHERE state_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':country_abbr', $country_abbr);
	$stmt->bindValue(':country_id', $value);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($country_abbr)) ? $country_abbr : '';
}
if($loc_type == 'country') {
	// update country name
	$slug = to_slug($value);

	$query = "UPDATE countries SET country_name = :value, slug = :slug WHERE country_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':value', $value);
	$stmt->bindValue(':slug', $slug);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($value)) ? $value : '';
}
if($loc_type == 'country_abbr') {
	// update country abbr
	$query = "UPDATE countries SET country_abbr = :value WHERE country_id = :loc_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':value', $value);
	$stmt->bindValue(':loc_id', $loc_id);
	$stmt->execute();

	$response = (!empty($value)) ? $value : '';
}

echo $response;