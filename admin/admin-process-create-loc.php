<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-create-loc.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

$loc_type = $params['loc_type'];

if($loc_type == 'city') {
	$city_name  = $params['city_name'];
	$slug       = to_slug($city_name);
	$state      = $params['state']; // $value = "$state_id,$state_abbr";
	$state_id   = '';
	$state_abbr = '';

	if(!empty($state)) {
		$state      = explode(',', $state);
		$state_id   = $state[0];
		$state_abbr = $state[1];
	}

	// trim
	$city_name  = trim($city_name);
	$slug       = trim($slug);
	$state_id   = trim($state_id);
	$state_abbr = trim($state_abbr);

	if(!empty($city_name)) {
		if(!empty($state)) {
			// insert into db
			$query = "INSERT INTO cities(city_name, state, state_id, slug)
				VALUES(:city_name, :state, :state_id, :slug)";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':city_name', $city_name);
			$stmt->bindValue(':state', $state_abbr);
			$stmt->bindValue(':state_id', $state_id);
			$stmt->bindValue(':slug', $slug);

			if($stmt->execute()) {
				echo $txt_city_created;
			}
		}
		else {
			echo $txt_create_state;
		}
	}
	else {
		echo $txt_city_name_empty;
	}
}

elseif($loc_type == 'state') {
	$state_name   = $params['state_name'];
	$state_abbr   = $params['state_abbr'];
	$slug         = to_slug($state_name);
	$country      = $params['country']; // $value = "$country_id,$country_abbr";
	$country_id   = '';
	$country_abbr = '';

	if(!empty($country)) {
		$country      = explode(',', $country);
		$country_id   = $country[0];
		$country_abbr = $country[1];
	}

	// trim
	$state_name   = trim($state_name);
	$state_abbr   = trim($state_abbr);
	$slug         = trim($slug);
	$country_id   = trim($country_id);
	$country_abbr = trim($country_abbr);

	if(!empty($state_name) && !empty($state_abbr)) {
		if(!empty($country)) {
			// insert into db
			$query = "INSERT INTO states(state_name, state_abbr, slug, country_abbr, country_id)
				VALUES(:state_name, :state_abbr, :slug, :country_abbr, :country_id)";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':state_name', $state_name);
			$stmt->bindValue(':state_abbr', $state_abbr);
			$stmt->bindValue(':slug', $slug);
			$stmt->bindValue(':country_abbr', $country_abbr);
			$stmt->bindValue(':country_id', $country_id);

			if($stmt->execute()) {
				echo $txt_state_created;
			}
		}
		else {
			echo $txt_create_country;
		}
	}
	else {
		echo $txt_state_name_empty;
	}
}

elseif($loc_type == 'country') {
	$country_name = $params['country_name'];
	$country_abbr = $params['country_abbr'];

	// trim
	$country_name = trim($country_name);
	$country_abbr = trim($country_abbr);

	// slug
	$slug = to_slug($country_name);

	if(!empty($country_name) && !empty($country_abbr)) {
		// insert into db
		$query = "INSERT INTO countries(country_name, country_abbr, slug)
			VALUES(:country_name, :country_abbr, :slug)";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':country_name', $country_name);
		$stmt->bindValue(':country_abbr', $country_abbr);
		$stmt->bindValue(':slug', $slug);

		if($stmt->execute()) {
			echo $txt_country_created;
		}
	}
	else {
		echo $txt_country_name_empty;
	}
}
else {

}
