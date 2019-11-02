<?php
require_once(__DIR__ . '/../inc/config.php');

// unset session vars
$_SESSION = array();

// before deleting cookie, delete entry from database
if(!empty($_COOKIE['loggedin'])) {
	$cookie_frags = explode('-', $_COOKIE['loggedin']);
	$cookie_userid = $cookie_frags[0];
	$cookie_token = $cookie_frags[2];

	$query = "DELETE FROM loggedin WHERE userid = :userid AND token = :token";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':userid', $cookie_userid);
	$stmt->bindValue(':token', sha1($cookie_token));
	$stmt->execute();
}

// remove loggedin cookie
setcookie('loggedin', '', time()-42000, $install_path, '', '', true);

// remove session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// referer
$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

if(empty($referer)) {
	$referer = $baseurl;
}

if(strpos($referer, '/user/') !== false) {
	$referer = $baseurl;
}

if(strpos($referer, '/admin/') !== false) {
	$referer = $baseurl;
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_logoff.php');