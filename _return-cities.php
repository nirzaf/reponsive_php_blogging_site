<?php
require_once(__DIR__ . '/inc/config.php');

$city_input = $_GET['query']; // query is the default name of the request parameter that contains the query.

$stmt = $conn->prepare("SELECT * FROM cities WHERE city_name LIKE :city_input LIMIT 7");
$stmt->bindValue(':city_input', '%'.$city_input.'%');
$stmt->execute();

$response = '
	{
	"suggestions":
	[';

$rowCount = 0;
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
	$rowCount++;

	if($rowCount != $stmt->rowCount())
		{
		$response .= '{ "value": "' . $row['city_name'] . ', ' . $row['state'] . '", "data": "' . $row['lat'] . ',' . $row['lng'] . '" },';
		}
	else
		{
		$response .= '{ "value": "' . $row['city_name'] . ', ' . $row['state'] . '", "data": "' . $row['lat'] . ',' . $row['lng'] . '" }';
		}
	}

$response .= '
	]
	}';

//return format

/*
{
    "suggestions":
	[
        { "value": "United Arab Emirates", "data": "AE" },
        { "value": "United Kingdom",       "data": "UK" },
        { "value": "United States",        "data": "US" }
    ]
}
*/

echo $response;