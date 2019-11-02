<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-modal-form-create-loc.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$loc_type = $_POST['loc_type'];

if($loc_type == 'city') {
	?>
	<form class="form-create-loc" method="post">
		<input type="hidden" id="loc_type" name="loc_type" value="city">
		<div class="block">
			<label class="label" for="city_name"><?= $txt_city_name; ?></label><br>
			<input type="text" id="city_name" name="city_name" class="form-control" required>
		</div>

		<div class="block">
			<label class="label" for="state"><?= $txt_select_state; ?></label><br>
			<select id="state" name="state" class="form-control" required>
				<?php
				// count states
				$query = "SELECT COUNT(*) AS total_rows FROM states";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_rows = $row['total_rows'];

				if($total_rows > 0) {
					// select all states
					$query = "SELECT * FROM states ORDER BY state_name";
					$stmt = $conn->prepare($query);
					$stmt->execute();

					while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$state_id     = $row['state_id'];
						$state_name   = $row['state_name'];
						$state_abbr   = $row['state_abbr'];

						$value = "$state_id,$state_abbr";
						?>
						<option value="<?= $value; ?>"><?= $state_name; ?></option>
						<?php
					}
				}
				else {
					?>
					<option value=""><?= $txt_msg_no_state; ?></option>
					<?php
				}
				?>
			</select>
		</div>
	</form>
	<?php
}
if($loc_type == 'state') {
	?>
	<form class="form-create-loc" method="post">
		<input type="hidden" id="loc_type" name="loc_type" value="state">

		<div class="block">
			<label class="label" for="state_name"><?= $txt_state_name; ?></label><br>
			<input type="text" id="state_name" name="state_name" class="form-control" required>
		</div>

		<div class="block">
			<label class="label" for="state_abbr"><?= $txt_state_abbr; ?></label><br>
			<input type="text" id="state_abbr" name="state_abbr" class="form-control" required>
		</div>

		<div class="block">
			<label class="label" for="state"><?= $txt_select_country; ?></label><br>
			<select id="country" name="country" class="form-control" required>
				<?php
				// count states
				$query = "SELECT COUNT(*) AS total_rows FROM countries";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_rows = $row['total_rows'];

				if($total_rows > 0) {
					// select all states
					$query = "SELECT * FROM countries ORDER BY country_name";
					$stmt = $conn->prepare($query);
					$stmt->execute();

					while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$country_id     = $row['country_id'];
						$country_name   = $row['country_name'];
						$country_abbr   = $row['country_abbr'];

						$value = "$country_id,$country_abbr";
						?>
						<option value="<?= $value; ?>"><?= $country_name; ?></option>
						<?php
					}
				}
				else {
					?>
					<option value=""><?= $txt_msg_no_country; ?></option>
					<?php
				}
				?>
			</select>
		</div>
	</form>
	<?php
}
if($loc_type == 'country') {
	?>
	<form class="form-create-loc" method="post">
		<input type="hidden" id="loc_type" name="loc_type" value="country">
		<div class="block">
			<label class="label" for="country_name"><?= $txt_country_name; ?></label><br>
			<input type="text" id="country_name" name="country_name" class="form-control" required>
		</div>

		<div class="block">
			<label class="label" for="country_abbr"><?= $txt_country_abbr; ?></label><br>
			<input type="text" id="country_abbr" name="country_abbr" class="form-control" required>
		</div>
	</form>
	<?php
}