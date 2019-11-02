<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-listings-trash.php');

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
$sort = !empty($frags[1]) ? $frags[1] : 'sort-date';

// get listings
// count how many
$query = "SELECT COUNT(*) AS total_rows FROM places WHERE status = 'trashed'";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
	$start = $pager->getStartRow();

	// sort, order by
	$orderby = "p.place_name";
	$where = "WHERE p.status = 'trashed'";

	if($sort == 'sort-date') {
		$orderby = "p.place_id DESC";
	}

	if($sort == 'sort-name') {
		$orderby = "p.place_name";
	}

	// the query
	$query = "SELECT
			p.place_id, p.place_name, p.submission_date, p.feat_home, p.status, p.paid,
			c.city_name, c.slug, c.state,
			rel.cat_id,
			cats.name AS cat_name,
			u.email,
			plans.plan_name
		FROM places p
			LEFT JOIN cities c ON p.city_id = c.city_id
			LEFT JOIN rel_place_cat rel ON rel.place_id = p.place_id
			LEFT JOIN cats ON rel.cat_id = cats.id
			LEFT JOIN users u ON u.id = p.userid
			LEFT JOIN plans ON plans.plan_id = p.plan
		$where
		GROUP BY p.place_id
		ORDER BY $orderby LIMIT :start, :limit";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();

	$places_arr = array();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$place_id        = $row['place_id'];
		$place_name      = $row['place_name'];
		$city_name       = $row['city_name'];
		$city_slug       = $row['slug'];
		$state_abbr      = $row['state'];
		$cat_name        = $row['cat_name'];
		$submission_date = $row['submission_date'];
		$feat_home       = $row['feat_home'];
		$status          = $row['status'];
		$paid            = $row['paid'];
		$user_email      = $row['email'];
		$plan_name       = $row['plan_name'];

		// sanitize
		$place_name = e($place_name);
		$place_slug = to_slug($place_name);

		// simplify date
		$submission_date = strtotime($submission_date);
		$date_formatted  = date( 'Y-m-d', $submission_date );

		// link to each place
		$link_url = $baseurl . '/' . $city_slug . '/place/' . $place_id . '/' . $place_slug;

		$cur_loop_arr = array(
			'place_id'       => $place_id,
			'place_name'     => $place_name,
			'place_slug'     => $place_slug,
			'link_url'       => $link_url,
			'city_name'      => $city_name,
			'city_slug'      => $city_slug,
			'state_abbr'     => $state_abbr,
			'cat_name'       => $cat_name,
			'date_formatted' => $date_formatted,
			'feat_home'      => $feat_home,
			'status'         => $status,
			'paid'           => $paid,
			'user_email'     => $user_email,
			'plan_name'      => $plan_name
		);

		$places_arr[] = $cur_loop_arr;
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
.editable-container.editable-inline {
	width: 100%;
}

@media only screen and (min-width: 768px) {
	.form-inline .form-control {
		width: 100%;
	}
}

.control-group.form-group {
	width: 100%;
}

.editable-input {
	width: 80%;
}

.x-editable-textarea {
	width: 600px;
}

