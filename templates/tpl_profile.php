<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="description" content="<?= $txt_meta_desc; ?>" />
<?php require_once('_html_head.php'); ?>
</head>
<body class="tpl-profile">
<?php require_once('_header.php'); ?>

<div class="wrapper">
	<div class="full-block">
		<div class="profile-pic-col">
			<div class="profile-pic">
				<div class="profile-pic-container-img" style="background-image:url('<?= $bg_img; ?>');"></div>
			</div>
		</div><!-- .profile-pic-col -->

		<div class="profile-details">
			<h1 class="profile-name"><?= $profile_display_name; ?></h1>
			<?= $txt_joined_on; ?>
		</div>

		<div class="clear"></div>

		<div class="profile-content-col">
			<h3><?= $txt_recent_activity; ?></h3>
			<?php
			if(!empty($reviews)) {
				// only show if place has name
				foreach($reviews as $k => $v) {
					if(!(empty($v['place_name']))) {
						?>
						<div class="review-item" data-review-id="<?= $v['review_id']; ?>">
							<div class="review-item-pic">
								<a href="<?= $v['link_url']; ?>"><img src="<?= $v['thumb_url']; ?>" /></a>
							</div>
							<div class="review-item-description">
								<div id="name-<?= $v['place_id']; ?>">
									<a href="<?= $v['link_url']; ?>"><strong><?= $v['place_name']; ?></strong></a>
								</div>
								<div class="review-pubdate"><?= $v['pubdate']; ?></div>

								<p><?php echo nl2p(ucfirst($v['text'])); ?></p>
							</div>

							<div class="clear"></div>
						</div>
						<?php
					}
				}
			}
			else {
				echo $txt_no_activity;
			}
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

						if($total_reviews > 0) {
							$page_url = "$baseurl/profile/$profile_id/";

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

							$last_page_txt = ($pager->getTotalPages() > 5) ? $txt_pager_page1 : $pager->getTotalPages();

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
		</div><!-- .profile-content-col -->
		<div class="clear"></div>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once('_footer.php'); ?>
</body>
</html>