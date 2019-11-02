<?php
require_once(__DIR__ . '/inc/config.php');
?>
<?php
$total_rows = 0;
$response = array();

$query_city_id = (!empty($_GET['city_id'])) ? $_GET['city_id'] : 0;
$user_query    = (!empty($_GET['query']))   ? $_GET['query']   : '';
$page          = (!empty($_GET['page']))    ? $_GET['page']    : 1;

// append *
$query_query = explode(' ', trim($user_query));
$new_query = '';
foreach($query_query as $v) {
	$new_query .= "$v* ";
}
$query_query = $new_query;

// check vars
if(!is_numeric($query_city_id)) {
	die('Wrong city id');
}
$query_city_id = (int)$query_city_id;

// city details
$query_city_name  = '';
$query_state_abbr = '';
if(!empty($query_city_id)) {
	$query = "SELECT city_name, state FROM cities WHERE city_id = :query_city_id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':query_city_id', $query_city_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$query_city_name = $row['city_name'];
	$query_state_abbr = $row['state'];
}

// paging vars
$limit = $items_per_page;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// get page
if($page == 1) {
	$pag = '';
}
else {
	$pag = "- $txt_page $page";
}

// count total rows
if(!empty($query_city_id) && !empty($user_query)) {
	$query = "SELECT COUNT(*) AS total_rows
		FROM places
		WHERE city_id = :city_id AND status = 'approved' AND paid = 1
			AND MATCH(place_name, description) AGAINST(:query IN BOOLEAN MODE)";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $query_city_id);
	$stmt->bindValue(':query', $query_query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows = $row['total_rows'];
}
else if (empty($query_city_id) && !empty($user_query)) {
	$query = "SELECT COUNT(*) AS total_rows
		FROM places
		WHERE status = 'approved' AND paid = 1
		AND MATCH(place_name, description) AGAINST(:query IN BOOLEAN MODE)";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':query', $query_query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows = $row['total_rows'];
}
else if (!empty($query_city_id) && empty($user_query)) {
	$query = "SELECT COUNT(*) AS total_rows
		FROM places
		WHERE status = 'approved' AND paid = 1 AND city_id = :city_id";

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $query_city_id);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows = $row['total_rows'];
}
else {
	$total_rows = 0;
}

$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
$start = $pager->getStartRow();

// initialize empty city and query check
$empty_city_and_query = false;

/*--------------------------------------------------
LIST ITEMS LOGIC
--------------------------------------------------*/
if(!empty($query_city_id) && !empty($user_query)) {
	$query = "SELECT p.place_id, p.place_name, p.address, p.cross_street,
				p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.description,
				c.city_name, c.slug, c.state, ph.filename, ph.dir,
				rev_table.avg_rating
				FROM places p
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN photos ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE p.city_id = :city_id AND p.status = 'approved' AND paid = 1
					AND MATCH(place_name, description) AGAINST(:query IN BOOLEAN MODE)
				GROUP BY p.place_id
				ORDER BY p.submission_date DESC
				LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $query_city_id);
	$stmt->bindValue(':query', $query_query);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
}

else if(empty($query_city_id) && !empty($user_query)) {
	$query = "SELECT p.place_id, p.place_name, p.address, p.cross_street,
				p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.description,
				c.city_name, c.slug, c.state, ph.filename, ph.dir,
				rev_table.avg_rating
				FROM places p
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN photos ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE p.status = 'approved' AND paid = 1
					AND MATCH(place_name, description) AGAINST(:query IN BOOLEAN MODE)
				GROUP BY p.place_id
				ORDER BY p.submission_date DESC
				LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':query', $query_query);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
}

