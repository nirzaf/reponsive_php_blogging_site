<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php');
require_once($lang_folder . '/admin_translations/trans-get-cat.php');

// csrf check
require_once(__DIR__ . '/_admin_inc_request_with_ajax.php');

$cat_id = $_POST['cat_id'];

$query = "SELECT * FROM cats WHERE id = :cat_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':cat_id', $cat_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$id           = (!empty($row['id']          )) ? $row['id']           : '';
$name         = (!empty($row['name']        )) ? $row['name']         : '';
$plural_name  = (!empty($row['plural_name'] )) ? $row['plural_name']  : '';
$parent_id    = (!empty($row['parent_id']   )) ? $row['parent_id']    : '';
$iconfont_tag = (!empty($row['iconfont_tag'])) ? $row['iconfont_tag'] : '';
$cat_order    = (!empty($row['cat_order']   )) ? $row['cat_order']    : '';

// sanitize
$name          = e(trim($name        ));
$plural_name   = e(trim($plural_name ));
$iconfont_tag  = e(trim($iconfont_tag));
?>
<form class="form-edit-cat" method="post">
	<input type="hidden" name="cat_id" value="<?= $id; ?>">

	<div class="block">
		<label class="label" for="cat_name"><?= $txt_cat_name; ?></label><br>
		<input type="text" id="cat_name" name="cat_name" class="form-control" value="<?= $name; ?>" required>
	</div>

	<div class="block">
		<label class="label" for="plural_name"><?= $txt_plural_name; ?></label><br>
		<input type="text" id="plural_name" name="plural_name" class="form-control" value="<?= $plural_name; ?>" required>
	</div>

	<div class="block">
		<label class="label" for="icon_filename"><?= $txt_iconfont_tag; ?></label><br>
		<input type="text" id="iconfont_tag" name="iconfont_tag" class="form-control" value="<?= $iconfont_tag; ?>" required>
	</div>

	<div class="block">
		<label class="label" for="cat_order"><?= $txt_cat_order; ?></label><br>
		<input type="text" id="cat_order" name="cat_order" class="form-control" value="<?= $cat_order; ?>" required>
	</div>

	<div class="block">
		<label class="label" for="cat_parent"><?= $txt_parent_cat; ?></label><br>
		<?= $txt_parent_explain; ?><br>
		<select id="cat_parent" name="cat_parent" class="form-control">
			<option value="0"><?= $txt_no_parent; ?></option>
			<?php
			// select only first 2 levels (parent = 0 or parent whose parent = 0)
			$modal_cats_arr = array();
			$level_0_ids    = array();

			$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id = 0 AND id != :id ORDER BY name";
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':id', $id);
			$stmt->execute();

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$cur_loop_array = array( 'id' => $row['id'], 'name' => $row['name'] );
				$modal_cats_arr[] = $cur_loop_array;
				$level_0_ids[] = $row['id'];
			}

			$in = '';
			foreach($level_0_ids as $k => $v) {
				if($k != 0) {
					$in .= ',';
				}
				$in .= "$v";
			}

			$query = "SELECT * FROM cats WHERE cat_status = 1 AND parent_id IN($in) ORDER BY name";
			$stmt = $conn->prepare($query);
			$stmt->execute();

			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$modal_cats_arr[] = array('id' => $row['id'], 'name' => $row['name']);
			}

			function cmp($a, $b) {
				return strcasecmp ($a['name'], $b['name']);
			}
			usort($modal_cats_arr, 'cmp');

			$selected = '';
			foreach($modal_cats_arr as $k => $v) {
				if($v['id'] == $parent_id) {
					$selected = 'selected';
				}
				else {
					$selected = '';
				}
				?>
				<option value="<?= $v['id']; ?>" <?= $selected; ?>><?= $v['name']; ?></option>
				<?php
			}
			?>
		</select>
	</div>
</form>