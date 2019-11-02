<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>

</head>
<body class="tpl-my-places">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once('_user_menu.php'); ?>
	</div>
	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<?php
			if($total_rows > 0) {
				$count = 1;
				foreach($places_arr as $k => $v) {
					?>
					<div class="block">
						<div class="user-item" id="place-<?= $v['place_id']; ?>">
							<div class="user-item-pic" id="<?= $v['place_id']; ?>">
								<a href="<?= $baseurl; ?>/<?= $v['city_slug']; ?>/place/<?= $v['place_id']; ?>/<?= $v['place_slug']; ?>" title="<?= $v['place_name']; ?>"><img src="<?= $v['photo_url']; ?>" /></a>
							</div><!-- .user-item-pic -->

							<div class="user-item-description">
								<div class="place-name" id="name-<?= $v['place_id']; ?>">
									<a href="<?= $baseurl; ?>/<?= $v['city_slug']; ?>/place/<?= $v['place_id']; ?>/<?= $v['place_slug']; ?>" title="<?= $v['place_name']; ?>"><?= $v['place_name']; ?></a>
								</div>
								<div class="user-item-pubdate"><?= $v['submission_date']; ?></div>

								<?= $v['description']; ?>

								<!-- controls -->
								<div class="controls">
									<a href="#" class="btn btn-ghost btn-less-padding"
										data-toggle="modal"
										data-target="#remove-place-modal"
										data-place-id="<?= $v['place_id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i>
 <?= $txt_remove_place; ?>
									</a>

									<a href="<?= $baseurl; ?>/user/edit-place/<?= $v['place_id']; ?>" class="btn btn-ghost btn-less-padding edit-place"
										data-edit-id="<?= $v['place_id']; ?>"><i class="fa fa-pencil" aria-hidden="true"></i>
 <?= $txt_edit_place; ?>
									</a>

									<span class="label label-default label-less-padding"><?= $v['status']; ?></span>
								</div><!-- .controls -->

							</div><!-- .user-item-description -->
							<div class="clear"></div>
						</div><!-- .user-item -->
					</div><!-- .block -->
					<?php
					$count++;
				} // end foreach
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
								$page_url = "$baseurl/user/my-places/page/";

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
				<div class="block"><?= $txt_no_activity; ?></div>
				<?php
			}
			?>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_user_footer.php'); ?>

<!-- Remove Place Modal -->
<div class="modal fade" id="remove-place-modal" tabindex="-1" role="dialog" aria-labelledby="Remove Place Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Remove</h4>
			</div>
			<div class="modal-body">
				Are you sure you want to remove this place?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-red remove-place" data-dismiss="modal" data-remove-id>Remove this place</button>
			</div>
		</div>
	</div>
</div><!-- end modal remove place -->

<script type="text/javascript">
// remove place modal
$('#remove-place-modal').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget); // Button that triggered the modal
	var place_id = button.data('place-id'); // Extract info from data-* attributes
	var modal = $(this);

	// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
	var post_url = '<?= $baseurl; ?>' + '/user/ajax-get-place-name.php';

	$.post(post_url, { place_id: place_id },
		function(data) {
			modal.find('.modal-title').text(data);
			modal.find('.remove-place').data('remove-id', place_id);
		}
	);
});

// remove place submit
$(document).ready(function(){
	$('.remove-place').click(function(e) {
		e.preventDefault();
		var place_id = $(this).data('remove-id');
		var post_url = '<?= $baseurl; ?>' + '/user/process-remove-place.php';
		var wrapper = '#place-' + place_id;
		$.post(post_url, {
			place_id: place_id
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
					var place_removed = $('<div class="alert alert-success"></div>');
					$(place_removed).text(data);
					$(place_removed).hide().appendTo(wrapper).fadeIn();
				}
			}
		);
	});

});

</script>
</body>
</html>
