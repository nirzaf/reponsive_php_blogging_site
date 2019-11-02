<?php
require_once(__DIR__ . '/inc/config.php');

// list/loc-slug/cat-slug/[s|a|c]-[loc_id]-[cat_id]-1

/*--------------------------------------------------
set vars from path info
--------------------------------------------------*/
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

// $loc_slug = $frags[1]; // get slugs from ids, not from url
// $cat_slug = $frags[2];
$get_vars = $frags[3];
$get_vars = explode("-", $get_vars);
$loc_type = $get_vars[0];
$loc_id   = !empty($get_vars[1]) ? $get_vars[1] : 0;
$cat_id   = !empty($get_vars[2]) ? $get_vars[2] : 0;
$page     = !empty($get_vars[3]) ? $get_vars[3] : 1;

/*--------------------------------------------------
validate path info vars
--------------------------------------------------*/
if(!is_numeric($loc_id)
	|| !is_numeric($cat_id)
	|| !is_numeric($page)
	|| ($loc_type != 'n' && $loc_id == 0)
	|| ($loc_type == 'n' && $cat_id == 0)) {
	header("HTTP/1.0 404 Not Found");
	throw new Exception('404 Not Found. Invalid path info.');
}

// valid loc types are sort of validated in .htaccess but do it here as a guarantee
$valid_loc_types = array('n', 's', 'a', 'c');

if(!in_array($loc_type, $valid_loc_types)) {
	header("HTTP/1.0 404 Not Found");
	throw new Exception("404 Not Found. Invalid loc type", E_USER_ERROR);
}

