<?php
require_once(__DIR__ . '/inc/config.php');

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
$page_id = $frags[1];

// validate page_id
if(!is_numeric($page_id)) {
	header("HTTP/1.0 404 Not Found");
	die('404 Not Found. Problem with path info.');
}

// query db
$query = "SELECT * FROM pages WHERE page_id = :page_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':page_id', $page_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title    = $row['page_title'];
$page_slug     = $row['page_slug'];
$meta_desc     = $row['meta_desc'];
$page_contents = $row['page_contents'];

/*--------------------------------------------------
include template file
--------------------------------------------------*/
require_once(__DIR__ . '/templates/tpl_page.php');
