<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/user_area_inc.php');

// user details
$userid = $_SESSION['userid'];

$stmt = $conn->prepare('SELECT hybridauth_provider_name FROM users WHERE id = :userid');
$stmt->bindValue(':userid', $userid);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$hybridauth_provider_name = $row['hybridauth_provider_name'];

// template file
require_once(__DIR__ . '/../templates/user_templates/tpl_edit-pass.php');