<?php
require_once(__DIR__ . '/inc/config.php');
// example.com/place/[place_id]/central-park
# 3)
# http://www.example.com/city-name/place/2537-place-slug
# must rewrite to
# http://www.example.com/place/2537 // 2537 is the place_id
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}
// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}
$frags = explode("/", $frags);
$place_id = $frags[1];

// check if place id is numeric
if(is_numeric($place_id)) {
	require_once 'place_db.php';
}
else {
	throw new Exception('Place id must be numeric');
}

// $cats_arr is created in place_db.php
if(!empty($cats_arr)) {
	foreach($cats_arr as $k => $v) {
		$cat_id = $v['id'];

		$query = "INSERT IGNORE INTO rel_place_cat(
			place_id,
			cat_id,
			city_id)
		VALUES(
			:place_id,
			:cat_id,
			:city_id)";

		$stmt = $conn->prepare($query);
		$stmt->bindValue(':place_id', $place_id);
		$stmt->bindValue(':cat_id', $cat_id);
		$stmt->bindValue(':city_id', $city_id);
		$stmt->execute();
	}
}

// template file
if(!$place_404) {
	// html title
	$txt_html_title = str_replace('%place_name%', $place_name, $txt_html_title);
	$txt_html_title = str_replace('%cat_name%'  , $cat_name  , $txt_html_title);
	$txt_html_title = str_replace('%city_name%' , $city_name , $txt_html_title);
	$txt_html_title = str_replace('%state_abbr%', $state_abbr, $txt_html_title);
	$txt_meta_desc  = str_replace('%place_name%', $place_name, $txt_meta_desc);
	$txt_meta_desc  = str_replace('%cat_name%'  , $cat_name  , $txt_meta_desc);
	$txt_meta_desc  = str_replace('%city_name%' , $city_name , $txt_meta_desc);
	$txt_meta_desc  = str_replace('%state_abbr%', $state_abbr, $txt_meta_desc);

	// vars
	$phone = str_replace('+1 ', '', $phone);
	$website = (!empty($website)) ?
		'<a href="' . $website . '" rel="nofollow" target="_blank">' . $website . '</a>'
		: '';
	$place_slug = to_slug($place_name);

	// canonical url
	$canonical = '';
	if(!empty($city_slug)) {
		$canonical = "$baseurl/$city_slug/place/$place_id/$place_slug";
	}
	else {
		$canonical = "$baseurl/$default_country_code/place/$place_id/$place_slug";
	}

	$txt_about = str_replace('%place_name%', $place_name, $txt_about);
	$txt_about = str_replace('%cat_name%', $cat_name, $txt_about);

	if(!empty($city_name)) {
		$txt_about = str_replace('%city_name%', $city_name, $txt_about);
	}

	elseif(!empty($county_name)) {
		$txt_about = str_replace('%city_name%', $county_name, $txt_about);
	}

	else {
		$txt_about = str_replace('%city_name%', '', $txt_about);
	}

	if(!empty($state_abbr)) {
		$txt_about = str_replace('%state_abbr%', $state_abbr, $txt_about);
	}

	else {
		$txt_about = str_replace('%state_abbr%', $country_name, $txt_about);
	}

	// translation substitutions
	$txt_reviews = str_replace('%place_name%', $place_name, $txt_reviews);

	// plugins: custom fields
	$display_custom_fields = '';
	if(file_exists(__DIR__ . '/plugins/custom_fields/user-custom-fields-place.php')) {
		include_once __DIR__ . '/plugins/custom_fields/user-custom-fields-place.php';
	}

	// template
	require_once __DIR__ . '/templates/tpl_place.php';
} // end if(!$place_404)
else {
	header("HTTP/1.0 404 Not Found");
	require_once __DIR__ . '/templates/tpl_place_404.php';
}
