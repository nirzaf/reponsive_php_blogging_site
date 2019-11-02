<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-plans.php');

$query = "SELECT * FROM plans WHERE plan_status > -1 ORDER BY plan_order";
$stmt = $conn->prepare($query);
$stmt->execute();

$plans_arr = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$plan_id          = $row['plan_id'];
	$plan_type        = $row['plan_type'];
	$plan_name        = $row['plan_name'];
	$plan_description = $row['plan_description1'];
	$plan_price       = $row['plan_price'];
	$plan_status      = $row['plan_status'];

	// sanitize
	$plan_name        = e($plan_name);

	$cur_arr = array(
				'plan_id'          => $plan_id,
				'plan_type'        => $plan_type,
				'plan_name'        => $plan_name,
				'plan_description' => $plan_description,
				'plan_price'       => $plan_price,
				'plan_status'      => $plan_status);
	$plans_arr[] = $cur_arr;
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>
textarea {
	height: 50px;
}
</style>
</head>
<body class="admin-plans">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<strong><?= $txt_action; ?>:</strong><br>
				<a href="" class="create-plan-btn btn btn-blue btn-less-padding"
					data-loc-type="city"
					data-modal-title="<?= $txt_create; ?>"
					data-toggle="modal"
					data-target="#create-plan-modal"
					>Create plan</a>
			</div>

			<?php
			if(!empty($plans_arr)) {
				?>
				<?= $txt_view_plans_page; ?>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th><?= $txt_plan_type; ?></th>
							<th><?= $txt_plan_name; ?></th>
							<th><?= $txt_description; ?></th>
							<th><?= $txt_price; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($plans_arr as $k => $v) {
							$this_plan_id          = $v['plan_id'];
							$this_plan_type        = $v['plan_type'];
							$this_plan_name        = $v['plan_name'];
							$this_plan_description = $v['plan_description'];
							$this_plan_price       = $v['plan_price'];
							$this_plan_status      = $v['plan_status'];
							?>
							<tr id="plan-<?= $this_plan_id; ?>">
								<td><?= $this_plan_type; ?></td>
								<td><?= $this_plan_name; ?></td>
								<td><?= $this_plan_description; ?></td>
								<td><?= $this_plan_price; ?></td>
								<td class="nowrap">
									<?php
									if($this_plan_status == 0) {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_active; ?>">
											<a href="#" class="btn btn-default btn-less-padding toggle-plan-status"
												id="toggle-plan-<?= $this_plan_id; ?>"
												data-plan-id="<?= $this_plan_id; ?>"
												data-plan-status="inactive">
												<i class="fa fa-toggle-off" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_active; ?>">
											<a href="#" class="btn btn-green btn-less-padding toggle-plan-status"
												id="toggle-plan-<?= $this_plan_id; ?>"
												data-plan-id="<?= $this_plan_id; ?>"
												data-plan-status="active">
												<i class="fa fa-toggle-on" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									?>
									<span id="edit-plan-<?= $this_plan_id; ?>" data-toggle="tooltip" title="<?= $txt_edit_plan; ?>">
										<a href="#" class="btn btn-default btn-less-padding edit-plan-btn"
											data-plan-id="<?= $this_plan_id; ?>"
											data-toggle="modal"
											data-target="#edit-plan-modal">
											<i class="fa fa-pencil"></i>
										</a>
									</span>

									<span data-toggle="tooltip" title="<?= $txt_remove_plan; ?>">
										<a href="#" class="btn btn-default btn-less-padding remove-plan"
											data-plan-id="<?= $this_plan_id; ?>">
											&nbsp;<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
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
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- modal edit plan -->
<div class="modal fade" id="edit-plan-modal" tabindex="-1" role="dialog" aria-labelledby="Edit Plan Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_edit_plan; ?></h3>
			</div>
			<div class="modal-body">

			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-blue btn-less-padding" type="submit" id="edit-plan-submit">
				<a href="#" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end modal edit plan -->

<!-- modal create plan -->
<div class="modal fade" id="create-plan-modal" tabindex="-1" role="dialog" aria-labelledby="Create Plan Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?= $txt_close; ?>"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_create; ?></h3>
			</div>
			<div class="modal-body">
				<form class="form-create-plan" method="post">
					<div class="block">
						<label class="label" for="plan_name"><?= $txt_plan_name; ?></label><br>
						<input type="text" id="plan_name" name="plan_name" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="plan_type"><?= $txt_plan_type; ?></label><br>
						<select id="plan_type" name="plan_type" class="form-control">
							<option value="free"><?= $txt_free; ?></option>
							<option value="free_feat"><?= $txt_free_featured; ?></option>
							<option value="one_time"><?= $txt_one_time; ?></option>
							<option value="one_time_feat"><?= $txt_one_time_f; ?></option>
							<option value="monthly"><?= $txt_monthly; ?></option>
							<option value="monthly_feat"><?= $txt_monthly_f; ?></option>
							<option value="annual">Annual</option>
							<option value="annual_feat">Annual Featured</option>
						</select>
					</div>

					<div class="block">
						<label class="label" for="plan_description1"><?= $txt_desc1; ?></label><br>
						<textarea id="plan_description1" name="plan_description1" class="form-control"></textarea>
					</div>

					<div class="block">
						<label class="label" for="plan_description2"><?= $txt_desc2; ?></label><br>
						<textarea id="plan_description2" name="plan_description2" class="form-control"></textarea>
					</div>

					<div class="block">
						<label class="label" for="plan_description3"><?= $txt_desc3; ?></label><br>
						<textarea id="plan_description3" name="plan_description3" class="form-control"></textarea>
					</div>

					<div class="block">
						<label class="label" for="plan_description4"><?= $txt_desc4; ?></label><br>
						<textarea id="plan_description4" name="plan_description4" class="form-control"></textarea>
					</div>

					<div class="block">
						<label class="label" for="plan_description5"><?= $txt_desc5; ?></label><br>
						<textarea id="plan_description5" name="plan_description5" class="form-control"></textarea>
					</div>

					<div class="block">
						<label class="label" for="plan_period"><?= $txt_period; ?></label><br>
						<input type="number" id="plan_period" name="plan_period" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="plan_order"><?= $txt_order; ?></label><br>
						<input type="number" id="plan_order" name="plan_order" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="plan_price"><?= $txt_plan_price; ?></label><br>
						<input type="number" id="plan_price" name="plan_price" class="form-control">
					</div>

					<div class="block">
						<label class="label" for="plan_status"><?= $txt_plan_status; ?></label><br>
						<?= $txt_enabled; ?> <input type="radio" id="plan_status" name="plan_status" value="1"><br>
						<?= $txt_disabled; ?> <input type="radio" id="plan_status" name="plan_status" value="0" checked>
					</div>
				</form>
			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-blue btn-less-padding" type="submit" id="create-plan-submit" value="<?= $txt_submit; ?>">
				<a href="#" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div><!-- modal content -->
	</div><!-- modal dialog -->
</div>
<!-- end modal create plan -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<script>
$(document).ready(function(){
	// show edit plan modal
	$('#edit-plan-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var plan_id = button.data('plan-id'); // Extract info from data-* attributes
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-get-plan.php';

		$.post(post_url, { plan_id: plan_id },
			function(data) {
				modal.find('.modal-body').html(data);
			}
		);
	});

	// submit edit plan modal
    $('#edit-plan-submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-edit-plan.php';

		$.post(post_url, {
			params: $('form.form-edit-plan').serialize(),
			},
			function(data) {
				location.reload(true);
			}
		);
    });

	// submit create plan modal
    $('#create-plan-submit').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-create-plan.php';

		$.post(post_url, {
			params: $('form.form-create-plan').serialize(),
			},
			function(data) {
				location.reload(true);
			}
		);
    });

	// toggle plan status
	$('.toggle-plan-status').click(function(e) {
		e.preventDefault();
		var plan_id     = $(this).data('plan-id');
		var post_url    = '<?= $baseurl; ?>' + '/admin/admin-process-toggle-plan-status.php';
		var plan_status = $(this).data('plan-status');

		$.post(post_url, {
			plan_id    : plan_id,
			plan_status: plan_status
			},
			function(data) {
				if(data == '<?= $txt_active; ?>') {
					$('#toggle-plan-' + plan_id).removeClass('btn-default');
					$('#toggle-plan-' + plan_id).addClass('btn-green');
					$('#toggle-plan-' + plan_id + ' i').removeClass('fa-toggle-off');
					$('#toggle-plan-' + plan_id + ' i').addClass('fa-toggle-on');
					$('#toggle-plan-' + plan_id).data('plan-status', 'approved');
				}
				if(data == '<?= $txt_inactive; ?>') {
					$('#toggle-plan-' + plan_id).removeClass('btn-green');
					$('#toggle-plan-' + plan_id).addClass('btn-default');
					$('#toggle-plan-' + plan_id + ' i').removeClass('fa-toggle-on');
					$('#toggle-plan-' + plan_id + ' i').addClass('fa-toggle-off');
					$('#toggle-plan-' + plan_id).data('plan-status', 'inactive');
				}
			}
		);
	});

	// remove plan
	$('.remove-plan').click(function(e){
		e.preventDefault();
		var plan_id = $(this).data('plan-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-plan.php';
		var wrapper = '#plan-' + plan_id;
		$.post(post_url, {
			plan_id: plan_id
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
				}
			}
		);
	});
});
</script>
</body>
</html>