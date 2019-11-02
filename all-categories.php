<?php
require_once(__DIR__ . '/inc/config.php');

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

// get city details
$is_empty_frags = false;
$suggest_cats_in_city = '';
if(!empty($frags[2])) {
	$stmt = $conn->prepare('SELECT * FROM cities WHERE city_id = :city_id');
	$stmt->bindValue(':city_id', $frags[2]);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$city_id    = $frags[2];
	$city_name  = $row['city_name'];
	$city_slug  = $row['slug'];
	$state_abbr = $row['state'];
	$state_id   = $row['state_id'];
	$lat        = $row['lat'];
	$lng        = $row['lng'];
}
else {
	$is_empty_frags = true;
	$city_id        = 0;
	$city_name      = '';
	$city_slug      = 'us';
	$state_abbr     = '';
	$state_id       = '';

	if(!empty($_COOKIE['city_id']) && !empty($_COOKIE['city_name']) && !empty($_COOKIE['city_slug'])) {
		$suggest_cats_in_city = $txt_suggest_city;
		$suggest_cats_in_city = str_replace('%city_name%' , $_COOKIE['city_name'] , $suggest_cats_in_city);
		$suggest_cats_in_city =
			'<a href="' . $baseurl . '/all-categories/' . $_COOKIE['city_slug'] . '/' . $_COOKIE['city_id'] . '">' . $suggest_cats_in_city . '</a>';
	}
}

if($city_id != 0) {
	$loc_slug = $city_slug;
	$loc_id   = $city_id;
	$loc_type = 'c';
}
else {
	$loc_slug = $default_country_code;
	$loc_id   = 0;
	$loc_type = 'n';
}

// count items in each cat
if($city_id != 0) {
	$query = "SELECT r.cat_id, COUNT(*) AS cat_count
			FROM rel_place_cat r
			INNER JOIN places p ON r.place_id = p.place_id
			WHERE r.city_id = :city_id AND p.status = 'approved'
			GROUP BY cat_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $city_id);
	$stmt->execute();
}
else {
	$query = "SELECT r.cat_id, COUNT(*) AS cat_count
			FROM rel_place_cat r
			INNER JOIN places p ON r.place_id = p.place_id
			WHERE p.status = 'approved'
			GROUP BY cat_id";
	$stmt = $conn->prepare($query);
	$stmt->execute();
}

// init items count arr
$cat_items_count = array();

// build array with items count for each cat
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$cat_items_count[$row['cat_id']] = $row['cat_count'];
}

// select all cats and build a flat array
$cats_arr = array();
$query = "SELECT * FROM cats WHERE cat_status = 1 ORDER BY parent_id, cat_order";
$stmt = $conn->prepare($query);
$stmt->execute();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$cur_loop_arr = array(
		'cat_id'       => $row['id'],
		'cat_name'     => $row['name'],
		'plural_name'  => $row['plural_name'],
		'parent_id'    => $row['parent_id'],
		'iconfont_tag' => $row['iconfont_tag']
	);
	$cats_arr[] = $cur_loop_arr;
}

// build an array with cats grouped by parent_id
$cats_grouped_by_parent = group_cats_by_parent($cats_arr);

//print_r2($cats_grouped_by_parent);

// translation replacements
if(!empty($city_id)) {
	// case: city defined
	$txt_html_title_1 = str_replace('%city_name%' , $city_name , $txt_html_title_1);
	$txt_html_title_1 = str_replace('%state_abbr%', $state_abbr, $txt_html_title_1);
	$txt_meta_desc_1  = str_replace('%city_name%' , $city_name , $txt_meta_desc_1);
	$txt_meta_desc_1  = str_replace('%state_abbr%', $state_abbr, $txt_meta_desc_1);
	$txt_main_title_1 = str_replace('%city_name%' , $city_name , $txt_main_title_1);
	$txt_main_title_1 = str_replace('%state_abbr%', $state_abbr, $txt_main_title_1);

	$txt_html_title = $txt_html_title_1;
	$txt_meta_desc  = $txt_meta_desc_1;
	$txt_main_title = $txt_main_title_1;
}
else {
	$txt_html_title = $txt_html_title_2;
	$txt_meta_desc  = $txt_meta_desc_2;
	$txt_main_title = $txt_main_title_2;
}

// template file
require_once(__DIR__ . '/templates/tpl_all-categories.php');