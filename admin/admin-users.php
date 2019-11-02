<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-users.php');

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

// count how many cats
$query = "SELECT COUNT(*) AS total_rows FROM users WHERE status <> 'trashed'";
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
		$query = "SELECT * FROM users WHERE status <> 'trashed' ORDER BY first_name LIMIT :start, :limit";
	}
	if($sort == 'sort-email') {
		$query = "SELECT * FROM users WHERE status <> 'trashed' ORDER BY email LIMIT :start, :limit";
	}
	if($sort == 'sort-date') {
		$query = "SELECT * FROM users WHERE status <> 'trashed' ORDER BY created DESC LIMIT :start, :limit";
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
					<div class="pull-right"><a href="<?= $baseurl; ?>/admin/admin-users-trash"><?= $txt_trash; ?></a></div>
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
								$this_user_prof_pic_status = $v['prof_pic_status'];
								$this_user_prof_pic_url    = $v['prof_pic_url'];
								?>
								<tr id="user-<?= $this_user_id; ?>">
									<td><?= $this_user_id; ?></td>
									<td><a href="<?= $baseurl; ?>/profile/<?= $this_user_id; ?>" target="_blank"><?= $this_user_name; ?></a></td>
									<td><?= $this_user_email; ?></td>
									<td><?= $this_user_created; ?></td>
									<td>
										<span data-toggle="tooltip" title="<?= $txt_tooltip_remove_user; ?>">
											<a href="" class="btn btn-default btn-less-padding remove-user"
												data-user-id="<?= $this_user_id; ?>">
												<i class="fa fa-trash"></i>
											</a>
										</span>

										<?php
										if($this_user_prof_pic_status == 'pending') {
											?>
											<span id="profile-pic-btn-<?= $this_user_id; ?>" data-toggle="tooltip" title="<?= $txt_tooltip_approve_profile_pic; ?>"
												>
												<a href="#" class="btn btn-default btn-less-padding pending-profile-pic"
													data-id="<?= $this_user_id; ?>"
													data-toggle="modal"
													data-target="#profile-pic-modal"
													data-profile-id="<?= $this_user_id; ?>"
													data-profile-pic-folder="<?= $folder; ?>"
													data-profile-pic-filename="<?= $this_user_prof_pic_url; ?>">
													<i class="fa fa-camera"></i>
												</a>
											</span>
											<?php
										}
										?>
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

<!-- modal profile picture -->
<div class="modal fade" id="profile-pic-modal" tabindex="-1" role="dialog" aria-labelledby="Profile Pic Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><?= $txt_approve_profile_pic; ?></h4>
			</div>
			<div class="modal-body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn-less-padding" data-dismiss="modal"><?= $txt_cancel; ?></button>
				<button type="button" class="btn btn-blue btn-less-padding pic-approve" data-dismiss="modal" data-approve-id><?= $txt_approve; ?></button>
				<button type="button" class="btn btn-default btn-less-padding pic-delete" data-dismiss="modal" data-delete-id><?= $txt_delete; ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end modal -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>
<script>
$(document).ready(function(){
	// call #profile-pic-modal
	$('#profile-pic-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var profile_id = button.data('profile-id'); // Extract info from data-* attributes
		var filename = button.data('profile-pic-filename'); // Extract info from data-* attributes
		var modal = $(this);

		modal.find('.pic-approve').attr('data-approve-id', profile_id);
		modal.find('.pic-delete').attr('data-delete-id', profile_id);
		$('.modal-body').empty();
		$('.modal-body').prepend('<img src="' + filename + '" class="modal-profile-pic" />');
	});

	// delete profile pic
	$('.pic-delete').click(function(e) {
		e.preventDefault();
		var delete_id = $(this).attr('data-delete-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-moderate-profile-pic.php';
		var btn_wrapper = '#profile-pic-btn-' + delete_id;

		$.post(post_url, {
			delete_id: delete_id,
			operation: 'delete'
			},
			function(data) {
				$(btn_wrapper).empty();
			}
		);
	});

	// approve profile-pic
	$('.pic-approve').click(function(e) {
		e.preventDefault();
		var approve_id = $(this).attr('data-approve-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-moderate-profile-pic.php';
		var btn_wrapper = '#profile-pic-btn-' + approve_id;

		$.post(post_url, {
			approve_id: approve_id,
			operation: 'approve'
			},
			function(data) {
				$(btn_wrapper).empty();
			}
		);
	});

	// remove user
	$('.remove-user').click(function(e) {
		e.preventDefault();
		var user_id = $(this).data('user-id');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-user.php';
		var tr = '#user-' + user_id;

		$.post(post_url, {
			user_id: user_id
			},
			function(data) {
				$(tr).hide();
			}
		);
	});
});

</script>
</body>
</html>