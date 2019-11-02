<?php
require_once(__DIR__ . '/config.php');

$user_lat = (!empty($_POST['lat'])) ? $_POST['lat'] : '';
$user_lng = (!empty($_POST['lng'])) ? $_POST['lng'] : '';
$max_dist = 150; // This is the maximum distance (in miles) away from $user_lat, $user_lng in which to search

$query = "SELECT city_id, city_name, state, state_id, slug, lat, lng, 3956 * 2 *
				ASIN(SQRT( POWER(SIN((:user_lat1 - lat) * pi() / 180 / 2), 2)
				+ COS(:user_lat2 * pi() / 180 ) * COS(lat * pi() / 180)
				*POWER(SIN((:user_lng1 - lng) * pi() / 180 / 2), 2))) AS distance
				FROM cities
				WHERE lng BETWEEN (:user_lng2 - $max_dist / COS(RADIANS(:user_lat3)) * 69)
				AND (:user_lng3 + $max_dist / COS(RADIANS(:user_lat4)) * 69)
				AND lat BETWEEN (:user_lat5 - ($max_dist / 69))
				AND (:user_lat6 + ($max_dist / 69))
				HAVING distance < $max_dist
				ORDER BY distance LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bindValue(':user_lat1', $user_lat);
$stmt->bindValue(':user_lat2', $user_lat);
$stmt->bindValue(':user_lat3', $user_lat);
$stmt->bindValue(':user_lat4', $user_lat);
$stmt->bindValue(':user_lat5', $user_lat);
$stmt->bindValue(':user_lat6', $user_lat);
$stmt->bindValue(':user_lng1', $user_lng);
$stmt->bindValue(':user_lng2', $user_lng);
$stmt->bindValue(':user_lng3', $user_lng);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$city_id = (!empty($row['city_id'])) ? $row['city_id'] : $default_loc_id;

// init response array
$response = array(
		'city_id' => $city_id
		);

// json encode
echo json_encode($response);
