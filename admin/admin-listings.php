<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-listings.php');

// path info
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
} else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}

// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}

// explode frags string
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
if(empty($_GET['s'])) {
	$query = "SELECT COUNT(*) AS total_rows FROM places WHERE status <> 'trashed'";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows = $row['total_rows'];

	if($total_rows > 0) {
		$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
		$start = $pager->getStartRow();

		// sort, order by
		$orderby = "p.place_name";
		$where = "WHERE p.status <> 'trashed'";

		if($sort == 'sort-date') {
			$orderby = "p.place_id DESC";
		}

		if($sort == 'sort-name') {
			$orderby = "p.place_name";
		}

		if($sort == 'find') {
			$orderby = "p.place_name";
			$where = "WHERE p.place_id = " . (int)$frags[2];

			// first count
			$query = "SELECT COUNT(*) AS total_rows FROM places WHERE place_id = " . (int)$frags[2];
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$total_rows = $row['total_rows'];
		}

		// the query
		$query = "SELECT
				p.place_id, p.place_name, p.submission_date, p.feat_home, p.status, p.paid, p.userid,
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
			$place_owner     = $row['userid'];
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
			$user_email = e($user_email);
			$place_slug = to_slug($place_name);

			// simplify date
			$submission_date = strtotime($submission_date);
			$date_formatted  = date( 'Y-m-d', $submission_date );

			// link to each place
			$link_url = $baseurl . '/' . $city_slug . '/place/' . $place_id . '/' . $place_slug;

			$cur_loop_arr = array(
				'place_id'       => $place_id,
				'place_name'     => $place_name,
				'place_owner'    => $place_owner,
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
} // end if(empty($_GET['s']))
else {
	$s    = $_GET['s'];
	$page = (!empty($_GET['page'])) ? $_GET['page'] : 1;

	$query = "SELECT COUNT(*) AS total_rows FROM places WHERE MATCH(place_name, description) AGAINST (:s) AND status <> 'trashed'";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':s', $s);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows = $row['total_rows'];

	if($total_rows > 0) {
		$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
		$start = $pager->getStartRow();

		// the query
		$query = "SELECT
				p.place_id, p.place_name, p.submission_date, p.feat_home, p.status, p.paid, p.userid,
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
			WHERE MATCH(place_name, description) AGAINST (:s) AND p.status <> 'trashed'
			GROUP BY p.place_id
			ORDER BY p.place_id DESC
			LIMIT :start, :limit";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':s', $s);
		$stmt->bindValue(':start', $start);
		$stmt->bindValue(':limit', $limit);
		$stmt->execute();

		$places_arr = array();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$place_id        = $row['place_id'];
			$place_name      = $row['place_name'];
			$place_owner     = $row['userid'];
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
			$user_email = e($user_email);
			$place_slug = to_slug($place_name);

			// simplify date
			$submission_date = strtotime($submission_date);
			$date_formatted  = date( 'Y-m-d', $submission_date );

			// link to each place
			$link_url = $baseurl . '/' . $city_slug . '/place/' . $place_id . '/' . $place_slug;

			$cur_loop_arr = array(
				'place_id'       => $place_id,
				'place_name'     => $place_name,
				'place_owner'    => $place_owner,
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
} // end else

// get array of ids for this render (not being used now, just for future updates)
if(!empty($places_arr)) {
	$places_ids = array();
	foreach($places_arr as $k => $v) {
		$places_ids[] = $v['place_id'];
	}

	// build $in var
	$in = '';
	$i = 0;
	foreach($places_arr as $k => $v) {
		if($i != 0) {
			$in .= ',';
			$in .= $v['place_id'];
		} else {
			$in .= $v['place_id'];
		}
		$i++;
	}

	// get custom slugs
	$slugs_arr = array();

	/* this is reserved for future versions
	$query = "SELECT place_id, slug FROM meta_data WHERE place_id IN ($in)";
	$stmt = $conn->prepare($query);
	$stmt->bindValue(':place_id', $place_id);
	$stmt->execute();



	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$place_id   = (!empty($row['place_id'])) ? $row['place_id'] : '';
		$place_slug = (!empty($row['slug']))     ? $row['slug']     : '';
		$slugs_arr[$place_id] = $place_slug;
	}
	*/
}

// translation var check if exists, if not, set default
// v. 1.09
$txt_transfer_owner = (!empty($txt_transfer_owner)) ? $txt_transfer_owner : "Transfer Listing to User ID: ";
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
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<form class="form-search-place" action="admin-listings">
				<div class="block">
					<input type="text" id="s" name="s" class="form-control width-25" required>
					<button type="button" id="search" name="search" class="btn btn-blue btn-less-padding btn-form-control" style="padding: 4px 8px;"><?= $txt_find; ?></button>
				</div>
			</form>

			<?php
			if(!isset($_GET['s'])) {
				?>
				<div class="block">
					<strong><?= $txt_sort; ?>:</strong><br>
					<a href="<?= $baseurl; ?>/admin/admin-listings/sort-name/" class="btn btn-default btn-less-padding"><?= $txt_by_name; ?></a>
					<a href="<?= $baseurl; ?>/admin/admin-listings" class="btn btn-default btn-less-padding"><?= $txt_by_date; ?></a>
				</div>
			<?php
			} else {
				?>
				<p>Search results for <em>'<?= $_GET['s']; ?>'</em></p>
				<?php
			}
			?>

			<div class="pull-left"><span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span></div>
			<div class="pull-right"><a href="<?= $baseurl; ?>/admin/admin-listings-trash"><?= $txt_trash; ?></a></div>
			<div class="clearfix"></div>

			<?php
			if($total_rows > 0) {
				?>

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
							$place_owner    = $v['place_owner'];
							?>
							<tr id="tr-place-id-<?= $place_id; ?>">
								<td><?= $place_id; ?></td>

								<td><a href="<?= $baseurl; ?>/<?= $city_slug; ?>/place/<?= $place_id; ?>/<?php
									if (array_key_exists($place_id, $slugs_arr)) {
										echo $slugs_arr[$place_id];
									} else {
										echo to_slug($place_name);
									}
								?>" title="<?= $place_name; ?>"><?= $place_name; ?></a></td>

								<td class="nowrap">
									<?php echo (!empty($city_name)) ? "$city_name, $state_abbr" : ''; ?>
								</td>
								<td class="nowrap"><?= $date_formatted; ?></td>
								<td class="nowrap">
									<!-- status btn -->
									<?php
									if($status == 'pending') {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_approved; ?>">
											<a href="#" class="btn btn-default btn-less-padding approve-place"
												id="status-place-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-status="pending">
												<i class="fa fa-toggle-off" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_approved; ?>">
											<a href="#" class="btn btn-green btn-less-padding approve-place"
												id="status-place-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-status="approved">
												<i class="fa fa-toggle-on" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									?>

									<!-- paid btn -->
									<?php
									if($paid == 0) {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_paid; ?>">
											<a href="#" class="btn btn-default btn-less-padding paid-place"
												id="paid-place-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-paid="unpaid">
												&nbsp;<i class="fa fa-usd" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_paid; ?>">
											<a href="#" class="btn btn-green btn-less-padding paid-place"
												id="paid-place-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-paid="paid">
												&nbsp;<i class="fa fa-usd" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									?>

									<!-- featured_home toggle -->
									<?php
									if($feat_home == 0) {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_featured; ?>">
											<a href="#" class="btn btn-default btn-less-padding featured-home"
												id="featured-home-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-featured-home="not_featured">
												&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_featured; ?>">
											<a href="#" class="btn btn-green btn-less-padding featured-home"
												id="featured-home-<?= $place_id; ?>"
												data-place-id="<?= $place_id; ?>"
												data-featured-home="featured">
												&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									?>

									<!-- edit btn -->
									<span data-toggle="tooltip"	title="<?= $txt_edit_place; ?>">
										<a href="<?= $baseurl; ?>/user/edit-place/<?= $place_id; ?>"
											class="btn btn-default btn-less-padding edit-place"
											data-id="<?= $place_id; ?>">
											&nbsp;<i class="fa fa-pencil" aria-hidden="true"></i>&nbsp;
										</a>
									</span>

									<!-- expand btn -->
									<span data-toggle="tooltip" title="<?= $txt_tooltip_expand; ?>">
										<a href="#" class="btn btn-default btn-less-padding expand-details"
											data-place-id="<?= $place_id; ?>">
											&nbsp;<i class="fa fa-expand" aria-hidden="true"></i>&nbsp;
										</a>
									</span>

									<!-- remove btn -->
									<span data-toggle="tooltip"	title="<?= $txt_remove_place; ?>">
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
											<span class="owner-email"><?= $user_email; ?></span>

											<strong><?= $txt_transfer_owner; ?></strong>

											<span class="btn btn-less-padding btn-default" id="activator-owner-<?= $place_id; ?>">
												<i class="fa fa-pencil"></i>
											</span>
											<div class="editable"
												data-url="<?= $baseurl; ?>/admin/admin-process-edit-owner.php"
												data-activator="#activator-owner-<?= $place_id; ?>"
												data-attribute="owner"
												data-object="<?= $place_id; ?>">
												<?= $place_owner; ?>
											</div>
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

				<?php
				// pagination for regular listings
				if(empty($_GET['s'])) {
					?>
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
				// pagination for search query
				} else {
					?>
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
									$page_url = "$baseurl/admin/admin-listings?s=$s&page=";

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
				}
				?>
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
				<h4 class="modal-title" id="myModalLabel"><?= $txt_remove_place; ?></h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></button>
				<button type="button" class="btn btn-blue btn-less-padding remove-place" data-dismiss="modal" data-remove-id><?= $txt_remove_place; ?></button>
			</div>
		</div>
	</div>
</div>

<script src="<?= $baseurl; ?>/lib/jinplace/jinplace.min.js"></script>
<script>
$(document).ready(function(){
	// hide all details
	$('.details-row').hide();

	// search by place id
	$('#submit_place_id').click(function() {
		var place_id = $('#place_id').val();
		window.location.href = '<?= $baseurl; ?>/admin/admin-listings/find/' + place_id;
	});

	// search by keyword
	$('#search').click(function() {
		var search = $('#s').val();
		window.location.href = '<?= $baseurl; ?>/admin/admin-listings?s=' + search;
	});

	// show remove place modal
	$('#remove-place-modal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var place_id = button.data('place-id'); // Extract info from data-* attributes
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-get-place.php';

		$.post(post_url, { place_id: place_id },
			function(data) {
				modal.find('.modal-body').html(data).fadeIn();
				modal.find('.remove-place').attr('data-remove-id', place_id);
			}
		);
	});

	// remove place button in modal clicked
    $('.remove-place').click(function(e){
		e.preventDefault();
		var modal = $('#remove-place-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-place.php';
		var button = $('.remove-place'); // Button that was clicked
		var place_id = button.data('remove-id');
		var row_id = '#tr-place-id-' + place_id;
		//$('#tr-place-id-' + place_id).remove();

		/*
		$.post(post_url, { place_id: place_id },
			function(data) {
				modal.find('.modal-body').empty();
				modal.find('.modal-body').html(data).fadeIn();
			}
		);
		*/

		$.post(post_url, { place_id: place_id })
			.done(function(data) {
				modal.find('.modal-body').empty();
				modal.find('.modal-body').html(data).fadeIn();
				location.reload(true);
			});
    });

	// after removing and clicking the close button on the modal, reload
	$('#remove-place-modal').on('hide.bs.modal', function (event) {
		//location.reload(true);
	});

	// toggle place status
	$('.approve-place').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('place-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-approve-place.php';
		var status = $(this).data('status');

		$.post(post_url, {
			place_id: place_id,
			status: status
			},
			function(data) {
				if(data == 'approved') {
					$('#status-place-' + place_id).removeClass('btn-default');
					$('#status-place-' + place_id).addClass('btn-green');
					$('#status-place-' + place_id + ' i').removeClass('fa-toggle-off');
					$('#status-place-' + place_id + ' i').addClass('fa-toggle-on');
					$('#status-place-' + place_id).data('status', 'approved');
				}
				if(data == 'pending') {
					$('#status-place-' + place_id).removeClass('btn-green');
					$('#status-place-' + place_id).addClass('btn-default');
					$('#status-place-' + place_id + ' i').removeClass('fa-toggle-on');
					$('#status-place-' + place_id + ' i').addClass('fa-toggle-off');
					$('#status-place-' + place_id).data('status', 'pending');
				}
			}
		);
	});

	// paid place switch
	$('.paid-place').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('place-id');
		var paid = $(this).data('paid');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-toggle-paid.php';

		$.post(post_url, {
			place_id: place_id,
			paid: paid
			},
			function(data) {
				if(data == 'unpaid') {
					$('#paid-place-' + place_id).removeClass('btn-green');
					$('#paid-place-' + place_id).addClass('btn-default');
					$('#paid-place-' + place_id).data('paid', 'unpaid');
				}
				if(data == 'paid') {
					$('#paid-place-' + place_id).removeClass('btn-default');
					$('#paid-place-' + place_id).addClass('btn-green');
					$('#paid-place-' + place_id).data('paid', 'paid');
				}
			}
		);
	});

	// featured home switch
	$('.featured-home').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('place-id');
		var featured_home = $(this).data('featured-home');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-toggle-featured-home.php';

		$.post(post_url, {
			place_id: place_id,
			featured_home: featured_home
			},
			function(data) {
				if(data == 'not_featured') {
					$('#featured-home-' + place_id).removeClass('btn-green');
					$('#featured-home-' + place_id).addClass('btn-default');
					$('#featured-home-' + place_id).data('featured-home', 'not_featured');
				}
				if(data == 'featured') {
					$('#featured-home-' + place_id).removeClass('btn-default');
					$('#featured-home-' + place_id).addClass('btn-green');
					$('#featured-home-' + place_id).data('featured-home', 'featured');
				}
			}
		);
	});

	// expand details
	$('.expand-details').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('place-id');
		$('#expand-details-' + place_id).toggle();

	});

	// initialize edit in place
	$('.editable').jinplace()
		.on('jinplace:done', function(ev, data) {
			var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-edit-owner.php';
			$.post(post_url, { owner: data, attribute: 'update-email' },
				function(data) {
					console.log(data);
					$('.owner-email').html(data);
				}
			);
    });
});
</script>
</body>
</html>