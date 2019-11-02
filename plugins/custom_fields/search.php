<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once($lang_folder . '/trans-list.php');
require_once(__DIR__ . '/translation.php');

$loc_type   = (!empty($_GET['loc_type'])) ? $_GET['loc_type'] : 'n';
$loc_id     = (!empty($_GET['city_id'] )) ? $_GET['city_id']  : 0;
$cat_id     = (!empty($_GET['cats']    )) ? $_GET['cats']     : array();
$page       = (!empty($_GET['page']    )) ? $_GET['page']     : 1;
$user_query = (!empty($_GET['q']       )) ? $_GET['q']        : '';

// append *
$query_query = explode(' ', $user_query);
$new_query = '';

foreach($query_query as $v) {
	$new_query .= "$v* ";
}

$q = $new_query;

if($loc_id > 0) {
	$loc_type = 'c';
}

$query_str = '';
$i = 0;
foreach($_GET as $k => $v) {
	if($i > 0) {
		$query_str .= '&';
	}

	if(!is_array($v)) {
		$query_str .= e($k);
		$query_str .= '=';
		$query_str .= e($v);
	}
	else {
		$j = 0;
		foreach($v as $k2 => $v2) {
			if($j > 0) {
				$query_str .= '&';
			}
			$query_str .= e($k);
			$query_str .= '[]=';
			$query_str .= e($v2);
			$j++;
		}
	}

	$i++;
}

// query string without page
/*--------------------------------------------------
validate GET vars
--------------------------------------------------*/
if(basename($_SERVER['SCRIPT_NAME']) == 'list.php') {
	if(!is_numeric($loc_id)
		|| !is_numeric($cat_id)
		|| !is_numeric($page)
		|| ($loc_type != 'n' && $loc_id == 0)
		|| ($loc_type == 'n' && $cat_id == 0)) {
		header("HTTP/1.0 404 Not Found");
		die('404 Not Found. Problem with url query string');
	}
}

if(basename($_SERVER['SCRIPT_NAME']) == 'search.php') {
	// reserved for later
}

/*--------------------------------------------------
Custom fields
--------------------------------------------------*/
$custom_fields = array();
foreach($_GET as $k => $v) {
	// if needle 'field_' is found
	if(strpos($k, 'field_') !== false && !empty($v)) {
		// sanitize
		if(is_numeric(str_replace('field_', '', $k))) {
			$this_id    = str_replace('field_', '', $k);
			$this_value = $v;

			$this_arr   = array(
				'field_id'    => (int)$this_id,
				'field_value' => $this_value
			);

			$custom_fields[] = $this_arr;
		}
	}
}

/*--------------------------------------------------
Build join and where clauses to use in query
--------------------------------------------------*/
$query_join  = '';
$query_where = '';
if(!empty($custom_fields)) {
	$query_join = " LEFT JOIN rel_place_custom_fields rpcf ON p.place_id = rpcf.place_id ";
	$query_where = ' AND (';

	$i = 1;
	foreach($custom_fields as $k => $v) {
		$field_id    = $v['field_id'];
		$field_value = $v['field_value'];

		if($i > 1) {
			$query_where .= " OR ";
		}

		//  WHERE  MATCH(title,description) AGAINST ('+Joins -right' IN BOOLEAN MODE);

		if(!is_array($field_value)) {
			$field_value_param = 'value_' . $field_id;
			//$query_where .= " (rpcf.field_id = $field_id AND rpcf.field_value = :$field_value_param) ";
			$query_where .= " (rpcf.field_id = $field_id AND MATCH(rpcf.field_value) AGAINST(:$field_value_param IN BOOLEAN MODE)) ";
		}

		// else field_value is array
		else {
			$j = 1;
			foreach($field_value as $k2 => $v2) {
				if($j > 1) {
					$query_where .= " OR ";
				}

				$field_value_param = 'value_' . $field_id;
				$field_value_param .= '_' . $k2;
				$query_where .= " (rpcf.field_id = $field_id AND rpcf.field_value = :$field_value_param)";
				$j++;
			}
		}
		$i++;
	}

	$query_where .= ') ';
}

/*--------------------------------------------------
Initialize combined response
--------------------------------------------------*/
$list_items = array();

