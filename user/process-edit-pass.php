<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// csrf check
require_once(__DIR__ . '/_user_inc_request_with_php.php');

// user details
$userid = $_SESSION['userid'];

// form post
$cur_pass = $_POST['cur_pass'];
$new_pass = $_POST['new_pass'];

// trim
$cur_pass = trim($cur_pass);
$new_pass = trim($new_pass);

// vars
$msg = '';

// get user details
$query = "SELECT * FROM users WHERE id = :userid";
$stmt = $conn->prepare($query);
$stmt->bindValue(':userid', $userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$password_hash = $row['password'];
$hybridauth_provider_name = $row['hybridauth_provider_name'];

// check if cur password submitted matches the one in the database
if(password_verify($cur_pass, $password_hash)) {
	$new_pass = password_hash($new_pass, PASSWORD_BCRYPT);
	$query = "UPDATE users SET password = :new_pass WHERE id = :id";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':new_pass', $new_pass);
	$stmt->bindValue(':id', $userid);
	if($stmt->execute()) {
		$msg = "<div class=\"success padding rounded\">$txt_success</div>";
	}
	else {
		$msg = "<div class=\"label-danger padding rounded\">$txt_problem</div>";
	}
}
else {
	$msg = "<div class=\"label-danger padding rounded\">$txt_wrong</div>";
}

if($hybridauth_provider_name != 'local' && !$is_admin) {
	$msg = "<div class=\"label-danger padding rounded\">$txt_social</div>";
}

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_process-edit-pass.php');
