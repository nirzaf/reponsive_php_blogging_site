<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-reviews.php');

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
$page = !empty($frags[2]) ? $frags[2] : 1;
$limit = $items_per_page;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// count how many
$query = "SELECT COUNT(*) AS c FROM reviews WHERE status <> 'trashed'";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['c'];
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>
.review-text {
	display: none;
}

@media only screen and (min-width: 768px) {
	.form-inline .form-control {
		width: 100%;
	}
}

.control-group.form-group {
	width: 100%;
}
</style>
</head>
<body class="admin-reviews">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="pull-left"><span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span></div>
			<div class="pull-right"><a href="<?= $baseurl; ?>/admin/admin-reviews-trash"><?= $txt_trash; ?></a></div>
			<div class="clearfix"></div>
			<?php
			if($total_rows > 0) {
				?>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th><?= $txt_id; ?></th>
							<th><?= $txt_date; ?></th>
							<th><?= $txt_user; ?></th>
							<th><?= $txt_place_name; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
						$start = $pager->getStartRow();

						$query = "SELECT
							r.review_id, r.place_id, r.pubdate, r.rating, r.text, r.user_id, r.status,
							p.place_name,
							ci.city_id AS review_city_id, ci.slug AS city_slug, ci.city_name,
							ph.dir, ph.filename,
							u.first_name, u.last_name
							FROM reviews r LEFT JOIN places p
								ON r.place_id = p.place_id
							LEFT JOIN cities ci
								ON p.city_id = ci.city_id
							LEFT JOIN (SELECT * FROM photos GROUP BY place_id) ph
								ON ph.place_id = r.place_id
							LEFT JOIN users u
								ON r.user_id = u.id
							WHERE r.status = 'pending' OR r.status = 'approved'
							ORDER BY pubdate DESC LIMIT :start, :limit";
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':start', $start);
						$stmt->bindValue(':limit', $limit);
						$stmt->execute();

						// places ids array
						$places_ids_arr = array();

						while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$review_id        = $row['review_id'];
							$place_id         = (!empty($row['place_id']))       ? $row['place_id']       : null;
							$pubdate          = (!empty($row['pubdate']))        ? $row['pubdate']        : '2016-03-18';
							$rating           = (!empty($row['rating']))         ? $row['rating']         : 0;
							$text             = (!empty($row['text']))           ? $row['text']           : '';
							$place_name       = (!empty($row['place_name']))     ? $row['place_name']     : '-';
							$review_city_id   = (!empty($row['review_city_id'])) ? $row['review_city_id'] : null;
							$review_city_slug = (!empty($row['city_slug']))      ? $row['city_slug']      : null;
							$review_city_name = (!empty($row['city_name']))      ? $row['city_name']      : null;
							$status           = (!empty($row['status']))         ? $row['status']         : null;
							$dir              = (!empty($row['dir']))            ? $row['dir']            : null;
							$filename         = (!empty($row['filename']))       ? $row['filename']       : null;
							$first_name       = (!empty($row['first_name']))     ? $row['first_name']     : null;
							$last_name        = (!empty($row['last_name']))      ? $row['last_name']      : null;

							// simplify date
							$pubdate = strtotime($pubdate);
							$pubdate = date( 'Y-m-d', $pubdate );

							// username
							$username = "$first_name $last_name";
							if (mb_strlen($username) > 10) {
								$username = mb_substr($username, 0, 10) . '...';
							}

							// limit strings
							if (strlen($place_name) > 20) {
								$place_name = mb_substr($place_name, 0, 20) . '...';
							}

							// sanitize
							$text       = e(trim($text));
							$place_name = e(trim($place_name));
							$first_name = e(trim($first_name));
							$last_name  = e(trim($last_name));

							// link to the place's page
							$link_url = $baseurl . '/' . $review_city_slug . '/place/' . $place_id . '/' . to_slug($place_name);

							// thumb
							if(!empty($row['filename'])) {
								$thumb_url = $pic_baseurl . '/' . $place_thumb_folder . '/' . $dir . '/' . $filename;
							}
							else {
								$thumb_url = $baseurl . '/imgs/empty.png';
							}
							?>
							<tr id="tr-review-id-<?= $review_id; ?>">
								<td class="nowrap"><?= $review_id; ?></td>
								<td class="nowrap"><?= $pubdate; ?></td>
								<td class="nowrap"><?= $username; ?></td>
								<td><a href="<?= $link_url; ?>" target="_blank"><?= $place_name; ?></a></td>
								<td class="nowrap">
									<?php
									if($status == 'pending') {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_tooltip_toggle_approved; ?>">
											<a href="#" class="btn btn-default btn-less-padding approve-review"
												id="status-review-<?= $review_id; ?>"
												data-review-id="<?= $review_id; ?>"
												data-status="pending">
												<i class="fa fa-toggle-off" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_tooltip_toggle_approved; ?>">
											<a href="#" class="btn btn-green btn-less-padding approve-review"
												id="status-review-<?= $review_id; ?>"
												data-review-id="<?= $review_id; ?>"
												data-status="approved">
												<i class="fa fa-toggle-on" aria-hidden="true"></i>
											</a>
										</span>
										<?php
									}
									?>

									<span data-toggle="tooltip" title="<?= $txt_tooltip_expand_review; ?>">
										<a href="#" class="btn btn-default btn-less-padding expand-review"
											data-review-id="<?= $review_id; ?>">
											&nbsp;<i class="fa fa-expand" aria-hidden="true"></i>&nbsp;
										</a>
									</span>

									<span data-toggle="tooltip" title="<?= $txt_tooltip_remove_review; ?>">
										<a href="#" class="btn btn-default btn-less-padding remove-review"
											data-review-id="<?= $review_id; ?>">
											&nbsp;<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
										</a>
									</span>
								</td>
							</tr>
							<tr id="expand-review-<?= $review_id; ?>" class="review-text">
								<td colspan="5" class="wrap">
									<div class="review-text-wrapper"><?= $text; ?></div>
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
							$page_url = "$baseurl/admin/admin-reviews/page/";

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
			} // end if total_rows > 0
			else {
				?><?= $txt_no_reviews_pending; ?><?php
			}
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<script type="text/javascript">
$(document).ready(function(){
	// hide all reviews' texts
	$('.review-text').hide();

	// expand review
	$('.expand-review').click(function(e) {
		e.preventDefault();
		var review_id = $(this).data('review-id');
		$('#expand-review-' + review_id).toggle();

	});

	// remove review
	$('.remove-review').click(function(e) {
		e.preventDefault();
		var review_id = $(this).data('review-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-review.php';
		var tr = '#tr-review-id-' + review_id;
		var tr2 = '#expand-review-' + review_id;
		$.post(post_url, {
			review_id: review_id
			},
			function(data) {
				$(tr).hide();
				$(tr2).hide();
			}
		);
	});

	// toggle review status
	$('.approve-review').click(function(e) {
		e.preventDefault();
		var review_id = $(this).data('review-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-approve-review.php';
		var status = $(this).data('status');

		$.post(post_url, {
			review_id: review_id,
			status: status
			},
			function(data) {
				if(data == 'approved') {
					$('#status-review-' + review_id).removeClass('btn-default');
					$('#status-review-' + review_id).addClass('btn-green');
					$('#status-review-' + review_id + ' i').removeClass('fa-toggle-off');
					$('#status-review-' + review_id + ' i').addClass('fa-toggle-on');
					$('#status-review-' + review_id).data('status', 'approved');
				}
				if(data == 'pending') {
					$('#status-review-' + review_id).removeClass('btn-green');
					$('#status-review-' + review_id).addClass('btn-default');
					$('#status-review-' + review_id + ' i').removeClass('fa-toggle-on');
					$('#status-review-' + review_id + ' i').addClass('fa-toggle-off');
					$('#status-review-' + review_id).data('status', 'pending');
				}
			}
		);
	});
});

</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jinplace/1.2.1/jinplace.min.js"></script>
</body>
</html>