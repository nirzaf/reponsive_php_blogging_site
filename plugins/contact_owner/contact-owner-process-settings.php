<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');
require_once(__DIR__ . '/translation.php');
require_once($lang_folder . '/admin_translations/_trans-global.php');

// csrf check
require_once(__DIR__ . '/../../admin/_admin_inc_request_with_php.php');

// post vars
$question      = (!empty($_POST['question']     )) ? $_POST['question']      : '';
$answer        = (!empty($_POST['answer']       )) ? $_POST['answer']        : '';
$email_subject = (!empty($_POST['email_subject'])) ? $_POST['email_subject'] : '';

// trim
$question      = trim($question);
$answer        = trim($answer);
$email_subject = trim($email_subject);

// values
$value = array('question' => $question, 'answer' => $answer, 'email_subject' => $email_subject);
$value = serialize($value);

$query = "SELECT COUNT(*) AS num_rows FROM config WHERE property = 'plugin_contact_owner'";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$num_rows = $row['num_rows'];

if($num_rows > 0) {
	$query = "UPDATE config SET value = :value WHERE property = 'plugin_contact_owner'";
}
else {
	$query = "INSERT INTO config(type, property, value) VALUES('plugin', 'plugin_contact_owner', :value)";
}

$stmt = $conn->prepare($query);
$stmt->bindValue(':value', $value);
$stmt->execute();
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_contact_owner_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../admin/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="admin-cats">
<?php require_once(__DIR__ . '/../../admin/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/../../admin/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_contact_owner_main_title; ?></h2>

		<div class="padding">
			<div class="alert alert-success"><?= $txt_contact_owner_process_settings; ?></div>
		</div>
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->


<?php require_once(__DIR__ . '/../../admin/_admin_footer.php'); ?>
</body>
</html>