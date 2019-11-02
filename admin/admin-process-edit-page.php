<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-process-edit-page.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$params = array();
parse_str($_POST['params'], $params);

$page_id       = (!empty($params['page_id']      ))? $params['page_id']       : 0;
$page_title    = (!empty($params['page_title']   ))? $params['page_title']    : 'undefined';
$meta_desc     = (!empty($params['meta_desc']    ))? $params['meta_desc']     : '';
$page_group    = (!empty($params['page_group']   ))? $params['page_group']    : '';
$page_order    = (!empty($params['page_order']   ))? $params['page_order']    : 0;
$page_contents = (!empty($params['page_contents']))? $params['page_contents'] : '';

// prepare vars
$page_slug = to_slug($page_title);
$page_order = (int)$page_order;

$query = "UPDATE pages SET
			page_title    = :page_title,
			page_slug     = :page_slug,
			meta_desc     = :meta_desc,
			page_contents = :page_contents,
			page_group    = :page_group,
			page_order    = :page_order
		WHERE page_id = :page_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_id', $page_id);
$stmt->bindValue(':page_title', $page_title);
$stmt->bindValue(':page_slug', $page_slug);
$stmt->bindValue(':meta_desc', $meta_desc);
$stmt->bindValue(':page_group', $page_group);
$stmt->bindValue(':page_order', $page_order);
$stmt->bindValue(':page_contents', $page_contents);

if($stmt->execute()) {
	?>
	<p><?= $txt_page_updated; ?></p>
	<?php
}
else {
	?>
	<p>Problem updating the page. Please try again.</p>
	<?php
}
