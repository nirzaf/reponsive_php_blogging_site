<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-get-plan.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$plan_id = $_POST['plan_id'];

$query = "SELECT * FROM plans WHERE plan_id = :plan_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':plan_id', $plan_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$plan_type         = $row['plan_type'];
$plan_name         = $row['plan_name'];
$plan_description1 = $row['plan_description1'];
$plan_description2 = $row['plan_description2'];
$plan_description3 = $row['plan_description3'];
$plan_description4 = $row['plan_description4'];
$plan_description5 = $row['plan_description5'];
$plan_period       = $row['plan_period'];
$plan_price        = $row['plan_price'];
$plan_order        = $row['plan_order'];
$plan_status       = $row['plan_status'];

$plan_name         = e($plan_name);
$plan_description1 = e($plan_description1);
$plan_description2 = e($plan_description2);
$plan_description3 = e($plan_description3);
$plan_description4 = e($plan_description4);
$plan_description5 = e($plan_description5);
?>
<form class="form-edit-plan" method="post">
	<input type="hidden" id="plan_id" name="plan_id" value="<?= $plan_id; ?>">
	<input type="hidden" id="plan_type" name="plan_type" value="<?= $plan_type; ?>">
	<div class="block">
		<strong><?= $txt_plan_type; ?>: </strong> <span id="plan_type"><?= $plan_type; ?></span>
	</div>

	<div class="block">
		<label class="label" for="plan_name"><?= $txt_plan_name; ?></label><br>
		<input type="text" id="plan_name" name="plan_name" class="form-control" value="<?= $plan_name; ?>">
	</div>

	<div class="block">
		<label class="label" for="plan_description1"><?= $txt_plan_desc1; ?></label><br>
		<textarea id="plan_description1" name="plan_description1" class="form-control"><?= $plan_description1; ?></textarea>
	</div>

	<div class="block">
		<label class="label" for="plan_description2"><?= $txt_plan_desc2; ?></label><br>
		<textarea id="plan_description2" name="plan_description2" class="form-control"><?= $plan_description2; ?></textarea>
	</div>

	<div class="block">
		<label class="label" for="plan_description3"><?= $txt_plan_desc3; ?></label><br>
		<textarea id="plan_description3" name="plan_description3" class="form-control"><?= $plan_description3; ?></textarea>
	</div>

	<div class="block">
		<label class="label" for="plan_description4"><?= $txt_plan_desc4; ?></label><br>
		<textarea id="plan_description4" name="plan_description4" class="form-control"><?= $plan_description4; ?></textarea>
	</div>

	<div class="block">
		<label class="label" for="plan_description5"><?= $txt_plan_desc5; ?></label><br>
		<textarea id="plan_description5" name="plan_description5" class="form-control"><?= $plan_description5; ?></textarea>
	</div>

	<?php
	// plan period is 0 if plan type is monthly
	if($plan_type != 'monthly' && $plan_type != 'monthly_feat') {
		?>
		<div class="block">
			<label class="label" for="plan_period"><?= $txt_plan_period; ?></label><br>
			<input type="number" id="plan_period" name="plan_period" class="form-control" value="<?= $plan_period; ?>" required>
		</div>
		<?php
	}
	?>

	<div class="block">
		<label class="label" for="plan_order"><?= $txt_plan_order; ?></label><br>
		<input type="number" id="plan_order" name="plan_order" class="form-control" value="<?= $plan_order; ?>">
	</div>

	<div class="block">
		<label class="label" for="plan_price"><?= $txt_plan_price; ?></label><br>
		<?php
		if($plan_type != 'free' && $plan_type != 'free_feat') {
			?>
			<input type="number" id="plan_price" name="plan_price" class="form-control" value="<?= $plan_price; ?>">
			<?php
		}
		else {
			?>
			<input type="hidden" id="plan_price" name="plan_price" class="form-control" value="<?= $plan_price; ?>">
			<?= $plan_price; ?> <em><?= $txt_change_price; ?></em>
			<?php
		}
		?>
	</div>

	<div class="block">
		<label class="label" for="plan_status"><?= $txt_plan_status; ?></label><br>
		<?= $txt_yes; ?> <input type="radio" id="plan_status" name="plan_status" value="1" <?php if($plan_status == 1) echo 'checked' ?>><br>
		<?= $txt_no; ?> <input type="radio" id="plan_status" name="plan_status" value="0" <?php if($plan_status == 0) echo 'checked' ?>>
	</div>
</form>