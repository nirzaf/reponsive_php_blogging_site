<?php
require_once(__DIR__ . '/inc/config.php');

// query is the default name of the request parameter that contains the query.
$city_input = (!empty($_GET['query'])) ? $_GET['query'] : '';

if(!empty($city_input)) {
	$stmt = $conn->prepare("SELECT * FROM cities WHERE city_name LIKE :city_input LIMIT 24");
	$stmt->bindValue(':city_input', '%'.$city_input.'%');
	$stmt->execute();

	$response = '
		{
		"results":
		[';

	$rowCount = 0;
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$rowCount++;

		if($rowCount != $stmt->rowCount()) {
			$response .= '{ "id": "' . $row['city_id'] . '", "text": "' . $row['city_name'] . ', ' . $row['state'] . '" },';
		}
		else {
			$response .= '{ "id": "' . $row['city_id'] . '", "text": "' . $row['city_name'] . ',' . $row['state'] . '" }';
		}
	}

	$response .= '
		]
		}';

	// $response = '{"results":[{"id":0,"text":"text name0"}]}';

	echo $response;
}