<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// path info
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

// paging vars
$page = !empty($frags[2]) ? $frags[2] : 1;
$limit = $items_per_page;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// query reviews, count total reviews for current user
$query = "SELECT COUNT(*) AS total_rows FROM reviews WHERE user_id = :userid AND status != 'trashed'";
$stmt = $conn->prepare($query);
$stmt->bindValue(':userid', $userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
	$start = $pager->getStartRow();

	$query = "SELECT
		r.review_id, r.place_id, r.pubdate, r.rating, r.text,
		p.place_name,
		ci.city_id AS review_city_id, ci.slug AS city_slug, ci.city_name,
		ph.dir, ph.filename
		FROM reviews r
		LEFT JOIN places p ON r.place_id = p.place_id
		LEFT JOIN cities ci ON p.city_id = ci.city_id
		LEFT JOIN (SELECT * FROM photos GROUP BY place_id) ph ON ph.place_id = r.place_id
		WHERE r.user_id = :user_id AND (r.status = 'approved' OR r.status = 'pending')
		ORDER BY r.pubdate DESC LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':user_id', $userid);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();

	$reviews = array();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$review_id        = $row['review_id'];
		$place_id         = (!empty($row['place_id']       )) ? $row['place_id']       : null;
		$pubdate          = (!empty($row['pubdate']        )) ? $row['pubdate']        : '';
		$place_name       = (!empty($row['place_name']     )) ? $row['place_name']     : '';
		$review_city_id   = (!empty($row['review_city_id'] )) ? $row['review_city_id'] : null;
		$review_city_slug = (!empty($row['city_slug']      )) ? $row['city_slug']      : null;
		$review_city_name = (!empty($row['city_name']      )) ? $row['city_name']      : null;
		$dir              = (!empty($row['dir']            )) ? $row['dir']            : null;
		$filename         = (!empty($row['filename']       )) ? $row['filename']       : null;
		$rating           = (!empty($row['rating']         )) ? $row['rating']         : 0;
		$text             = (!empty($row['text']           )) ? $row['text']           : '';

		// prepare vars
		$place_name = e($place_name);
		$text       = e($text);
		$place_name = (empty($place_name)) ? 'Untitled Place' : $place_name;

		// link to the place's page
		$link_url = $baseurl . '/' . $review_city_slug . '/place/' . $place_id . '/' . to_slug($place_name);

		// thumb
		if(!empty($filename)) {
			$thumb_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;
		}
		else {
			$thumb_url = $baseurl . '/imgs/empty.png';
		}

		// add to array
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
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_my-reviews.php');