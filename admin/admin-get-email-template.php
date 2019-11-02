<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-get-email-template.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$template_id = $_POST['template_id'];

$query = "SELECT * FROM email_templates WHERE id = :template_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':template_id', $template_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$template_type    = $row['type'];
$template_subject = $row['subject'];
$template_body    = $row['body'];

// instructions
$instruct = '';
if($template_type == 'reset_pass') {
	$instruct = $txt_instruct_reset;
}
if($template_type == 'signup_confirm') {
	$instruct = $txt_instruct_signup;
}

?>
<form class="form-edit-email-template" method="post">
	<input type="hidden" id="template_id" name="template_id" value="<?= $template_id; ?>">

	<div class="block">
		<label class="label" for="template_subject"><?= $txt_email_subject; ?></label><br>
		<input type="text" id="template_subject" name="template_subject" class="form-control" value="<?= $template_subject; ?>">
	</div>

	<div class="block">
		<label class="label" for="template_body"><?= $txt_email_body; ?></label><br>
		<?= $instruct; ?><br>
		<textarea id="template_body" name="template_body" class="form-control"><?= $template_body; ?></textarea>
	</div>
</form>