/*--------------------------------------------------
get all cats
--------------------------------------------------*/
// string which will hold comma separated list of cats ids to be used in the mysql query
$in_str = '';

if(!empty($cat_id)) {
	$in = array();

	foreach($cat_id as $v) {
		$v = (int) $v;

		if(!empty($v)) {
			$in[] = $v;

			$children = get_children_cats_ids($v, $conn);

			if(!empty($children)) {
				foreach($children as $v2) {
					$in[] = $v2;
				}
			}
		}
	}

	foreach($in as $k => $v) {
		if($k == 0) {
			$in_str .= $v;
		}
		else {
			$in_str .= ",$v";
		}
	}
}

/*--------------------------------------------------
Count results first
--------------------------------------------------*/
$total_rows = 0;

$query_match_q = '';
if(strlen($user_query) > 2) {
	$query_match_q = " AND MATCH(p.place_name, p.description) AGAINST(:q IN BOOLEAN MODE) ";
}

if($loc_type == 'n') {
	// if all cats and no specific location
	if(empty($in_str)) {
		// doesn't happen?
		// in search it happens
		$query = "
			SELECT COUNT(*) AS total_rows FROM
				(
				SELECT p.place_id FROM places p
					LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
					$query_join
					WHERE p.status = 'approved' AND p.paid = 1
					$query_match_q
					$query_where
					GROUP BY place_id
				) temp
			";

		$stmt = $conn->prepare($query);
	} // end if all cats and no specific location

	// specific cat and no location
	else {
		$query = "
			SELECT COUNT(*) AS total_rows FROM
				(
				SELECT p.place_id FROM places p
					LEFT JOIN rel_place_cat r ON p.place_id = r.place_id
					$query_join
					WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
					$query_match_q
					$query_where
					GROUP BY place_id
				) temp
			";
		$stmt = $conn->prepare($query);
	}
}

// if list by city
if($loc_type == 'c') {
	// if all cats and by city
	if(empty($in_str)) {
		$query = "
			SELECT COUNT(*) AS total_rows FROM
				(
				SELECT p.place_id FROM places p
					$query_join
					WHERE p.city_id = :loc_id AND p.status = 'approved' AND paid = 1
					$query_match_q
					$query_where
					GROUP BY place_id
				) temp
			";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':loc_id', $loc_id);
	}
	// end if all cats and by city

	// if specific category and by city
	else {
		$query = "
			SELECT COUNT(*) AS total_rows FROM
				(
				SELECT p.place_id FROM places p
					INNER JOIN rel_place_cat r ON p.place_id = r.place_id
					$query_join
					WHERE p.city_id = :loc_id AND p.status = 'approved' AND p.paid = 1
					AND r.cat_id IN ($in_str)
					$query_match_q
					$query_where
					GROUP BY place_id
				) temp
			";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':loc_id', $loc_id);
	}
	// end if specific category and by city
} // end if list by city

// bind dynamic and get results
// bind dynamically
if(!empty($custom_fields)) {
	foreach($custom_fields as $k => $v) {
		$field_id    = $v['field_id'];
		$field_value = $v['field_value'];

		// bind field_value
		if(!is_array($field_value)) {
			$field_value_param = 'value_' . $field_id;
			// keep line below for reference purposes
			// $query_where .= " (rpcf.field_id = $field_id AND rpcf.field_value = :$field_value_param) ";

			// bind values
			$stmt->bindValue(":$field_value_param", $field_value);
		}

		// else field_value is array
		else {
			foreach($field_value as $k2 => $v2) {
				$field_value_param = 'value_' . $field_id;
				$field_value_param .= '_' . $k2;

				// bind values
				$stmt->bindValue(":$field_value_param", $v2);
			}
		}
	}
}

//execute count query

if(strlen($user_query) > 2) {
	$stmt->bindValue(":q", $q);
}
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = (!empty($row['total_rows'])) ? $row['total_rows'] : 0;

/*--------------------------------------------------
After counting, do the query proper
--------------------------------------------------*/
$start = 0;
if($loc_type == 'n') {
	// if all cats and no specific location
	if(empty($in_str)) {
		// doesn't happen?
		// it happens in advanced search
		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.area_code, p.phone, p.lat, p.lng, p.state_id, p.feat,
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
				$query_join
				WHERE p.status = 'approved' AND p.paid = 1
				$query_match_q
				$query_where
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
		}
	} // end if all cats and no specific location

	// specific cat and no location
	else {
		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.area_code, p.phone, p.lat, p.lng, p.state_id, p.feat,
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
				$query_join
				WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
				$query_match_q
				$query_where
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
		}
	}
}

