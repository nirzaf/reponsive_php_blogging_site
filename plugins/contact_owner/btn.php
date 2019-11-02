<?php
require __DIR__ . '/translation.php';

$query = "SELECT * FROM config WHERE property = :plugin_contact_owner";
$stmt  = $conn->prepare($query);
$stmt->bindValue(':plugin_contact_owner', 'plugin_contact_owner');
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$plugin_values = $row['value'];
$plugin_values = unserialize($plugin_values);

?>
<a href=""
	class="btn btn-default btn-even-less-padding launch-contact-form"
	style="font-weight: 700;"
	data-toggle="modal"
	data-target="#contact-owner-modal">
	<em><?= $txt_contact_owner_btn_contact; ?></em>
</a>

<!-- modal contact owner -->
<div class="modal fade" id="contact-owner-modal" tabindex="-1" role="dialog" aria-labelledby="Contact Owner Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_contact_owner_main_title; ?></h3>
			</div>
			<div class="modal-body">
				<div id="contact-owner-result"></div>
				<form id="contact-owner-form" method="post">
					<input type="hidden" name="place_id" id="place_id" value="<?= $place_id; ?>">

					<div class="form-row">
						<label for=""><?= $txt_contact_owner_label_email; ?></label>
						<input type="email" name="sender_email" id="sender_email"
						<?php
						if(!empty($email)) echo "value='$email'";
						?>
						>
					</div>

					<div class="form-row">
						<label for=""><?= $txt_contact_owner_label_msg; ?></label>
						<textarea name="sender_msg" id="sender_msg" style="height: 72px; min-height: 36px;"></textarea>
					</div>

					<div class="form-row">
						<label for=""><?= $plugin_values['question']; ?></label>
						<input type="text" name="verify_answer" id="verify_answer">
					</div>
				</form>
			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-blue btn-less-padding" type="submit" id="submit-contact-owner">
				<a href="#" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end modal edit plan -->

<script>
$(document).ready(function(){
	$('#contact-owner-result').hide();

	$('#contact-owner-modal').on('show.bs.modal', function (event) {
		$('#contact-owner-result').hide(120);
		$('#contact-owner-form').show(120);

	});

	$('#submit-contact-owner').click(function(e) {
		e.preventDefault();

		// vars
		var place_id      = $('#place_id').val();
		console.log('place id is ' + place_id);
		var sender_email  = $('#sender_email').val();
		var sender_msg    = $('#sender_msg').val();
		var verify_answer = $('#verify_answer').val();
		var url           = '<?= $baseurl; ?>/plugins/contact_owner/send-msg.php';
		var spinner       = '<i class="fa fa-spinner fa-spin fa-fw"></i><span class="sr-only">Loading...</span>';

		// hide form and show spinner
		$('#contact-owner-result').show();
		$('#contact-owner-form').toggle(120);
		$('#contact-owner-result').html(spinner).fadeIn();

		// ajax post
		$.post(url, {
			place_id      : place_id,
			sender_email  : sender_email,
			sender_msg    : sender_msg,
			verify_answer : verify_answer
		}, function(data) {

			$('#contact-owner-result').html(data).fadeIn();
			console.log(data);
		});
	});
});
</script>