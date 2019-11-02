<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-my-reviews">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper my-reviews">
	<div class="menu-box">
		<?php require_once('_user_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<?php
			if(!empty($reviews)) {
				$count = 1;
				foreach($reviews as $k => $v) {
					if(!(empty($v['place_name']))) {
						?>
						<div class="content-row">
							<div class="user-item" id="review-<?= $v['review_id']; ?>">
								<div class="user-item-pic">
									<a href="<?= $v['link_url']; ?>"><img src="<?= $v['thumb_url']; ?>" /></a>
								</div>

								<div class="user-item-description">
									<div class="place-name" id="name-<?= $v['place_id']; ?>">
										<a href="<?= $v['link_url']; ?>" target="_blank"><?= $v['place_name']; ?></a>
									</div>
									<div class="review-pubdate"><?= $v['pubdate']; ?></div>

									<div
										class="editable"
										data-type="textarea"
										data-url="<?= $baseurl; ?>/user/process-edit-review.php"
										data-activator="#activator-<?= $v['review_id']; ?>"
										data-attribute="<?= $v['review_id']; ?>"
									>
										<?php echo nl2p(ucfirst($v['text'])); ?>
									</div>

									<!-- review controls -->
									<div class="controls">
										<a href="#" class="btn btn-ghost btn-less-padding"
											data-toggle="modal"
											data-target="#remove-review-modal"
											data-review-id="<?= $v['review_id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i> <?= $txt_remove_review; ?>
										</a>

										<a href="#" id="activator-<?= $v['review_id']; ?>" class="btn btn-ghost btn-less-padding"><i class="fa fa-pencil" aria-hidden="true"></i> <?= $txt_edit_review; ?>
										</a>
									</div><!-- .controls -->
								</div>

								<div class="clear"></div>
							</div><!-- .user-item -->
						</div><!-- .content-row -->
						<?php
					}
				}
			}

			// else empty $reviews_arr
			else {
				?>
				<div class="content-row"><?= $txt_no_activity; ?></div>
				<?php
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

						if($total_rows > 0) {
							$page_url = "$baseurl/user/my-reviews/page/";

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

			<!-- Modal -->
			<div class="modal fade" id="remove-review-modal" tabindex="-1" role="dialog" aria-labelledby="Remove Review Modal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel"><?= $txt_modal_remove_review_title; ?></h4>
						</div>
						<div class="modal-body">
							<?= $txt_modal_remove_review_msg; ?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?= $txt_modal_remove_review_cancel_btn; ?></button>
							<button type="button" class="btn btn-primary remove-review" data-dismiss="modal" data-remove-id><?= $txt_modal_remove_review_confirm_btn; ?></button>
						</div>
					</div>
				</div>
			</div>

		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_user_footer.php'); ?>

<script type="text/javascript">
// dynamic modal code
$('#remove-review-modal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget); // Button that triggered the modal
	var review_id = button.data('review-id'); // Extract info from data-* attributes
	var modal = $(this);

	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	// var post_url = '<?= $baseurl; ?>' + '/user/ajax-get-place-name.php';

	modal.find('.remove-review').attr('data-remove-id', review_id);

	//$.post(post_url, { review_id: review_id },
	//	function(data) {
	//		modal.find('.remove-review').attr('data-remove-id', review_id);
	//	}
	//);
})

$(document).ready(function(){
	$('.remove-review').click(function() {
		var review_id = $(this).attr('data-remove-id');
		var post_url = '<?= $baseurl; ?>' + '/user/process-remove-review.php';
		var wrapper = '#review-' + review_id;
		$.post(post_url, {
			review_id: review_id
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
					var review_removed = $('<div class="alert alert-success"></div>');
					$(review_removed).text(data);
					$(review_removed).hide().appendTo(wrapper).fadeIn();
				}
			}
		);
	});

	// edit in place
	$('.editable').jinplace();

});
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jinplace/1.2.1/jinplace.min.js"></script>
</body>
</html>
