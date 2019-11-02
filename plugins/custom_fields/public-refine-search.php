<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/translation.php');

// find global fields
$query = "SELECT f.*
			FROM custom_fields f
			LEFT JOIN rel_cat_custom_fields r
			ON f.field_id = r.field_id
			WHERE r.rel_id IS NULL AND f.searchable = 1 AND f.field_status = 1
			ORDER BY f.field_order DESC";
$stmt = $conn->prepare($query);
$stmt->execute();

$custom_fields = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = $row['field_id'];
	$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
	$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : '';
	$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
	$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
	$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';

	if(!empty($field_name) && !empty($field_type)) {
		$custom_fields[] = array(
			'field_id'    => $field_id,
			'field_name'  => $field_name,
			'field_type'  => $field_type,
			'values_list' => $values_list,
			'tooltip'     => $tooltip,
			'icon'        => $icon
		);
	}
}

// find fields for this cat
$query = "SELECT f.*
	FROM rel_cat_custom_fields r
	LEFT JOIN custom_fields f ON r.field_id = f.field_id
	WHERE r.cat_id = :cat_id AND f.searchable = 1 AND  field_status = 1
	ORDER BY f.field_order DESC";
$stmt = $conn->prepare($query);
$stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$field_id    = $row['field_id'];
	$field_name  = (!empty($row['field_name' ])) ? $row['field_name' ] : '';
	$field_type  = (!empty($row['field_type' ])) ? $row['field_type' ] : '';
	$values_list = (!empty($row['values_list'])) ? $row['values_list'] : '';
	$tooltip     = (!empty($row['tooltip'    ])) ? $row['tooltip'    ] : '';
	$icon        = (!empty($row['icon'       ])) ? $row['icon'       ] : '';

	$custom_fields[] = array(
		'field_id'    => $field_id,
		'field_name'  => $field_name,
		'field_type'  => $field_type,
		'values_list' => $values_list,
		'tooltip'     => $tooltip,
		'icon'        => $icon
	);
}
?>
<style>
#refine-search-wrapper {
	background: #fff;
	padding: 24px;
	margin-bottom: 6px;
	border-radius: 3px;
	border: 1px solid #ddd;
}

#refine-search ul {
	list-style-type: none;
	padding: 0;
	vertical-align: top;
}

#refine-search li {
	display: block;
	vertical-align: top;
	padding-bottom: 12px;
	margin-bottom: 12px;
}

#refine-search li label {
	display: inline-block;
	padding: 0;
	margin-right: 12px;
	font-weight: 400;
}

#refine-search .field-name {
	font-weight: 700;
	margin-bottom: 6px;
}

#refine-search-wrapper {
	display: none;
}

.advanced-search-link-wrapper {
	text-align: right;
}

.advanced-search-link {

}

.submit-search {
	text-align: right;
}
</style>
<div id="refine-search-wrapper">
	<form id="refine-search" method="get" action="<?= $baseurl; ?>/plugins/custom_fields/search.php">
		<input type="hidden" name="loc_type" value="<?= $loc_type; ?>">
		<input type="hidden" name="loc_id"   value="<?= $loc_id; ?>">
		<input type="hidden" name="cat_id"   value="<?= $cat_id; ?>">

		<ul>
			<?php
			foreach($custom_fields as $v) {
				$field_id    = $v['field_id'];
				$field_name  = $v['field_name'];
				$field_type  = $v['field_type'];
				$values_list = $v['values_list'];
				$tooltip     = $v['tooltip'];
				$icon        = $v['icon'];

				// explode values
				$values_arr = array();
				if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
					$values_arr = explode(';', $values_list);
				}

				?>
				<li id="li-field-<?= $field_id; ?>">
					<div class="field-name"><?= $field_name; ?></div>

					<?php
					if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
						foreach($values_arr as $v) {
							$v = e(trim($v));
							?>
							<label><input type="checkbox" name="field_<?= $field_id; ?>[]" value="<?= $v; ?>"> <?= $v; ?></label>
							<?php
						}
					}

					if($field_type == 'text' || $field_type == 'multiline' || $field_type == 'url') {
						?>
						<input type="text" name="field_<?= $field_id; ?>">
						<?php
					}
					?>
				</li>
			<?
			}
			?>
		</ul>
		<div class="submit-search">
			<input type="submit" id="submit-refine-search" name="submit" class="btn btn-blue">
		</div>
	</form>
</div><!-- #refine-search-wrapper -->
<div class="advanced-search-link-wrapper block">
	<a href="" class="advanced-search-link btn btn-default btn-even-less-padding"><?= $txt_advanced_search; ?>  <span id="caret" class="caret"></span></a>
</div>
<script>
$('.dropdown-menu').click(function(e) {
	e.stopPropagation();
});

$('.advanced-search-link').click(function(e) {
	e.preventDefault();
	$('#refine-search-wrapper').toggle(240);
	$('#caret').toggleClass('reversed');
});
</script>