.editable-click, a.editable-click, a.editable-click:hover {
	text-decoration: none;
	border-bottom: 0;
}
</style>
</head>
<body class="admin-listings">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><i class="fa fa-trash" aria-hidden="true"></i> <?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<strong><?= $txt_sort; ?>:</strong><br>
				<a href="<?= $baseurl; ?>/admin/admin-listings-trashed/sort-name/" class="btn btn-default btn-less-padding"><?= $txt_by_name; ?></a>
				<a href="<?= $baseurl; ?>/admin/admin-listings-trashed" class="btn btn-default btn-less-padding"><?= $txt_by_date; ?></a>
			</div>

			<?php
			if($total_rows > 0) {
				?>
				<div class="pull-left"><span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span></div>
				<div class="pull-right"><a href="#" class="empty-trash" data-toggle="modal" data-target="#empty-trash-modal"><?= $txt_empty; ?></a></div>

				<div class="clearfix"></div>
				<div class="table-responsive">
					<table class="table admin-table table-striped">
						<tr>
							<th><?= $txt_id; ?></th>
							<th><?= $txt_place_name; ?></th>
							<th><?= $txt_city; ?></th>
							<th><?= $txt_date; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($places_arr as $k => $v) {
							$place_id       = $v['place_id'];
							$place_name     = $v['place_name'];
							$place_slug     = $v['place_slug'];
							$link_url       = $v['link_url'];
							$city_name      = $v['city_name'];
							$city_slug      = $v['city_slug'];
							$state_abbr     = $v['state_abbr'];
							$cat_name       = $v['cat_name'];
							$date_formatted = $v['date_formatted'];
							$feat_home      = $v['feat_home'];
							$status         = $v['status'];
							$paid           = $v['paid'];
							$user_email     = $v['user_email'];
							$plan_name      = $v['plan_name'];
							?>
							<tr id="tr-place-id=<?= $place_id; ?>">
								<td><?= $place_id; ?></td>
								<td><a href="<?= $link_url; ?>" target="_blank"><?= $place_name; ?></a></td>
								<td class="nowrap">
									<?php echo (!empty($city_name)) ? "$city_name, $state_abbr" : ''; ?>
								</td>
								<td class="nowrap"><?= $date_formatted; ?></td>
								<td class="nowrap">
									<!-- expand btn -->
									<span data-toggle="tooltip" title="<?= $txt_tooltip_expand; ?>">
										<a href="#" class="btn btn-default btn-less-padding expand-details"
											data-place-id="<?= $place_id; ?>">
											&nbsp;<i class="fa fa-expand" aria-hidden="true"></i>&nbsp;
										</a>
									</span>

									<!-- restore btn -->
									<span data-toggle="tooltip" title="<?= $txt_tooltip_restore; ?>">
										<a href="#" class="btn btn-default btn-less-padding restore-place"
											data-place-id="<?= $place_id; ?>">
											&nbsp;<i class="fa fa-undo" aria-hidden="true"></i>&nbsp;
										</a>
									</span>

									<!-- remove btn -->
									<span data-toggle="tooltip"	title="<?= $txt_tooltip_remove; ?>">
										<a href="#" class="btn btn-default btn-less-padding"
											data-toggle="modal"
											data-target="#remove-place-modal"
											data-place-id="<?= $place_id; ?>">
											&nbsp;<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
										</a>
									</span>
								</td>
							</tr>
							<tr id="expand-details-<?= $place_id; ?>" class="details-row">
								<td colspan="5" class="wrap">
									<div class="details-block">
										<div class="">
											<strong><?= $txt_listing_owner; ?>:</strong>
											<?= $user_email; ?>
										</div>

										<div class="">
											<strong><?= $txt_city; ?>:</strong>
											<?php
											echo (!empty($city_name)) ? "$city_name, $state_abbr" : '';
											?>
										</div>

										<div class="">
											<strong><?= $txt_plan_name; ?>:</strong>
											<?= $plan_name; ?>
										</div>
										<div class="">
											<strong><?= $txt_cat; ?>:</strong>
											<?= $cat_name; ?>
										</div>
									</div>
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
								$page_url = "$baseurl/admin/admin-listings/$sort/page/";

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
			} // end if($total_rows > 0)
			else {
				?>
				<?= $txt_no_results; ?>
				<?php
			}
			?>

	</div><!-- .padding -->
	</div><!-- .main-container -->
	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<!-- Remove Place Modal -->
<div class="modal fade" id="remove-place-modal" tabindex="-1" role="dialog" aria-labelledby="Remove Place Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $txt_remove_perm; ?></h4>
			</div>
			<div class="modal-body">
				<?= $txt_remove_perm_sure; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></button>
				<button type="button" class="btn btn-blue btn-less-padding remove-place" data-dismiss="modal"><?= $txt_remove_perm; ?></button>
			</div>
		</div>
	</div>
</div>

<!-- Empty Trash Modal -->
<div class="modal fade" id="empty-trash-modal" tabindex="-1" role="dialog" aria-labelledby="Empty Trash Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $txt_remove_perm; ?></h4>
			</div>
			<div class="modal-body">
				<?= $txt_remove_perm_sure_all; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></button>
				<button type="button" class="btn btn-blue btn-less-padding empty-trash-confirm" data-dismiss="modal"><?= $txt_empty; ?></button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// hide all details
	$('.details-row').hide();

	// expand details
	$('.expand-details').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('place-id');
		$('#expand-details-' + place_id).toggle();

	});

	// restore listing
    $('.restore-place').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-restore-place.php';
		var place_id = $(this).data('place-id');

		$.post(post_url, { place_id: place_id },
			function(data) {
				location.reload(true);
			}
		);
    });

	// when remove place modal pops up
	$('#remove-place-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var place_id = button.data('place-id'); // Extract info from data-* attributes
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-get-place.php';

		$.post(post_url, { place_id: place_id },
			function(data) {
				modal.find('.modal-body').html(data).fadeIn();
				modal.find('.remove-place').attr('data-place-id', place_id);
			}
		);
	});

	// remove place button in modal clicked
    $('.remove-place').click(function(e){
		e.preventDefault();
		var modal = $('#remove-place-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-place-perm.php';
		var clicked_button = $(this);
		var place_id = clicked_button.data('place-id');
		console.log('place id is ' + place_id);

		$.post(post_url, { place_id: place_id },
			function(data) {
				console.log(data);
				modal.find('.modal-body').empty();
				modal.find('.modal-body').html(data).fadeIn();
			}
		);
    });

	// after removing and clicking the close button on the modal, reload
	$('#remove-place-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});

	// when empty trash modal pops up
	$('#empty-trash-modal').on('show.bs.modal', function(event) {
		// do nothing for now
	});

	// empty all button in modal clicked
    $('.empty-trash-confirm').click(function(event){
		event.preventDefault();
		var modal = $('#empty-trash-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-empty-trash-listings.php';
		var clicked_button = $(this);

		$.post(post_url, {},
			function(data) {
				modal.find('#empty-trash-modal .modal-body').empty();
				modal.find('#empty-trash-modal .modal-body').html(data).fadeIn();
			}
		);
    });

	// after emptying all and clicking the close button on the modal, reload
	$('#empty-trash-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});

});
</script>
</body>
</html>