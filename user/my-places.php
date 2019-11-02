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

// query ads, count total places for current user
$query = "SELECT COUNT(*) AS total_rows FROM places
	WHERE status != 'trashed' AND paid = 1 AND userid = :userid";
$stmt = $conn->prepare($query);
$stmt->bindValue(':userid', $userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

// if user has places
if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
	$start = $pager->getStartRow();

	$query = "SELECT p.place_id, p.place_name, p.description, p.submission_date, p.status,
				c.city_id, c.city_name, c.slug AS city_slug,
				ph.filename, ph.dir
				FROM places p
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN photos ph ON p.place_id = ph.place_id
				WHERE p.userid = :userid AND p.status != 'trashed'
				GROUP BY p.place_id
				ORDER BY p.place_id DESC
				LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':userid', $userid);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();

	$places_arr = array();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$place_id        = (!empty($row['place_id']))        ? $row['place_id']        : '';
		$place_name      = (!empty($row['place_name']))      ? $row['place_name']      : '';
		$description     = (!empty($row['description']))     ? $row['description']     : '';
		$submission_date = (!empty($row['submission_date'])) ? $row['submission_date'] : '';
		$status          = (!empty($row['status']))          ? $row['status']          : '';
		$city_id         = (!empty($row['city_id']))         ? $row['city_id']         : null;
		$city_name       = (!empty($row['city_name']))       ? $row['city_name']       : '';
		$city_slug       = (!empty($row['city_slug']))       ? $row['city_slug']       : '';
		$state_id        = (!empty($row['state_id']))        ? $row['state_id']        : null;
		$state           = (!empty($row['state']))           ? $row['state']           : '';
		$dir             = (!empty($row['dir']))             ? $row['dir']             : '';
		$filename        = (!empty($row['filename']))        ? $row['filename']        : '';

		// photo_url
		$photo_url = '';
		if(!empty($filename)) {
			$photo_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;
		}
		else {
			$photo_url = $baseurl . '/imgs/blank.png';
		}

		// place_slug
		$place_slug = to_slug($place_name);

		// description
		if(!empty($description)) {
			$description = mb_substr($description, 0, 75) . '...';
		}

		// sanitize
		$place_name      = e($place_name);
		$description     = e($description);

		// status
		if($status == 'approved') {
			$status = $txt_status_approved;
		}
		else {
			$status = $txt_status_pending;
		}

		$cur_loop_arr = array(
			'place_id'        => $place_id,
			'place_name'      => $place_name,
			'place_slug'      => $place_slug,
			'description'     => $description,
			'submission_date' => $submission_date,
			'status'          => $status,
			'city_id'         => $city_id,
			'city_name'       => $city_name,
			'city_slug'       => $city_slug,
			'state_id'        => $state_id,
			'state'           => $state,
			'photo_url'       => $photo_url
		);

		// add cur loop to places array
		$places_arr[] = $cur_loop_arr;
	} // end while
}

else {

}

// translation substitutions
$txt_html_title = str_replace('%site_name%', $site_name, $txt_html_title);

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_my-places.php');