/*--------------------------------------------------
get category info
--------------------------------------------------*/
if($cat_id != 0) {
	$query = "SELECT * FROM cats WHERE id = :cat_id";
	$stmt  = $conn->prepare($query);
	$stmt->bindValue(':cat_id', $cat_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$cat_id          = $row['id'];
	$cat_name        = $row['name'];
	$cat_slug        = to_slug($cat_name);
	$plural_name     = (!empty($row['plural_name'])) ? $row['plural_name'] : $cat_name ;
	$plural_cat_slug = to_slug($plural_name);
	$parent_id       = $row['parent_id'];
	$iconfont_tag    = $row['iconfont_tag'];
}
else {
	$cat_slug = 'all-categories';
}

if($cat_id != 0) {
	$cats_path = get_parent($cat_id, array(), $conn);
	$cats_path = array_reverse($cats_path);
}
else {
	$cats_path = array();
}

/*--------------------------------------------------
get location info
--------------------------------------------------*/
//
$loc_slug = '';

// get loc info
if($loc_id != 0) {
	if($loc_type == 's') {
		$query = "SELECT * FROM states WHERE state_id = :loc_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':loc_id', $loc_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$state_name = $row['state_name'];
		$state_abbr = $row['state_abbr'];
		$state_slug = $row['slug'];
		$state_id   = $loc_id;
		$region     = $state_name;
		$loc_slug   = $state_slug;
	}
	if($loc_type == 'c') {
		$query = "SELECT
			cities.city_name, cities.slug AS city_slug, cities.lat, cities.lng,
			states.state_id, states.state_name, states.slug AS state_slug, states.state_abbr
			FROM cities
			LEFT JOIN states ON cities.state_id = states.state_id
			WHERE cities.city_id = :city_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':city_id', $loc_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$city_id    = $loc_id;
		$city_name  = $row['city_name'];
		$city_slug  = $row['city_slug'];
		$city_lat   = $row['lat'];
		$city_lng   = $row['lng'];
		$state_name = $row['state_name'];
		$state_abbr = $row['state_abbr'];
		$state_id   = $row['state_id'];
		$state_slug = $row['state_slug'];
		$region     = $city_name . ', ' . $state_abbr;
		$loc_slug   = $city_slug;
	}
	if($loc_type == 'a') {
		$query = "SELECT
			n.neighborhood_slug, n.neighborhood_name,
			c.city_id, c.city_name, c.state_id, c.state, c.slug,
			s.state_name, s.slug AS state_slug
		FROM neighborhoods n
		LEFT JOIN cities c ON n.city_id = c.city_id
		LEFT JOIN states s ON s.state_id = c.state_id
		WHERE n.neighborhood_id = :neighborhood_id";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood_id', $loc_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$neighborhood_id   = $loc_id;
		$neighborhood_name = $row['neighborhood_name'];
		$neighborhood_slug = $row['neighborhood_slug'];
		$city_id           = $row['city_id'];
		$city_name         = $row['city_name'];
		$city_slug         = $row['slug'];
		$state_id          = $row['state_id'];
		$state_name        = $row['state_name'];
		$state_abbr        = $row['state'];
		$state_slug        = $row['state_slug'];
		$region            = $neighborhood_name . ', ' . $city_name;
		$loc_slug          = $neighborhood_slug;
	}
}
else {
	$loc_name = $country_name;
	$loc_slug = $default_country_code;
}

/*--------------------------------------------------
Initialize combined response
--------------------------------------------------*/
$list_items = array();

/*--------------------------------------------------
Query database
--------------------------------------------------*/
// get all children for current cat
if($cat_id != 0) {
	$in = array();
	$in[] = $cat_id;

	$children = get_children_cats_ids($cat_id, $conn);

	if(!empty($children)) {
		foreach($children as $v) {
			$in[] = $v;
		}
	}

	$in_str = '';

	foreach($in as $k => $v) {
		if($k == 0) {
			$in_str .= $v;
		}
		else {
			$in_str .= ",$v";
		}
	}
}

// define queries
$total_rows = 0;
$start      = 0;
if($loc_type == 'n') {
	// if all cats and no specific location
	if($cat_id == 0) {
		// doesn't happen?
	} // end if all cats and no specific location

	// specific cat and no location
	else {
		$query = "SELECT COUNT(*) AS total_rows FROM places p
			INNER JOIN rel_place_cat r ON p.place_id = r.place_id
			WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
						p.place_id, p.place_name, p.address, p.cross_street,
						p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
						c.city_name, c.slug, c.state,
						cats.iconfont_tag,
						ph.dir, ph.filename,
						rev_table.text, rev_table.avg_rating
					FROM places p
					LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
					LEFT JOIN cities c ON c.city_id = p.city_id
					LEFT JOIN cats ON r.cat_id = cats.id
					LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
					LEFT JOIN (
						SELECT *,
							AVG(rev.rating) AS avg_rating
							FROM reviews rev
							GROUP BY place_id
						) rev_table ON p.place_id = rev_table.place_id
					WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
					GROUP BY p.place_id
					ORDER BY p.feat DESC, p.submission_date DESC
					LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
}

// if list by state
if($loc_type == 's') {
	// if all cats and by state
	if($cat_id == 0) {
		$query = "SELECT COUNT(*) AS total_rows FROM places
					WHERE state_id = :state_id AND status = 'approved' AND paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':state_id', $state_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) { // we only know the state, so we have to query cities table
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
						p.place_id, p.place_name, p.address, p.cross_street,
						p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
						c.city_name, c.slug, c.state,
						cats.iconfont_tag,
						ph.dir, ph.filename,
						rev_table.text, rev_table.avg_rating
					FROM places p
					LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
					LEFT JOIN cities c ON p.city_id = c.city_id
					LEFT JOIN cats ON r.cat_id = cats.id
					LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
					LEFT JOIN (
						SELECT *,
							AVG(rev.rating) AS avg_rating
							FROM reviews rev
							GROUP BY place_id
						) rev_table ON p.place_id = rev_table.place_id
					WHERE p.state_id = :state_id AND p.status = 'approved' AND p.paid = 1
					GROUP BY p.place_id
					ORDER BY p.feat DESC, p.submission_date DESC
					LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':state_id', $state_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}

	// if specific cat and by state
	else {
		$query = "SELECT COUNT(*) AS total_rows
			FROM places p
			LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
			WHERE r.cat_id IN ($in_str)
			AND p.state_id = :state_id
			AND p.status = 'approved' AND p.paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':state_id', $state_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
					c.city_name, c.slug, c.state,
					cats.iconfont_tag,
					ph.dir, ph.filename,
					rev_table.text, rev_table.avg_rating
				FROM places p
				LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN cats ON r.cat_id = cats.id
				LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE r.cat_id IN ($in_str) AND p.state_id = :state_id AND p.status = 'approved' AND p.paid = 1
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':state_id', $state_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
}

// if list by city
if($loc_type == 'c') {
	// if all cats and by city
	if($cat_id == 0) {
		$query = "SELECT COUNT(*) AS total_rows FROM places
					WHERE city_id = :city_id AND status = 'approved' AND paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':city_id', $city_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
					c.city_name, c.slug, c.state,
					cats.iconfont_tag,
					ph.dir, ph.filename,
					rev_table.text, rev_table.avg_rating
				FROM places p
				LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN cats ON r.cat_id = cats.id
				LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE p.city_id = :city_id AND p.status = 'approved' AND p.paid = 1
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':city_id', $city_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
	// end if all cats and by city

	// if specific category and by city
	else {
		$query = "SELECT COUNT(*) AS total_rows FROM places p INNER JOIN rel_place_cat r ON p.place_id = r.place_id
			WHERE r.cat_id IN ($in_str)
			AND p.city_id = :city_id
			AND p.status = 'approved' AND p.paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':city_id', $city_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
					c.city_name, c.slug, c.state,
					cats.iconfont_tag,
					ph.dir, ph.filename,
					rev_table.text, rev_table.avg_rating
				FROM places p
				LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN cats ON r.cat_id = cats.id
				LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
				AND p.city_id = :city_id
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':city_id', $city_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
	// end if specific category and by city
} // end if list by city

// if list by neighborhood
if($loc_type == 'a') {
	// if all cats and by neighborhood
	if($cat_id == 0) {
		$query = "SELECT COUNT(*) AS total_rows FROM places
					WHERE neighborhood = :neighborhood AND status = 'approved' AND paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood', $loc_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
					c.city_name, c.slug, c.state,
					cats.iconfont_tag,
					ph.dir, ph.filename,
					rev_table.text, rev_table.avg_rating
				FROM places p
				LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN cats ON r.cat_id = cats.id
				LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE p.neighborhood = :neighborhood AND p.status = 'approved' AND p.paid = 1
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':neighborhood', $neighborhood_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
	// end if all cats and by neighborhood

	// if specific category and by neighborhood
	else {
		$query = "SELECT COUNT(*) AS total_rows FROM places p INNER JOIN rel_place_cat r ON p.place_id = r.place_id
			WHERE r.cat_id IN ($in_str)
			AND p.neighborhood = :neighborhood
			AND p.status = 'approved' AND p.paid = 1";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':neighborhood', $loc_id);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$total_rows = $row['total_rows'];

		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.feat,
					c.city_name, c.slug, c.state,
					cats.iconfont_tag,
					ph.dir, ph.filename,
					rev_table.text, rev_table.avg_rating
				FROM places p
				LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN cats ON r.cat_id = cats.id
				LEFT JOIN (SELECT * FROM photos) ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
				AND p.neighborhood = :neighborhood
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':neighborhood', $neighborhood_id);
			$stmt->bindValue(':start', $start);
			$stmt->bindValue(':items_per_page', $items_per_page);
		}
	}
	// end if specific category and by neighborhood
} // end if list by neighborhood

// execute query
$stmt->execute();

// insert into $list_items array
$count = 0;
if($total_rows > 0) {
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$place_id         = $row['place_id'];
		$place_name       = $row['place_name'];
		$address          = $row['address'];
		$cross_street     = $row['cross_street'];
		$place_city_name  = $row['city_name'];
		$place_city_slug  = (!is_null($row['slug'])) ? $row['slug'] : 'location';
		$place_state_id   = $row['state_id'];
		$place_state_abbr = $row['state'];
		$postal_code      = $row['postal_code'];
		$area_code        = $row['area_code'];
		$phone            = $row['phone'];
		$lat              = $row['lat'];
		$lng              = $row['lng'];
		$iconfont_tag     = (!empty($row['iconfont_tag'])) ? $row['iconfont_tag'] : '';
		$rating           = $row['avg_rating'];
		$is_feat          = $row['feat'];

		// thumb
		if(!empty($row['filename'])) {
			$photo_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $row['dir'] . '/' . $row['filename'];
		}
		else {
			$photo_url = $baseurl . '/imgs/blank.png';
		}

		// get one tip
		$tip_text = $row['text'];
		if(!empty($tip_text)) {
			$tip_text = get_snippet($tip_text) . '...';
		}

		// clean place name
		$endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');
		$place_name = str_replace($endash, "-", $place_name);

		$list_items[] = array(
			'place_id'     => $place_id,
			'place_name'   => e($place_name),
			'address'      => e($address),
			'cross_street' => e($cross_street),
			'city_name'    => $place_city_name,
			'city_slug'    => $place_city_slug,
			'state_abbr'   => $place_state_abbr,
			'postal_code'  => e($postal_code),
			'area_code'    => e($area_code),
			'phone'        => e($phone),
			'lat'          => $lat,
			'lng'          => $lng,
			'iconfont_tag' => $iconfont_tag,
			'photo_url'    => $photo_url,
			'tip_text'     => e($tip_text),
			'rating'       => $rating,
			'is_feat'      => $is_feat
		);
	}
}

$stmt->closeCursor();

/*--------------------------------------------------
Results array to be used by map markers
--------------------------------------------------*/
$count = ($page - 1) * $items_per_page;
$results_arr = array();
foreach($list_items as $k => $v) {
	if(!empty($v['lat'])) {
		$count++;
		$results_arr[] = array(
			"ad_id"    => $v['place_id'],
			"ad_lat"   => $v['lat'],
			"ad_lng"   => $v['lng'],
			"ad_title" => $v['place_name'],
			"count"    => $count
		);
		$places_names_arr[] = $v['place_name'];
	}
}

/*--------------------------------------------------
html title and meta descriptions
--------------------------------------------------*/
$total_items = count($list_items);

// get first 2 or 3 place names and build string to use in meta descriptions
if(!empty($list_items)) {
	$meta_desc_str = '';

	for($i = 0; $i < 3; $i++) {
		if(!empty($list_items[$i]['place_name'])) {
			if($i != 0) {
				$meta_desc_str .= ', ';
			}
			$meta_desc_str .= $list_items[$i]['place_name'];
		}
	}
}
else {
	$meta_desc_str = '';
}

if($loc_type == 'n') {
	// case: category = defined, location = country (e.g. mexican restaurant in United States)
	$txt_html_title_1 = str_replace('%plural_name%'    , $plural_name , $txt_html_title_1);
	$txt_html_title_1 = str_replace('%default_country%', $country_name, $txt_html_title_1);
	$txt_html_title_1 = str_replace('%page%'           , $page        , $txt_html_title_1);

	$txt_meta_desc_1  = str_replace('%plural_name%'  , $plural_name  , $txt_meta_desc_1);
	$txt_meta_desc_1  = str_replace('%limit%'        , $total_items  , $txt_meta_desc_1);
	$txt_meta_desc_1  = str_replace('%page%'         , $page         , $txt_meta_desc_1);
	$txt_meta_desc_1  = str_replace('%meta_desc_str%', $meta_desc_str, $txt_meta_desc_1);

	$txt_html_title   = $txt_html_title_1;
	$txt_meta_desc    = $txt_meta_desc_1;
}

if($loc_type == 's') {
	if($cat_id != 0) {
		// case: category = defined, state = defined (e.g. mexican restaurant in California)
		$txt_html_title_2 = str_replace('%plural_name%'  , $plural_name  , $txt_html_title_2);
		$txt_html_title_2 = str_replace('%state_name%'   , $state_name   , $txt_html_title_2);
		$txt_html_title_2 = str_replace('%page%'         , $page         , $txt_html_title_2);

		$txt_meta_desc_2  = str_replace('%plural_name%'  , $plural_name  , $txt_meta_desc_2);
		$txt_meta_desc_2  = str_replace('%state_name%'   , $state_name   , $txt_meta_desc_2);
		$txt_meta_desc_2  = str_replace('%limit%'        , $total_items  , $txt_meta_desc_2);
		$txt_meta_desc_2  = str_replace('%page%'         , $page         , $txt_meta_desc_2);
		$txt_meta_desc_2  = str_replace('%meta_desc_str%', $meta_desc_str, $txt_meta_desc_2);

		$txt_html_title   = $txt_html_title_2;
		$txt_meta_desc    = $txt_meta_desc_2;
	}
	else {
		// case: category = undefined, state = defined (e.g. all types of venues in California)
		$txt_html_title_3 = str_replace('%state_name%'   , $state_name   , $txt_html_title_3);
		$txt_html_title_3 = str_replace('%page%'         , $page         , $txt_html_title_3);

		$txt_meta_desc_3  = str_replace('%state_name%'   , $state_name   , $txt_meta_desc_3);
		$txt_meta_desc_3  = str_replace('%limit%'        , $total_items  , $txt_meta_desc_3);
		$txt_meta_desc_3  = str_replace('%page%'         , $page         , $txt_meta_desc_3);
		$txt_meta_desc_3  = str_replace('%meta_desc_str%', $meta_desc_str, $txt_meta_desc_3);
		$txt_html_title   = $txt_html_title_3;
		$txt_meta_desc    = $txt_meta_desc_3;
	}
}

if($loc_type == 'c') {
	if($cat_id != 0) {
		// case: category = defined, city = defined (e.g. mexican restaurant in Los Angeles)
		$txt_html_title_4 = str_replace('%plural_name%'  , $plural_name  , $txt_html_title_4);
		$txt_html_title_4 = str_replace('%city_name%'    , $city_name    , $txt_html_title_4);
		$txt_html_title_4 = str_replace('%state_abbr%'   , $state_abbr   , $txt_html_title_4);
		$txt_html_title_4 = str_replace('%page%'         , $page         , $txt_html_title_4);

		$txt_meta_desc_4  = str_replace('%plural_name%'  , $plural_name  , $txt_meta_desc_4);
		$txt_meta_desc_4  = str_replace('%city_name%'    , $city_name    , $txt_meta_desc_4);
		$txt_meta_desc_4  = str_replace('%limit%'        , $total_items  , $txt_meta_desc_4);
		$txt_meta_desc_4  = str_replace('%page%'         , $page         , $txt_meta_desc_4);
		$txt_meta_desc_4  = str_replace('%meta_desc_str%', $meta_desc_str, $txt_meta_desc_4);
		$txt_html_title   = $txt_html_title_4;
		$txt_meta_desc    = $txt_meta_desc_4;
	}
	else {
		// case: category = undefined, city = defined (e.g. all types of venues in Los Angeles)
		$txt_html_title_5 = str_replace('%city_name%'    , $city_name    , $txt_html_title_5);
		$txt_html_title_5 = str_replace('%page%'         , $page         , $txt_html_title_5);

		$txt_meta_desc_5  = str_replace('%city_name%'    , $city_name    , $txt_meta_desc_5);
		$txt_meta_desc_5  = str_replace('%limit%'        , $total_items  , $txt_meta_desc_5);
		$txt_meta_desc_5  = str_replace('%page%'         , $page         , $txt_meta_desc_5);
		$txt_meta_desc_5  = str_replace('%meta_desc_str%', $meta_desc_str, $txt_meta_desc_5);
		$txt_html_title   = $txt_html_title_5;
		$txt_meta_desc    = $txt_meta_desc_5;
	}
}

if($loc_type == 'a') {
	if($cat_id != 0) {
		// case: category = defined, neighborhood = defined (e.g. mexican restaurant in Downtown)
		$txt_html_title_6 = str_replace('%plural_name%'      , $plural_name      , $txt_html_title_6);
		$txt_html_title_6 = str_replace('%city_name%'        , $city_name        , $txt_html_title_6);
		$txt_html_title_6 = str_replace('%neighborhood_name%', $neighborhood_name, $txt_html_title_6);
		$txt_html_title_6 = str_replace('%state_abbr%'       , $state_abbr       , $txt_html_title_6);
		$txt_html_title_6 = str_replace('%page%'             , $page             , $txt_html_title_6);

		$txt_meta_desc_6  = str_replace('%plural_name%'      , $plural_name      , $txt_meta_desc_6);
		$txt_meta_desc_6  = str_replace('%neighborhood_name%', $neighborhood_name, $txt_meta_desc_6);
		$txt_meta_desc_6  = str_replace('%city_name%'        , $city_name        , $txt_meta_desc_6);
		$txt_meta_desc_6  = str_replace('%limit%'            , $total_items      , $txt_meta_desc_6);
		$txt_meta_desc_6  = str_replace('%page%'             , $page             , $txt_meta_desc_6);
		$txt_meta_desc_6  = str_replace('%meta_desc_str%'    , $meta_desc_str    , $txt_meta_desc_6);
		$txt_html_title   = $txt_html_title_6;
		$txt_meta_desc    = $txt_meta_desc_6;
	}
	else {
		// case: category = undefined, city = defined (e.g. all types of venues in Downtown, Los Angeles)
		$txt_html_title_7 = str_replace('%neighborhood_name%', $neighborhood_name, $txt_html_title_7);
		$txt_html_title_7 = str_replace('%city_name%'        , $city_name        , $txt_html_title_7);
		$txt_html_title_7 = str_replace('%page%'             , $page             , $txt_html_title_7);

		$txt_meta_desc_7  = str_replace('%neighborhood_name%', $neighborhood_name, $txt_meta_desc_7);
		$txt_meta_desc_7  = str_replace('%city_name%'        , $city_name        , $txt_meta_desc_7);
		$txt_meta_desc_7  = str_replace('%limit%'            , $total_items      , $txt_meta_desc_7);
		$txt_meta_desc_7  = str_replace('%page%'             , $page             , $txt_meta_desc_7);
		$txt_meta_desc_7  = str_replace('%meta_desc_str%'    , $meta_desc_str    , $txt_meta_desc_7);
		$txt_html_title   = $txt_html_title_7;
		$txt_meta_desc    = $txt_meta_desc_7;
	}
}

/*--------------------------------------------------
breadcrumbs
--------------------------------------------------*/
$breadcrumbs = '';
if($loc_type == 'n') {
	foreach($cats_path as $v) { // $cats_path is an array of category ids
		$stmt = $conn->prepare('SELECT name, plural_name, id FROM cats WHERE id = :id');
		$stmt->bindValue(':id', $v);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this_cat_id   = $row['id'];
		$this_cat_name = (!empty($row['plural_name'])) ? $row['plural_name'] : $row['name'];
		$this_cat_slug = to_slug($this_cat_name);

		$breadcrumbs .= "<a href=\"$baseurl/$default_country_code/list/$this_cat_slug/n-0-$this_cat_id-1\">$this_cat_name</a> > ";
	}
	$breadcrumbs .= $plural_name;
} // end breadcrumbs $loc_type == 'n'

if($loc_type == 's') {
	if($cat_id == 0) { // state without cat
		$breadcrumbs .= $state_name;
	}
	else { // state and cat
		$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/all-categories/s-$loc_id-0-1\">$state_name</a> > ";

		foreach($cats_path as $v) { // $cats_path is an array of category ids
			$stmt = $conn->prepare('SELECT name, plural_name, id FROM cats WHERE id = :id');
			$stmt->bindValue(':id', $v);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this_cat_id = $row['id'];
			$this_cat_name = (!empty($row['plural_name'])) ? $row['plural_name'] : $row['name'];
			$this_cat_slug = to_slug($this_cat_name);

			$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/$this_cat_slug/s-$loc_id-$this_cat_id-1\">$this_cat_name</a> > ";
		}
		$breadcrumbs .= $plural_name;
	}
} // end breadcrumbs $loc_type == 's'

if($loc_type == 'c') {
	if($cat_id == 0) {
		if(!empty($state_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/all-categories/s-$state_id-0-1\">$state_name</a> > $city_name ";
		}
		else {
			$breadcrumbs .= " $city_name ";
		}
	}
	else {
		if(!empty($state_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/all-categories/s-$state_id-0-1\">$state_name</a> > ";
		}
		$breadcrumbs .= "<a href=\"$baseurl/$city_slug/list/all-categories/c-$city_id-0-1\">$city_name</a> > ";

		foreach($cats_path as $v) { // $cats_path is an array of category ids
			$stmt = $conn->prepare('SELECT name, plural_name, id FROM cats WHERE id = :id');
			$stmt->bindValue(':id', $v);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this_cat_id   = $row['id'];
			$this_cat_name = (!empty($row['plural_name'])) ? $row['plural_name'] : $row['name'];
			$this_cat_slug = to_slug($this_cat_name);

			$breadcrumbs .= "<a href=\"$baseurl/$city_slug/list/$this_cat_slug/c-$city_id-$this_cat_id-1\">$this_cat_name</a> > ";
		}
		$breadcrumbs .= $plural_name;
	}
} // end breadcrumbs $loc_type == 'c'

if($loc_type == 'a') {
	if($cat_id == 0) {
		if(!empty($state_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/all-categories/s-$state_id-0-1\">$state_name</a> > ";
		}
		if(!empty($city_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$city_slug/list/all-categories/c-$city_id-0-1\">$city_name</a> > ";
		}
		$breadcrumbs .= $neighborhood_name;
	}
	else {
		if(!empty($state_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$state_slug/list/all-categories/s-$state_id-0-1\">$state_name</a> > ";
		}
		if(!empty($city_id)) {
			$breadcrumbs .= "<a href=\"$baseurl/$city_slug/list/all-categories/c-$city_id-0-1\">$city_name</a> > ";
		}
		$breadcrumbs .= "<a href=\"$baseurl/$neighborhood_slug/list/all-categories/a-$neighborhood_id-0-1\">$neighborhood_name</a> > ";

		foreach($cats_path as $v) { // $cats_path is an array of category ids
			$stmt = $conn->prepare('SELECT name, plural_name, id FROM cats WHERE id = :id');
			$stmt->bindValue(':id', $v);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$this_cat_id   = $row['id'];
			$this_cat_name = (!empty($row['plural_name'])) ? $row['plural_name'] : $row['name'];
			$this_cat_slug = to_slug($this_cat_name);

			$breadcrumbs .= "<a href=\"$baseurl/$neighborhood_slug/list/$this_cat_slug/a-$neighborhood_id-$this_cat_id-1\">$this_cat_name</a> > ";
		}
		$breadcrumbs .= $plural_name;
	}
} // end breadcrumbs $loc_type == 'c'

/*--------------------------------------------------
sub categories
--------------------------------------------------*/
$subcats = array();
$query = "SELECT * FROM cats WHERE parent_id = :cat_id AND cat_status = 1 ORDER BY name";
$stmt = $conn->prepare($query);
$stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$sub_cat_id   = $row['id'];
	$sub_cat_name = (!empty($row['plural_name'])) ? $row['plural_name'] : $row['name'];
	$subcats[] = array('cat_id' => $sub_cat_id, 'cat_name' => $sub_cat_name);
}

/*--------------------------------------------------
page navigation
--------------------------------------------------*/
$pager_template = '';
if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
}

if(!empty($pager) && $pager->getTotalPages() > 1) {
	$curPage = $page;

	$startPage = ($curPage < 5) ? 1 : $curPage - 4;
	$endPage = 8 + $startPage;
	$endPage = ($pager->getTotalPages() < $endPage) ? $pager->getTotalPages() : $endPage;
	$diff = $startPage - $endPage + 8;
	$startPage -= ($startPage - $diff > 0) ? $diff : 0;

	$startPage = ($startPage == 1) ? 2 : $startPage;
	$endPage = ($endPage == $pager->getTotalPages()) ? $endPage - 1 : $endPage;

	if($total_rows > 0) {
		$page_url = "$baseurl/$loc_slug/list/$cat_slug/$loc_type-$loc_id-$cat_id-";

		if ($curPage > 1) {
			$pager_template .= "<li><a href=\"$page_url" . "1\">$txt_pager_page1</a></li>";
		}
		if ($curPage > 6) {
			$pager_template .= "<li><span>...</span></li>";
		}
		if ($curPage == 1) {
			$pager_template .= "<li class=\"active\"><span>$txt_pager_page1</span></li>";
		}
		for($i = $startPage; $i <= $endPage; $i++) {
			if($i == $page) {
				$pager_template .= "<li class=\"active\"><span>$i</span></li>";
			}
			else {
				$pager_template .= "<li><a href=\"$page_url" . "$i\">$i</a></li>";
			}
		}

		if($curPage + 5 < $pager->getTotalPages()) {
			$pager_template .= "<li><span>...</span></li>";
		}
		if($pager->getTotalPages() > 5) {
			$last_page_txt = $txt_pager_lastpage;
		}

		$last_page_txt = ($pager->getTotalPages() > 5) ? $txt_pager_lastpage : $pager->getTotalPages();

		if($curPage == $pager->getTotalPages()) {
			$pager_template .= "<li class=\"active\"><span>$last_page_txt</span></li>";
		}
		else {
			$pager_template .= "<li><a href=\"$page_url" . $pager->getTotalPages() . "\">$last_page_txt</a></li>";
		}
	} //  end if($total_rows > 0)
} //  end if(!empty($pager) && $pager->getTotalPages() > 1)
if(!empty($pager) && $pager->getTotalPages() == 1) {
	// do something
}

if($page == 1) {
	$pag = '';
}
else {
	$pag = "- $txt_page $page";
}

/*--------------------------------------------------
results list counter
--------------------------------------------------*/
$count = ($page - 1) * $items_per_page;

/*--------------------------------------------------
canonical url
--------------------------------------------------*/
if($cat_id != 0) {
	$canonical = "$baseurl/$loc_slug/list/$plural_cat_slug/$loc_type-$loc_id-$cat_id-$page";
}
else {
	$canonical = "$baseurl/$loc_slug/list/all-categories/$loc_type-$loc_id-$cat_id-$page";
}

/*--------------------------------------------------
include template file
--------------------------------------------------*/
require_once(__DIR__ . '/templates/tpl_list.php');