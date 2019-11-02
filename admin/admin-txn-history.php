<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id
require_once($lang_folder . '/admin_translations/trans-txn-history.php');

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
$limit = 50;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// find total number of transactions
$query = "SELECT COUNT(*) AS total_rows FROM transactions";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="description" content="" />
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>
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
<body class="admin-txn-history">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span>
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th><!-- count --></th>
						<th><?= $txt_txn_id; ?></th>
						<th><?= $txt_place_id; ?></th>
						<th><?= $txt_txn_type; ?></th>
						<th><?= $txt_amount; ?></th>
						<th><?= $txt_sub_id; ?></th>
						<th><?= $txt_txn_date; ?></th>
					</tr>
					<?php
					// item counter
					$count = ($page - 1) * $items_per_page;

					if($total_rows > 0) {
						$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
						$start = $pager->getStartRow();

						$query = "SELECT * FROM transactions ORDER BY txn_date DESC LIMIT $start, $limit";
						$stmt = $conn->prepare($query);
						$stmt->bindValue(':start', $start);
						$stmt->bindValue(':limit', $limit);
						$stmt->execute();

						while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$count++;

							$txn_id         = $row['txn_id'];
							$place_id       = $row['place_id'];
							$txn_type       = $row['txn_type'];
							$amount         = $row['amount'];
							$subscr_id      = $row['subscr_id'];
							$txn_date       = $row['txn_date'];
							$payment_status = $row['payment_status'];

							$txn_id_compact = (strlen($txn_id) > 6) ? substr($txn_id,0,6).'...' : $txn_id;
							$subscr_id_compact = (strlen($subscr_id) > 6) ? substr($subscr_id,0,6).'...' : $subscr_id;

							echo '<tr>';
								echo '<td>';
								echo $count;
								echo '</td>';

								echo '<td>';
								echo $txn_id_compact;
								echo '</td>';

								echo '<td>';
								echo $place_id;
								echo '</td>';

								echo '<td>';
								echo '<span data-toggle="tooltip" title="' . $payment_status . '">' . $txn_type . '</span>';
								echo '</td>';

								echo '<td>';
								echo $amount;
								echo '</td>';

								echo '<td>';
								echo $subscr_id_compact;
								echo '</td>';

								echo '<td>';
								echo $txn_date;
								echo '</td>';
							echo '</tr>';
						}
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
							$page_url = "$baseurl/admin/moderate-reviews/page/";

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
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

</body>
</html>