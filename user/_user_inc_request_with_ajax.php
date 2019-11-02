<?php
if(empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
	die("Missing Token");
}

if($_SERVER['HTTP_X_CSRF_TOKEN'] != session_id()) {
	die("Invalid Token");
}
