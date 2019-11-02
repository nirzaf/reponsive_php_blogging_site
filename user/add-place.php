<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// check if plan selected
$frags = '';
$plan_id = '';
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
$frags   = explode("/", $frags);
$plan_id = (!empty($frags[1])) ? $frags[1] : '';

if(empty($plan_id)) {
	trigger_error("Invalid plan selection", E_USER_ERROR);
	die();
}

/*--------------------------------------------------
session to prevent multiple form submissions
--------------------------------------------------*/
$submit_token = uniqid('', true);
$_SESSION['submit_token'] = $submit_token;

/*--------------------------------------------------
plugins
--------------------------------------------------*/


/*--------------------------------------------------
translations
--------------------------------------------------*/
$txt_html_title = str_replace('%site_name%', $site_name, $txt_html_title);
$txt_main_title = str_replace('%site_name%', $site_name, $txt_main_title);

require_once(__DIR__ . '/../templates/user_templates/tpl_add-place.php');