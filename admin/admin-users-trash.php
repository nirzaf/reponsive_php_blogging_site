<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-users-trash.php');

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
$query = "SELECT COUNT(*) AS total_rows FROM users WHERE status = 'trashed'";
$stmt = $conn->prepare($query);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_rows = $row['total_rows'];

// initialize cats array
$users_arr = array();

if($total_rows > 0) {
	$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
	$start = $pager->getStartRow();

	// select all cats information and put in an array
	if($sort == 'sort-name') {
		$query = "SELECT * FROM users WHERE status = 'trashed' ORDER BY first_name LIMIT :start, :limit";
	}
	if($sort == 'sort-email') {
		$query = "SELECT * FROM users WHERE status = 'trashed' ORDER BY email LIMIT :start, :limit";
	}
	if($sort == 'sort-date') {
		$query = "SELECT * FROM users WHERE status = 'trashed' ORDER BY created DESC LIMIT :start, :limit";
	}

	$stmt = $conn->prepare($query);
	$stmt->bindValue(':start', $start);
	$stmt->bindValue(':limit', $limit);
	$stmt->execute();
	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$this_id              = $row['id'];
		$this_username        = $row['first_name'] . ' ' . $row['last_name'];
		$this_email           = $row['email'];
		$this_created         = $row['created'];
		$this_status          = $row['status'];
		$this_prof_pic_status = $row['profile_pic_status'];

		// username
		if (mb_strlen($this_username) > 15) {
			$this_username = mb_substr($this_username, 0, 15) . '...';
		}

		// sanitize
		$this_username = e(trim($this_username));
		$this_email    = e(trim($this_email));

		// simplify date
		$this_created = strtotime($this_created);
		$this_created = date( 'Y-m-d', $this_created );

		// profile pic
		$folder = floor($this_id / 1000) + 1;

		if(strlen($folder) < 1) {
			$folder = '999';
		}

		// profile pic path
		$this_pic_path = $pic_basepath . '/' . $profile_full_folder . '/' . $folder . '/' . $this_id;

		// check if file exists
		$this_pic_glob_arr = glob("$this_pic_path.*");

		if(!empty($this_pic_glob_arr)) {
			$this_prof_pic_filename = basename($this_pic_glob_arr[0]);
		}
		else {
			$this_prof_pic_filename = '';
		}

		if(!empty($this_pic_glob_arr)) {
			// set first match as profile pic
			$this_prof_pic_url = $pic_baseurl . '/' . $profile_full_folder . '/' . $folder . '/' . $this_prof_pic_filename;
		}
		else {
			$this_prof_pic_url = '';
		}

		$cur_loop_arr = array(
			'id'              => $this_id,
			'name'            => $this_username,
			'email'           => $this_email,
			'created'         => $this_created,
			'status'          => $this_status,
			'prof_pic_status' => $this_prof_pic_status,
			'prof_pic_url'    => $this_prof_pic_url
		);

		if($cur_loop_arr['id'] != 1) {
			$users_arr[] = $cur_loop_arr;
		}
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
<body class="admin-users">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>

<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<p><strong><?= $txt_sort; ?>:</strong><br>
				<a href="<?= $baseurl; ?>/admin/admin-users/sort-name/" class="btn btn-default btn-less-padding"><?= $txt_by_name; ?></a>
				<a href="<?= $baseurl; ?>/admin/admin-users/sort-email/" class="btn btn-default btn-less-padding"><?= $txt_by_email; ?></a>
				<a href="<?= $baseurl; ?>/admin/admin-users/sort-date/" class="btn btn-default btn-less-padding"><?= $txt_by_date; ?></a>
				</p>
			</div>

			<div class="block">
				<?php
				if(!empty($users_arr)) {
					?>
					<div class="pull-left"><span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span></div>
					<div class="pull-right"><a href="#" class="empty-trash" data-toggle="modal" data-target="#empty-trash-modal"><?= $txt_empty; ?></a></div>
					<div class="clearfix"></div>
					<div class="table-responsive">
						<table class="table admin-table">
							<tr>
								<th><?= $txt_id; ?></th>
								<th><?= $txt_name; ?></th>
								<th><?= $txt_email; ?></th>
								<th><?= $txt_created; ?></th>
								<th><?= $txt_action; ?></th>
							</tr>
							<?php
							foreach($users_arr as $k => $v) {
								$this_user_id              = $v['id'];
								$this_user_name            = $v['name'];
								$this_user_email           = $v['email'];
								$this_user_created         = $v['created'];
								$this_user_status          = $v['status'];
								?>
								<tr id="user-<?= $this_user_id; ?>">
									<td><?= $this_user_id; ?></td>
									<td><a href="<?= $baseurl; ?>/profile/<?= $this_user_id; ?>" target="_blank"><?= $this_user_name; ?></a></td>
									<td><?= $this_user_email; ?></td>
									<td><?= $this_user_created; ?></td>
									<td>
										<!-- restore btn -->
										<span data-toggle="tooltip" title="<?= $txt_tooltip_restore; ?>">
											<a href="#" class="btn btn-default btn-less-padding restore-user"
												data-user-id="<?= $this_user_id; ?>">
												&nbsp;<i class="fa fa-undo" aria-hidden="true"></i>&nbsp;
											</a>
										</span>

										<!-- remove btn -->
										<span data-toggle="tooltip" title="<?= $txt_tooltip_remove_user; ?>">
											<a href="#" class="btn btn-default btn-less-padding remove-user"
												data-toggle="modal"
												data-target="#remove-user-modal"
												data-user-id="<?= $this_user_id; ?>">
												&nbsp;<i class="fa fa-trash" aria-hidden="true"></i>&nbsp;
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
								$endPage   = 8 + $startPage;
								$endPage   = ($pager->getTotalPages() < $endPage) ? $pager->getTotalPages() : $endPage;
								$diff      = $startPage - $endPage + 8;
								$startPage -= ($startPage - $diff > 0) ? $diff : 0;

								$startPage = ($startPage == 1) ? 2 : $startPage;
								$endPage = ($endPage == $pager->getTotalPages()) ? $endPage - 1 : $endPage;

								if($total_rows > 0) {
									$page_url = "$baseurl/admin/admin-users/$sort/page/";

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
					<p><?= $txt_no_users; ?></p>
					<?php
				}
				?>
			</div><!-- .block -->
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- Remove User Modal -->
<div class="modal fade" id="remove-user-modal" tabindex="-1" role="dialog" aria-labelledby="Remove User Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $txt_modal_remove_title; ?></h4>
			</div>
			<div class="modal-body">
				<?= $txt_remove_perm_sure; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></button>
				<button type="button" class="btn btn-blue btn-less-padding remove-user-confirm" data-dismiss="modal"><?= $txt_remove; ?></button>
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
				<h4 class="modal-title" id="myModalLabel"><?= $txt_empty; ?></h4>
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

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>
<script>
$(document).ready(function(){
	// restore user
    $('.restore-user').click(function(e){
		e.preventDefault();
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-restore-user.php';
		var restore_user_id = $(this).data('user-id');

		$.post(post_url, { restore_user_id: restore_user_id },
			function(data) {
				location.reload(true);
			}
		);
    });

	// when remove user modal pops up
	$('#remove-user-modal').on('show.bs.modal', function(e) {
		var button = $(e.relatedTarget); // Button that triggered the modal
		var remove_user_id = button.data('user-id'); // Extract info from data-* attributes
		var modal = $(this);

		modal.find('.remove-user-confirm').attr('data-user-id', remove_user_id);
	});

	// remove user permanently
	$('.remove-user-confirm').click(function(e) {
		e.preventDefault();
		var remove_user_id = $(this).data('user-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-user-perm.php';
		var tr = '#user-' + remove_user_id;

		$.post(post_url, {
			remove_user_id: remove_user_id
			},
			function(data) {
				$(tr).hide();
			}
		);
	});

	// empty trash button in modal clicked
    $('.empty-trash-confirm').click(function(event){
		event.preventDefault();
		var modal = $('#empty-trash-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-empty-trash-users.php';

		$.post(post_url, {
			from_check: 'admin-users-trash'
			},
			function(data) {
				modal.find('#empty-trash-modal .modal-body').empty();
				modal.find('#empty-trash-modal .modal-body').html(data).fadeIn();
			}
		);
    });

	// after emptying trash and clicking the close button on the modal, reload
	$('#empty-trash-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});
});

</script>
</body>
</html>