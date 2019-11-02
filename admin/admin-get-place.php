<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-get-place.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$place_id = $_POST['place_id'];

$query = "SELECT
		p.place_id, p.place_name, p.submission_date, p.status,
		c.city_name, c.state,
		rel.cat_id,
		cats.name AS cat_name
	FROM places p
		LEFT JOIN cities c ON p.city_id = c.city_id
		LEFT JOIN rel_place_cat rel ON rel.place_id = p.place_id
		LEFT JOIN cats ON rel.cat_id = cats.id
	WHERE p.place_id = :place_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':place_id', $place_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$place_name      = (!empty($row['place_name']     )) ? $row['place_name']      : '';
$city_name       = (!empty($row['city_name']      )) ? $row['city_name']       : '';
$state_abbr      = (!empty($row['state']          )) ? $row['state']           : '';
$cat_name        = (!empty($row['cat_name']       )) ? $row['cat_name']        : '';
$submission_date = (!empty($row['submission_date'])) ? $row['submission_date'] : '';
$status          = (!empty($row['status']         )) ? $row['status']          : '';

$place_name      = e($place_name);
$city_name       = e($city_name);
$state_abbr      = e($state_abbr);
$cat_name        = e($cat_name);
$submission_date = e($submission_date);
$status          = e($status);
?>
<p class="padding bg-warning rounded"><?= $txt_remove_warn; ?></p>

<div class="modal-col-left"><strong><?= $txt_place_id; ?>:</strong></div>
<div class="modal-col-right"><?= $place_id; ?></div>
<div class="clear"></div>

<div class="modal-col-left"><strong><?= $txt_place_name; ?>:</strong></div>
<div class="modal-col-right"><?= $place_name; ?></div>
<div class="clear"></div>

<div class="modal-col-left"><strong><?= $txt_city; ?>:</strong></div>
<div class="modal-col-right"><?= $city_name; ?>, <?= $state_abbr; ?></div>
<div class="clear"></div>

<div class="modal-col-left"><strong><?= $txt_cat; ?>:</strong></div>
<div class="modal-col-right"><?= $cat_name; ?></div>
<div class="clear"></div>

<div class="modal-col-left"><strong><?= $txt_submission; ?>:</strong></div>
<div class="modal-col-right"><?= $submission_date; ?></div>
<div class="clear"></div>