// if list by city
if($loc_type == 'c') {
	// if all cats and by city
	if(empty($in_str)) {
		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.area_code, p.phone, p.lat, p.lng, p.state_id, p.feat,
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
				$query_join
				WHERE p.city_id = :loc_id AND p.status = 'approved' AND p.paid = 1
				$query_match_q
				$query_where
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':loc_id', $loc_id);
		}
	}
	// end if all cats and by city

	// if specific category and by city
	else {
		if($total_rows > 0) {
			$pager = new DirectoryApp\PageIterator($items_per_page, $total_rows, $page);
			$start = $pager->getStartRow();

			$query = "SELECT
					p.place_id, p.place_name, p.address, p.cross_street,
					p.postal_code, p.area_code, p.phone, p.lat, p.lng, p.state_id, p.feat,
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
				$query_join
				WHERE r.cat_id IN ($in_str) AND p.status = 'approved' AND p.paid = 1
				AND p.city_id = :loc_id
				$query_match_q
				$query_where
				GROUP BY p.place_id
				ORDER BY p.feat DESC, p.submission_date DESC
				LIMIT :start, :items_per_page";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':loc_id', $loc_id);
		}
	}
	// end if specific category and by city
} // end if list by city

// bind custom fields params
if($total_rows > 0) {
	if(!empty($custom_fields)) {
		foreach($custom_fields as $k => $v) {
			$field_id    = $v['field_id'];
			$field_value = $v['field_value'];

			// bind field_value
			if(!is_array($field_value)) {
				$field_value_param = 'value_' . $field_id;
				// keep line below for reference purposes
				// $query_where .= " (rpcf.field_id = $field_id AND rpcf.field_value = :$field_value_param) ";

				// bind values
				$stmt->bindValue(":$field_value_param", $field_value);
			}

			// else field_value is array
			else {
				foreach($field_value as $k2 => $v2) {
					$field_value_param = 'value_' . $field_id;
					$field_value_param .= '_' . $k2;

					// $query_where .= " (rpcf.field_id = :$field_id AND rpcf.field_value = :$field_value_param)";

					// bind values
					$stmt->bindValue(":$field_value_param", $v2);
				}
			}
		}
	}

	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':items_per_page', $items_per_page);
	if(strlen($q) > 2) {
		$stmt->bindValue(':q', $q);
	}
	// execute query
	$stmt->execute();
}

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

/*--------------------------------------------------
html title and meta descriptions
--------------------------------------------------*/
$total_items = '';

$txt_html_title   = $txt_search_results;
$txt_meta_desc    = '';

/*--------------------------------------------------
breadcrumbs
--------------------------------------------------*/
$breadcrumbs = '';

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
		$page_url = "$baseurl/plugins/custom_fields/search.php?" . $query_str . '&page=';

		if ($curPage > 1) {
			$pager_template .= "<li><a href='$page_url" . "1'>$txt_pager_page1</a></li>";
		}
		if ($curPage > 6) {
			$pager_template .= "<li><span>...</span></li>";
		}
		if ($curPage == 1) {
			$pager_template .= "<li class='active'><span>$txt_pager_page1</span></li>";
		}
		for($i = $startPage; $i <= $endPage; $i++) {
			if($i == $page) {
				$pager_template .= "<li class='active'><span>$i</span></li>";
			}
			else {
				$pager_template .= "<li><a href='$page_url" . "$i'>$i</a></li>";
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
			$pager_template .= "<li class='active'><span>$last_page_txt</span></li>";
		}
		else {
			$pager_template .= "<li><a href='$page_url" . $pager->getTotalPages() . "'>$last_page_txt</a></li>";
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
$canonical = "$baseurl/plugins/custom_fields/search.php";

/*--------------------------------------------------
include template file
--------------------------------------------------*/
$dont_index  = true;
$breadcrumbs = '';
require_once(__DIR__ . '/../../templates/tpl_list.php');
