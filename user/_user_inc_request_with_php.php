<?php
if(empty($_POST['csrf_token'])) {
	die("Missing Token");
}

if($_POST['csrf_token'] != session_id()) {
	die("Invalid Token");
}