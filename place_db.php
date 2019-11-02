<?php
$place_404 = 1;

// query db
$stmt = $conn->prepare("SELECT * FROM places WHERE place_id = :place_id");
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();

if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$place_404 = 0;

	$place_id        = (!empty($row['place_id']))        ? $row['place_id']        : '';
	$place_userid    = (!empty($row['userid']))          ? $row['userid']          : '';
	$lat             = (!empty($row['lat']))             ? $row['lat']             : '';
	$lng             = (!empty($row['lng']))             ? $row['lng']             : '';
	$place_name      = (!empty($row['place_name']))      ? $row['place_name']      : '';
	$address         = (!empty($row['address']))         ? $row['address']         : '';
	$postal_code     = (!empty($row['postal_code']))     ? $row['postal_code']     : '';
	$cross_street    = (!empty($row['cross_street']))    ? $row['cross_street']    : '';
	$neighborhood    = (!empty($row['neighborhood']))    ? $row['neighborhood']    : 0;
	$city_id         = (!empty($row['city_id']))         ? $row['city_id']         : 0;
	$state_id        = (!empty($row['state_id']))        ? $row['state_id']        : 0;
	$inside          = (!empty($row['inside']))          ? $row['inside']          : '';
	$area_code       = (!empty($row['area_code']))       ? $row['area_code']       : '';
	$phone           = (!empty($row['phone']))           ? $row['phone']           : '';
	$twitter         = (!empty($row['twitter']))         ? $row['twitter']         : '';
	$facebook        = (!empty($row['facebook']))        ? $row['facebook']        : '';
	$foursq_id       = (!empty($row['foursq_id']))       ? $row['foursq_id']       : '';
	$website         = (!empty($row['website']))         ? $row['website']         : '';
	$description     = (!empty($row['description']))     ? $row['description']     : '';
	$hours_info      = (!empty($row['business_hours_info'])) ? $row['business_hours_info'] : '';
	$submission_date = (!empty($row['submission_date'])) ? $row['submission_date'] : '';
	$status          = (!empty($row['status']))          ? $row['status']          : '';
	$paid            = (!empty($row['paid']))            ? $row['paid']            : 0;

	if(!$is_admin) {
		if($status != 'approved' && !$paid) {
			header("HTTP/1.0 404 Not Found");
			die("This place is pending moderation. Please check back again soon.");
		}
	}

	//get cat details
	$query = "SELECT * FROM rel_place_cat
		INNER JOIN cats ON rel_place_cat.cat_id = cats.id
		WHERE rel_place_cat.place_id = :place_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	$cat_id     = $row['cat_id'];
	$cat_name   = $row['name'];
	$cat_slug   = to_slug($row['name']);
	$cat_plural = $row['plural_name'];

	$cats_path   = get_parent($cat_id, array(), $conn);
	$cats_path   = array_reverse($cats_path);
	$cats_path[] = $cat_id;

	/*--------------------------------------------------
	Place info
	--------------------------------------------------*/
	$place_slug = to_slug($place_name);

	// city and state details
	$query = "SELECT
				c.city_name, c.slug AS city_slug,
				s.state_id, s.state_name, s.state_abbr, s.slug AS state_slug
				FROM cities c
				INNER JOIN states s ON c.state_id = s.state_id
				WHERE city_id = :city_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $city_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$city_name  = $row['city_name'];
	$city_slug  = $row['city_slug'];
	$state_id   = $row['state_id'];
	$state_name = $row['state_name'];
	$state_abbr = $row['state_abbr'];
	$state_slug = $row['state_slug'];

	// neighborhood
	$neighborhood_slug = '';
	$neighborhood_name = '';

	if(!empty($neighborhood)) {
		$query = "SELECT * FROM neighborhoods WHERE neighborhood_id = :neighborhood_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood_id', $neighborhood);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$neighborhood_slug = $row['neighborhood_slug'];
		$neighborhood_name = $row['neighborhood_name'];
	}

	// related listings

	// listings in the same neighborhood
	$places_in_neighborhood = '';
	$neighborhood_link = '';

	if(!empty($neighborhood)) {
		$query = "SELECT COUNT(*) AS total_count FROM places WHERE neighborhood = :neighborhood_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood_id', $neighborhood);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$places_in_neighborhood = $row['total_count'];

		$neighborhood_link = "$baseurl/$city_slug-$neighborhood_slug/list/all-categories/a-$neighborhood-0-1";
	}

	// listings in the same neighborhood and category
	$cat_members_in_neighborhood = '';
	$cat_neighborhood_link = '';

	if(!empty($neighborhood)) {
		$query = "SELECT COUNT(*) AS total_count FROM places p
			INNER JOIN rel_place_cat r ON p.place_id = r.place_id
		WHERE neighborhood = :neighborhood_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood_id', $neighborhood);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$cat_members_in_neighborhood = $row['total_count'];

		$cat_neighborhood_link = "$baseurl/$city_slug-$neighborhood_slug/list/$cat_slug/a-$neighborhood-$cat_id-1";
	}

	// calculate rating
	$rating = '';
	$stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE place_id = :place_id");
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$rating = $row['avg_rating'];

	// get photos
	$photos = array();
	$stmt = $conn->prepare("SELECT * FROM photos WHERE place_id = :place_id");
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$img_url = $pic_baseurl . '/' . $place_full_folder . '/' . $row['dir'] . '/' . $row['filename'];
		$img_url_thumb = $pic_baseurl . '/' . $place_thumb_folder . '/' . $row['dir'] . '/' . $row['filename'];
		$data_title = $place_name . ' picture';
		$source = 'self';
		$photos[] = array('img_url' => $img_url, 'img_url_thumb' => $img_url_thumb, 'data_title' => $data_title, 'source' => 'self');
	}

	// blur photo
	$blur_photo = '';
	if(!empty($photos)) {
		$blur_photo = $photos[0]['img_url'];
	}

	// reviews (aka tips)
	$query = "SELECT
				UNIX_TIMESTAMP(pubdate) AS review_date, r.*, u.first_name, u.last_name
				FROM reviews r LEFT JOIN users u ON r.user_id = u.id
				WHERE r.place_id = :place_id AND r.status = 'approved'
				ORDER BY r.pubdate DESC LIMIT 100";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();

	$tips = array();

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$review_id              = $row['review_id'];
		$review_user_id         = $row['user_id'];
		$review_user_first_name = $row['first_name'];
		$review_user_last_name  = $row['last_name'];
		$review_rating          = $row['rating'];
		$review_text            = $row['text'];
		$review_pubdate         = $row['review_date'];

		// sanitize
		$review_user_first_name = e(trim($review_user_first_name));
		$review_user_last_name  = e(trim($review_user_last_name ));
		$review_text            = e(trim($review_text));

		// prepare vars
		$review_user_display_name = "$review_user_first_name $review_user_last_name";

		// review user profile pic
		$folder = floor($review_user_id / 1000) + 1;
		if(strlen($folder) < 1) {
			$folder = '999';
		}

		$review_user_profile_pic_path = $pic_basepath . '/' . $profile_thumb_folder . '/' . $folder . '/' . $review_user_id;
		$review_user_profile_pic_path = glob("$review_user_profile_pic_path.*");

		if(empty($review_user_display_name)) {
			$review_user_display_name = 'anonymous';
		}

		if(!empty($review_user_profile_pic_path)) {
			$review_user_profile_pic_path = explode('/', $review_user_profile_pic_path[0]);
			$review_user_profile_pic_filename = end($review_user_profile_pic_path);
			$review_user_profile_pic_url = "$pic_baseurl/$profile_thumb_folder/$folder/$review_user_profile_pic_filename";
		}
		else {
			$review_user_profile_pic_url = "$baseurl/imgs/blank.png";
		}

		$tips[] = array(
				'review_id'         => $review_id,
				'user_id'           => $review_user_id,
				'user_display_name' => $review_user_display_name,
				'profile_pic_url'   => $review_user_profile_pic_url,
				'rating'            => $review_rating,
				'text'              => $review_text,
				'pubdate'           => $review_pubdate,
				'profile_link'      => $baseurl . '/profile/' . $review_user_id
		);
	}

	// hours
	$query = "SELECT day, open, close FROM business_hours WHERE place_id = :place_id ORDER BY day, open";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();

	// store all query rows in an array (see __test-hours.php)
	/*
	Array(
		[0] => Array(
				[day] => 1
				[open] => 1100
				[close] => 1300)
		[1] => Array(
				[day] => 1
				[open] => 1800
				[close] => 2300)
		[2] => Array(
				[day] => 2
				[open] => 1100
				[close] => 1300)
		...
	*/
	$hours = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$hours[] = $row;
	}

	// now group by day and change from day int to day name
	/*
	Array(
		[mon] => Array(
				[0] => 1100-1300
				[1] => 1800-2300)
		[tue] => Array(
				[0] => 1100-1300
				[1] => 1800-2300)
		...
	*/
	$new_hours = array();
	foreach($hours as $k => $v) {
		$day = $v['day'];

		$open = mb_substr($v['open'], 0, 2) . ':' . mb_substr($v['open'], 2, 2);
		$close = mb_substr($v['close'], 0, 2) . ':' . mb_substr($v['close'], 2, 2);

		switch($day) {
			case 0:
				$day = $txt_week_mon;
				break;
			case 1:
				$day = $txt_week_tue;
				break;
			case 2:
				$day = $txt_week_wed;
				break;
			case 3:
				$day = $txt_week_thu;
				break;
			case 4:
				$day = $txt_week_fri;
				break;
			case 5:
				$day = $txt_week_sat;
				break;
			case 6:
				$day = $txt_week_sun;
				break;
		}
		$new_hours[$day][] = $open . ' - ' . $close;
	}

	// now group by hours
	/*
	Array(
		[1100-1300;1800-2300] => Array(
				[0] => mon
				[1] => tue
				[2] => wed
				[3] => thu
				[4] => fri)
		[1800-2300] => Array(
				[0] => sat))
	*/
	$newnew_hours = array();

	foreach($new_hours as $k => $v) { // $k is 'mon' and $v is array( [0] => 1100-1300, [1] => 1800-2300 )
		$new_key = '';
		foreach($v as $k2 => $v2) {
			$new_key .= ($k2 == 0) ? $v2 : ";$v2";
		}
		$newnew_hours[$new_key][] = $k;
	}

	// now invert array
	/*
	Array(
		[mon, tue, wed, thu, fri] => 1100-1300;1800-2300
		[sat] => 1800-2300
	)
	*/
	$newnewnew_hours = array();
	foreach($newnew_hours as $k => $v) {
		$new_index = '';
		foreach($v as $k2 => $v2) {
			$new_index .= ($k2 == 0) ? $v2 : ", $v2";
		}
		$newnewnew_hours[$new_index] = $k;
	}

	// now rearrange
	/*
	Array(
		[mon, tue, wed, thu, fri] => Array(
				[0] => 1100-1300
				[1] => 1800-2300)
		[sat] => Array(
				[0] => 1800-2300))
	*/
	$newnewnewnew_hours = array();
	foreach($newnewnew_hours as $k => $v) {
		$v = explode(';', $v);
		$newnewnewnew_hours[$k] = $v;
	}

	// now build tpl_hours array, the one that is used in the template
	/*
	Array(
		[0] => Array(
				[days] => mon, tue, wed, thu, fri
				[open] => Array(
						[0] => 1100-1300
						[1] => 1800-2300))
		[1] => Array(
				[days] => sat
				[open] => Array(
						[0] => 1800-2300)))
	*/
	$tpl_hours = array();
	foreach($newnewnewnew_hours as $k => $v) {
		$tpl_hours[] = array(
			'days' => $k,
			'open' => $v
		);
	}

	/*--------------------------------------------------
	Prepare vars
	--------------------------------------------------*/
	$place_name   = e($place_name  );
	$address      = e($address     );
	$postal_code  = e($postal_code );
	$cross_street = e($cross_street);
	$neighborhood = e($neighborhood);
	$inside       = e($inside      );
	$area_code    = e($area_code   );
	$phone        = e($phone       );
	$twitter      = e($twitter     );
	$facebook     = e($facebook    );
	$website      = e($website     );
	$description  = e($description );
	$hours_info   = e($hours_info  );

	// add line break
	$description = nl2br($description);

	if(!empty($phone)) {
		$phone = substr_replace($phone, '-', 3, 0);
	}
}