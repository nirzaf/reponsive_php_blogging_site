<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-cats.php');

// path info
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}

// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}

$frags = explode("/", $frags);

// paging vars
$page = !empty($frags[3]) ? $frags[3] : 1;
$limit = $items_per_page;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// sort order
$sort = !empty($frags[1]) ? $frags[1] : 'sort-name';

// count how many cats
$query = "SELECT COUNT(*) AS total_rows FROM cats WHERE cat_status = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

// initialize cats array
$cats_arr = array();

if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
	$start = $pager->getStartRow();

	// select all cats information and put in an array
	if($sort == 'sort-name') {
		$query = "SELECT * FROM cats WHERE cat_status = 1 ORDER BY name LIMIT :start, :limit";
	}
	if($sort == 'sort-parent') {
		$query = "SELECT * FROM cats WHERE cat_status = 1 ORDER BY parent_id LIMIT :start, :limit";
	}

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cat_id          = $row['id'];
		$cat_name        = $row['name'];
		$cat_plural_name = $row['plural_name'];
		$cat_parent_id   = $row['parent_id'];
		$cat_order       = $row['cat_order'];

		// sanitize
		$cat_name        = e(trim($cat_name));
		$cat_plural_name = e(trim($cat_plural_name));
		$cat_parent_id   = (!empty($cat_parent_id)) ? $cat_parent_id : 0;

		$cur_loop_arr = array(
			'cat_id'          => $cat_id,
			'cat_name'        => $cat_name,
			'cat_plural_name' => $cat_plural_name,
			'cat_parent_id'   => $cat_parent_id,
			'cat_order'       => $cat_order
		);

		$cats_arr[] = $cur_loop_arr;
	}
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="admin-cats">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
			<strong><?= $txt_sort; ?>:</strong><br>
			<a href="<?= $baseurl; ?>/admin/admin-cats/sort-name/" class="btn btn-default btn-less-padding"><?= $txt_by_name; ?></a>
			<a href="<?= $baseurl; ?>/admin/admin-cats/sort-parent/" class="btn btn-default btn-less-padding"><?= $txt_by_parent_id; ?></a>
			</div>

			<div class="block">
				<strong><?= $txt_action; ?>:</strong><br>
				<a href="" class="create-cat-btn btn btn-blue btn-less-padding"
					data-loc-type="city"
					data-modal-title="<?= $txt_create_cat; ?>"
					data-toggle="modal"
					data-target="#create-cat-modal"
					><?= $txt_create_cat; ?></a>
			</div>

			<?php
			if(!empty($cats_arr)) {
				?>
				<span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span>
				<div class="table-responsive">
					<table class="table admin-table table-striped">
						<tr>
							<th><?= $txt_id; ?></th>
							<th><?= $txt_name; ?></th>
							<th><?= $txt_parent_id; ?></th>
							<th><?= $txt_order; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($cats_arr as $k => $v) {
							$cat_id        = $v['cat_id'];
							$cat_name      = $v['cat_name'];
							$cat_parent_id = $v['cat_parent_id'];
							$cat_order     = $v['cat_order'];
							?>
							<tr id="cat-<?= $cat_id; ?>">
								<td><?= $cat_id; ?></td>
								<td class="nowrap">
									<?= $cat_name; ?>
								</td>
								<td class="nowrap">
									<?= $cat_parent_id; ?>
								</td>
								<td class="nowrap">
									<?= $cat_order; ?>
								</td>
								<td class="nowrap">
									<span id="edit-cat-<?= $cat_id; ?>" data-toggle="tooltip" title="<?= $txt_edit_cat; ?>">
										<a href="#" class="btn btn-default btn-less-padding edit-cat-btn"
											data-cat-id="<?= $cat_id; ?>"
											data-toggle="modal"
											data-target="#edit-cat-modal">
											<i class="fa fa-pencil"></i>
										</a>
									</span>

									<span data-toggle="tooltip"	title="<?= $txt_remove_cat; ?>">
										<a href="" class="btn btn-less-padding btn-default remove-cat"
											data-cat-id="<?= $cat_id; ?>">
											<i class="fa fa-trash"></i>
										</a>
									</span>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>

				<nav>
					<ul class="pagination">
						<?php
						if(isset($pager) && $pager->getTotalPages() > 1) {
							$curPage = $page;

							$startPage = ($curPage < 5)? 1 : $curPage - 4;
							$endPage = 8 + $startPage;
							$endPage = ($pager->getTotalPages() < $endPage) ? $pager->getTotalPages() : $endPage;
							$diff = $startPage - $endPage + 8;
							$startPage -= ($startPage - $diff > 0) ? $diff : 0;

							$startPage = ($startPage == 1) ? 2 : $startPage;
							$endPage = ($endPage == $pager->getTotalPages()) ? $endPage - 1 : $endPage;

							if($total_rows > 0) {
								$page_url = "$baseurl/admin/admin-cats/$sort/page/";

								if ($curPage > 1) {
									?>
									<li><a href="<?= $page_url; ?>1"><?= $txt_pager_page1; ?></a></li>
									<?php
								}
								if ($curPage > 6) {
									?>
									<li><span>...</span></li>
									<?php
								}
								if ($curPage == 1) {
									?>
									<li class="active"><span><?= $txt_pager_page1; ?></span></li>
									<?php
								}
								for($i = $startPage; $i <= $endPage; $i++) {
									if($i == $page) {
										?>
										<li class="active"><span><?= $i; ?></span></li>
										<?php
									}
									else {
										?>
										<li><a href="<?php echo $page_url, $i; ?>"><?= $i; ?></a></li>
										<?php
									}
								}

								if($curPage + 5 < $pager->getTotalPages()) {
									?>
									<li><span>...</span></li>
									<?php
								}
								if($pager->getTotalPages() > 5) {
									$last_page_txt = $txt_pager_last_page;
								}

								$last_page_txt = ($pager->getTotalPages() > 5) ? $txt_pager_last_page : $pager->getTotalPages();

								if($curPage == $pager->getTotalPages()) {
									?>
									<li class="active"><span><?= $last_page_txt; ?></span></li>
									<?php
								}
								else {
									?>
									<li><a href="<?php echo $page_url, $pager->getTotalPages(); ?>"><?= $last_page_txt; ?></a></li>
									<?php
								}
							} //  end if($total_rows > 0)
						} //  end if(isset($pager) && $pager->getTotalPages() > 1)
						if(isset($pager) && $pager->getTotalPages() == 1) {
							?>

							<?php
						}
						?>
					</ul>
				</nav>
			<?php
			} // end if(!empty($cats_arr))
			else {
				?>
				<p><?= $txt_no_cats; ?></p>
				<?php
			}
			?>

		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- modal edit category -->
<div class="modal fade" id="edit-cat-modal" tabindex="-1" role="dialog" aria-labelledby="Edit Cat Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_edit_cat; ?></h3>
			</div>
			<div class="modal-body">

			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-blue btn-less-padding" type="submit" id="edit-cat-submit">
				<a href="#" class="btn btn-default btn-less-padding" id="modal-cancel-edit" data-dismiss="modal">
					<?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end edit cat modal -->

<!-- modal create category -->
<div class="modal fade" id="create-cat-modal" tabindex="-1" role="dialog" aria-labelledby="Create Category Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title" id="modal-title"><?= $txt_create_cat; ?></h3>
			</div>
			<div class="modal-body">
				<form class="form-create-cat" method="post">
					<div class="block">
						<label class="label" for="cat_name"><?= $txt_cat_name; ?></label><br>
						<input type="text" id="cat_name" name="cat_name" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="plural_name"><?= $txt_plural_name; ?></label><br>
						<input type="text" id="plural_name" name="plural_name" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="iconfont_tag"><?= $txt_iconfont_tag; ?></label><br>
						<input type="text" id="iconfont_tag" name="iconfont_tag" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="cat_order"><?= $txt_cat_order; ?></label><br>
						<input type="text" id="cat_order" name="cat_order" class="form-control" required>
					</div>

					<div class="block">
						<label class="label" for="cat_parent"><?= $txt_parent_cat; ?></label><br>
						<?= $txt_parent_explain; ?><br>
						<select id="cat_parent" name="cat_parent" class="form-control">
							<option value="0"><?= $txt_no_parent; ?></option>
							<?php
							// select only first 2 levels (parent = o or parent whose parent = 0)
							$modal_cats_arr = array();
							$level_0_ids = array();

							$query = "SELECT * FROM cats WHERE parent_id = 0 AND cat_status = 1 ORDER BY name";
							$stmt = $conn->prepare($query);
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

							if(!empty($in)) {
								$query = "SELECT * FROM cats WHERE parent_id IN($in) AND cat_status = 1 ORDER BY name";
								$stmt = $conn->prepare($query);
								$stmt->execute();

								while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$modal_cats_arr[] = array('id' => $row['id'], 'name' => $row['name']);
								}

								function cmp($a, $b) {
									return strcasecmp ($a['name'], $b['name']);
								}
								usort($modal_cats_arr, 'cmp');

								foreach($modal_cats_arr as $k => $v) {
									?>
									<option value="<?= $v['id']; ?>"><?= $v['name']; ?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
				</form>
			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-less-padding btn-blue" type="submit" id="create-cat-submit">
				<a href="#" class="btn btn-less-padding btn-default" id="modal-cancel-create" data-dismiss="modal">
					<?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end create cat modal -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<!-- javascript -->
<script src="<?= $baseurl; ?>/lib/jinplace/jinplace.min.js"></script>
<script>
$(document).ready(function(){
	// show edit cat modal
	$('#edit-cat-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var cat_id = button.data('cat-id'); // Extract info from data-* attributes
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-get-cat.php';

		$.post(post_url, { cat_id: cat_id },
			function(data) {
				modal.find('.modal-body').html(data);
			}
		);
	});

	// edit cat form submit
    $('#edit-cat-submit').click(function(e){
		e.preventDefault();
		var modal = $('#edit-cat-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-edit-cat.php';

		$.post(post_url, {
			params: $('form.form-edit-cat').serialize(),
			},
			function(data) {
				modal.find('.modal-body').html(data);
				modal.find('#edit-cat-submit').remove();
				modal.find('#modal-cancel-edit').empty().text('OK');
			}
		);
    });

	// edit cat modal on close
	$('#edit-cat-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});

	// remove category
	$('.remove-cat').click(function(e) {
		e.preventDefault();
		var cat_id = $(this).data('cat-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-cat.php';
		var wrapper = '#cat-' + cat_id;
		$.post(post_url, {
			cat_id: cat_id
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
				}
			}
		);
	});

	// create cat form submit
    $('#create-cat-submit').click(function(e){
		e.preventDefault();
		var modal = $('#create-cat-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-create-cat.php';

		$.post(post_url, {
			params: $('form.form-create-cat').serialize(),
			},
			function(data) {
				modal.find('.modal-body').html(data);
				modal.find('#create-cat-submit').remove();
				modal.find('#modal-cancel-create').empty().text('OK');
			}
		);
    });

	// create cat modal on close
	$('#create-cat-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});

	// initialize edit in place
	$('.editable').jinplace();
});
</script>
</body>
</html>