else if(!empty($query_city_id) && empty($user_query)) {
	$query = "SELECT p.place_id, p.place_name, p.address, p.cross_street,
				p.postal_code, p.phone, p.area_code, p.lat, p.lng, p.state_id, p.description,
				c.city_name, c.slug, c.state, ph.filename, ph.dir,
				rev_table.avg_rating
				FROM places p
				LEFT JOIN cities c ON p.city_id = c.city_id
				LEFT JOIN photos ph ON p.place_id = ph.place_id
				LEFT JOIN (
					SELECT *,
						AVG(rev.rating) AS avg_rating
						FROM reviews rev
						GROUP BY place_id
					) rev_table ON p.place_id = rev_table.place_id
				WHERE p.city_id = :city_id AND p.status = 'approved' AND paid = 1
				GROUP BY p.place_id
				ORDER BY p.submission_date DESC
				LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':city_id', $query_city_id);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
}

else{ // both $query_loc and $query_query empty
	$empty_city_and_query = true;
}

// now execute query
$stmt->execute();

// build results array
if($total_rows > 0) {
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$place_id         = $row['place_id'];
		$place_name       = $row['place_name'];
		$address          = $row['address'];
		$cross_street     = $row['cross_street'];
		$place_city_name  = $row['city_name'];
		$place_city_slug  = $row['slug'];
		$place_state_id   = $row['state_id'];
		$place_state_abbr = $row['state'];
		$postal_code      = $row['postal_code'];
		$area_code        = $row['area_code'];
		$phone            = $row['phone'];
		$lat              = $row['lat'];
		$lng              = $row['lng'];
		$rating           = $row['avg_rating'];
		$description      = $row['description'];

		// short description
		$description = get_snippet($description, 20);

		// cat icon (just use blank img for now)
		$cat_icon = $baseurl . '/imgs/blank.png';

		// thumb
		if(!empty($row['filename'])) {
			$photo_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $row['dir'] . '/' . $row['filename'];
		}
		else {
			$photo_url = $cat_icon;
		}

		// clean place name
		$endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');
		$place_name = str_replace($endash, "-", $place_name);

		$list_items[] = array(
			'place_id'         => $place_id,
			'place_name'       => e($place_name),
			'place_slug'       => to_slug($place_name),
			'address'          => e($address),
			'cross_street'     => e($cross_street),
			'place_city_name'  => $place_city_name,
			'place_city_slug'  => $place_city_slug,
			'place_state_abbr' => $place_state_abbr,
			'postal_code'      => e($postal_code),
			'area_code'        => e($area_code),
			'phone'            => e($phone),
			'lat'              => $lat,
			'lng'              => $lng,
			'cat_icon'         => $cat_icon,
			'photo_url'        => $photo_url,
			'rating'           => $rating,
			'description'      => $description
		);
	}
}

$stmt->closeCursor();

$location = '';
if(!empty($query_city_name) && !empty($query_state_abbr)) {
	$location = "$query_city_name, $query_state_abbr";
}

// translations
if(empty($location)) {
	$txt_html_title    = $txt_html_title_no_loc;
	$txt_meta_desc     = $txt_meta_desc_no_loc;
	$txt_main_title    = $txt_main_title_no_loc;

	$txt_html_title    = str_replace('%search_term%', e($user_query), $txt_html_title);
	$txt_meta_desc     = str_replace('%search_term%', e($user_query), $txt_meta_desc);
	$txt_main_title    = str_replace('%search_term%', e($user_query), $txt_main_title);
	$txt_empty_results = str_replace('%search_term%', e($user_query), $txt_empty_results);
}
else {
	$txt_html_title    = str_replace('%search_term%', e($user_query), $txt_html_title);
	$txt_html_title    = str_replace('%location%'   , $location     , $txt_html_title);
	$txt_meta_desc     = str_replace('%search_term%', e($user_query), $txt_meta_desc);
	$txt_meta_desc     = str_replace('%location%'   , $location     , $txt_meta_desc);
	$txt_main_title    = str_replace('%search_term%', e($user_query), $txt_main_title);
	$txt_main_title    = str_replace('%location%'   , $location     , $txt_main_title);
	$txt_empty_results = str_replace('%search_term%', e($user_query), $txt_empty_results);
}

// sanitize
$user_query = e($user_query);

// template file
require_once(__DIR__ . '/templates/tpl_searchresults.php');