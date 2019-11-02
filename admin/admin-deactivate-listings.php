<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-tools.php');

$query = "UPDATE places SET paid = 0 WHERE valid_until < CURRENT_TIMESTAMP";
$stmt = $conn->prepare($query);

if($stmt->execute()) {
	?>
	<p><?= $txt_deactivate_success; ?></p>
<?php
}
else {
	?>
	<p><?= $txt_deactivate_fail; ?></p>
<?php
}
