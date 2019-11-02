<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?></title>
<meta name="description" content="<?= $txt_meta_desc; ?>" />
<?php require_once(__DIR__ . '/../../templates/_html_head.php'); ?>
<link rel="stylesheet" href="<?= $baseurl; ?>/plugins/custom_fields/styles.css">
</head>
<body class="tpl-advanced-search">

<?php require_once(__DIR__ . '/../../templates/_header.php'); ?>

<div class="wrapper">
	<h1><?= $txt_main_title; ?></h1>

	<div class="full-block">
		<form class="form-create-custom-field" method="get" action="<?= $baseurl; ?>/plugins/custom_fields/search.php">
			<div class="block">
				<label class="label" for="q"><?= $txt_label_keyword; ?></label><br>
				<input type="text" id="q" name="q" class="form-control">
			</div>

			<div class="block">
				<label for="city_id"><?= $txt_label_city; ?></label>
				<select id="city_id" name="city_id"></select>
			</div>

			<div class="block">
				<label class="label"><?= $txt_label_category; ?></label><br>

				<input type="checkbox" id="select_all" name="select_all"> <?= $txt_label_cat_all; ?><br>

				<?php
				// send bogus non empty array so that the show_cats() function returns checkboxes not checked
				$empty_arr = array('bogus');
				show_cats($cats_grouped_by_parent, 0, $empty_arr, 1);
				?>
			</div><!-- .block (show categories tree) -->

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
				<div class="block" id="li-field-<?= $field_id; ?>">

					<label><?= $field_name; ?></label>
					<?php
					if($field_type == 'radio' || $field_type == 'select' || $field_type == 'checkbox') {
						foreach($values_arr as $v) {
							$v = e(trim($v));
							?>
							<label class="label-inline">
								<input type="checkbox" name="field_<?= $field_id; ?>[]" value="<?= $v; ?>"> <?= $v; ?>
							</label>
							<?php
						}
					}

					if($field_type == 'text' || $field_type == 'multiline' || $field_type == 'url') {
						?>
						<input type="text" name="field_<?= $field_id; ?>">
						<?php
					}
					?>
				</div>
			<?php
			}
			?>

			<div class="block">
				<input type="submit" id="submit" name="submit" class="btn btn-blue">
			</div>
		</form>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/../../templates/_footer.php'); ?>

<script type="text/javascript">
$(document).ready(function() {
	// toggle categories checkboxes
	$('#select_all').click(function(e){
		var checkedStatus = this.checked;
		$('#cat-checkboxes').find(':checkbox').each(function() {
			$(this).prop('checked', checkedStatus);
		});
	});

	$('#city_id').select2({
	ajax: {
		url: '<?= $baseurl; ?>/_return_cities_select2.php',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				query: params.term, // search term
				page: params.page
			};
		}
	},
	escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	minimumInputLength: 1
});
});
</script>
</body>
</html>