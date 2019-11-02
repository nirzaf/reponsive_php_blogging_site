<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-emails.php');

$query = "SELECT * FROM email_templates LIMIT 10";
$stmt = $conn->prepare($query);
$stmt->execute();

$email_templates_arr = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$template_id          = $row['id'];
	$template_type        = $row['type'];
	$template_subject     = $row['subject'];
	$template_description = $row['description'];

	$cur_arr = array(
				'template_id'          => $template_id,
				'template_type'        => $template_type,
				'template_subject'     => $template_subject,
				'template_description' => $template_description
				);
	$templates_arr[] = $cur_arr;
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>
textarea.form-control {
	height: 250px;
}
</style>
</head>
<body class="admin-emails">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<?php
			if(!empty($templates_arr)) {
				?>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th><?= $txt_type; ?></th>
							<th><?= $txt_subject; ?></th>
							<th><?= $txt_description; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($templates_arr as $k => $v) {
							?>
							<tr id="template-<?= $v['template_id']; ?>">
								<td><?= $v['template_type']; ?></td>
								<td><?= $v['template_subject']; ?></td>
								<td><?= $v['template_description']; ?></td>
								<td class="nowrap">
									<span id="edit-template-<?= $v['template_id']; ?>" data-toggle="tooltip" title="<?= $txt_edit_template; ?>">
										<a href="#" class="btn btn-default btn-less-padding edit-template-btn"
											data-template-id="<?= $v['template_id']; ?>"
											data-toggle="modal"
											data-target="#edit-template-modal">
											<i class="fa fa-pencil"></i>
										</a>
									</span>
								</td>
							</tr>
						<?php
						}
					?>
					</table>
				</div>
				<?php
			}
			else {
				?>
				<p><?= $txt_no_templates; ?></p>
				<?php
			}
			?>

			<h3><?= $txt_available_vars_header; ?></h3>

			<div><strong>reset_pass:</strong> %reset_link%</div>
			<div><strong>signup_confirm:</strong> %confirm_link%</div>
			<div><strong>subscr_failed:</strong> %username%</div>
			<div><strong>subscr_signup:</strong> %username%, %place_link%</div>
			<div><strong>web_accept:</strong> %username%, %place_link%</div>
			<div><strong>subscr_eot:</strong> %username%, %place_link%</div>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- modal edit email template -->
<div class="modal fade" id="edit-template-modal" tabindex="-1" role="dialog" aria-labelledby="Edit Email Template Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_edit_template; ?></h3>
			</div>
			<div class="modal-body">

			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-blue btn-less-padding" type="submit" id="edit-template-submit">
				<a href="#" class="btn btn-default btn-less-padding" data-dismiss="modal" id="btn-dismiss"><?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end modal -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<script>
$(document).ready(function(){
	// show edit email template modal
	$('#edit-template-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var template_id = button.data('template-id'); // Extract info from data-* attributes
		var modal = $(this);

		// reinitialize buttons if needed
		$('#edit-template-submit').show();
		$('#btn-dismiss').empty().append('cancel');

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-get-email-template.php';

		$.post(post_url, { template_id: template_id },
			function(data) {
				modal.find('.modal-body').html(data);
			}
		);
	});

	// submit edit plan modal
    $('#edit-template-submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-edit-email-template.php';

		$.post(post_url, {
			params: $('form.form-edit-email-template').serialize(),
			},
			function(data) {
				$('.modal-body').empty().html(data);
				$('#edit-template-submit').hide();
				$('#btn-dismiss').empty().append('ok');
			}
		);
    });

	// edit cat modal on close
	$('#edit-template-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});
});
</script>
</body>
</html>