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

$profile_id = $frags[1];

/*--------------------------------------------------
Paging
--------------------------------------------------*/
$page = !empty($frags[2]) ? $frags[2] : 1;
$limit = 20;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

/*--------------------------------------------------
Profile details
--------------------------------------------------*/
$query = "SELECT * FROM users WHERE id = :profile_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':profile_id', $profile_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// rows
$first_name         = $row['first_name'];
$last_name          = $row['last_name'];
$city_name          = $row['city_name'];
$country_name       = $row['country_name'];
$created            = $row['created'];
$email              = $row['email'];
$profile_pic_status = $row['profile_pic_status'];

// prepare vars
$email_frags = explode('@', $email);
$email_prefix = $email_frags[0];

if(!empty($first_name) && !empty($last_name)) {
	$profile_display_name = $first_name . ' ' . $last_name;
}
elseif(!empty($first_name) && empty($last_name)) {
	$profile_display_name = $first_name;
}
elseif(empty($first_name) && !empty($last_name)) {
	$profile_display_name = $last_name;
}
else {
	$profile_display_name = $email_prefix;
}

$join_date = date("F j, Y", strtotime($created));

// sanitize vars
$first_name           = e(trim($first_name));
$last_name            = e(trim($last_name));
$profile_display_name = e(trim($profile_display_name));
$city_name            = e(trim($city_name));
$country_name         = e(trim($country_name));

/*--------------------------------------------------
List Reviews
--------------------------------------------------*/

// count how many reviews this user has
$query = "SELECT COUNT(*) AS total_rows FROM reviews WHERE user_id = :profile_id AND (status = 'approved' OR status = 'pending')";
$stmt = $conn->prepare($query);
$stmt->bindValue(':profile_id', $profile_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_reviews = $row['total_rows'];

if($total_reviews > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_reviews, $page);
	$start = $pager->getStartRow();

	// query reviews
	$query = "SELECT
			r.review_id, r.place_id, r.pubdate, r.rating, r.text,
			p.place_name,
			ci.city_id AS review_city_id, ci.slug AS city_slug, ci.city_name,
			ph.dir, ph.filename
		FROM reviews r
		LEFT JOIN places p ON r.place_id = p.place_id
		LEFT JOIN cities ci ON p.city_id = ci.city_id
		LEFT JOIN (SELECT * FROM photos GROUP BY place_id) ph ON ph.place_id = r.place_id
		WHERE r.user_id = :user_id AND r.status = 'approved'
		ORDER BY r.pubdate DESC LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':user_id', $profile_id);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();

	$reviews = array();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$review_id        = $row['review_id'];
		$place_id         = (!empty($row['place_id']       )) ? $row['place_id']       : null;
		$pubdate          = (!empty($row['pubdate']        )) ? $row['pubdate']        : '';
		$place_name       = (!empty($row['place_name']     )) ? $row['place_name']     : 'Read Review';
		$review_city_id   = (!empty($row['review_city_id'] )) ? $row['review_city_id'] : null;
		$review_city_slug = (!empty($row['city_slug']      )) ? $row['city_slug']      : null;
		$review_city_name = (!empty($row['city_name']      )) ? $row['city_name']      : null;
		$dir              = (!empty($row['dir']            )) ? $row['dir']            : null;
		$filename         = (!empty($row['filename']       )) ? $row['filename']       : null;
		$rating           = (!empty($row['rating']         )) ? $row['rating']         : 0;
		$text             = (!empty($row['text']           )) ? $row['text']           : '';

		// prepare vars
		$text       = e($text);
		$place_name = e($place_name);

		// link to the place's page
		$link_url = $baseurl . '/' . $review_city_slug . '/place/' . $place_id . '/' . to_slug($place_name);

		// thumb
		if(!empty($row['filename'])) {
			$thumb_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;
		}
		elseif(!empty($row['best_photo'])) {
			$thumb_url = $best_photo;
		}
		else {
			$thumb_url = $baseurl . '/imgs/blank.png';
		}

		$reviews[] = array(
			'review_id'        => $review_id,
			'place_id'         => $place_id,
			'link_url'         => $link_url,
			'pubdate'          => $pubdate,
			'place_name'       => $place_name,
			'thumb_url'        => $thumb_url,
			'review_city_id'   => $review_city_id,
			'review_city_slug' => $review_city_slug,
			'review_city_name' => $review_city_name,
			'rating'           => $rating,
			'text'             => $text
		);
	}
} // end if($total_reviews > 0)

/*--------------------------------------------------
Profile pic
--------------------------------------------------*/
$profile_pic = '';
$folder = floor($profile_id / 1000) + 1;

if(strlen($folder) < 1) {
	$folder = '999';
}

// get profile pic filename
$profile_pic_path = $profile_full_folder . '/' . $folder . '/' . $profile_id;

foreach($img_exts as $v) {
	if(file_exists($pic_basepath . '/' . $profile_pic_path . '.' . $v)) {
		$profile_pic = $pic_baseurl . '/' . $profile_pic_path . '.' . $v;
		break;
	}
}

if(!empty($profile_pic) && $profile_pic_status == 'approved') {
	$bg_img = $profile_pic;
}
else {
	$bg_img = $baseurl . '/imgs/blank.png' ;
}

/*--------------------------------------------------
Translations substitutions
--------------------------------------------------*/
$txt_html_title = str_replace('%profile_display_name%', $profile_display_name, $txt_html_title);
$txt_meta_desc  = str_replace('%profile_display_name%', $profile_display_name, $txt_meta_desc);
$txt_joined_on  = str_replace('%join_date%', $join_date, $txt_joined_on);

// template file
require_once(__DIR__ . '/templates/tpl_profile.php');