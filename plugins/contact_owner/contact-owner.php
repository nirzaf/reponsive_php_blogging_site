<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');

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

// get all custom fields and their values
$query = "SELECT * FROM config WHERE property = :plugin_contact_owner";
$stmt  = $conn->prepare($query);
$stmt->bindValue(':plugin_contact_owner', 'plugin_contact_owner');
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$plugin_values = $row['value'];
$plugin_values = unserialize($plugin_values);

$question      = (!empty($plugin_values['question']))      ? $plugin_values['question']       : '';
$answer        = (!empty($plugin_values['answer']))        ? $plugin_values['answer']         : '';
$email_subject = (!empty($plugin_values['email_subject'])) ? $plugin_values['email_subject'] : '';
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_contact_owner_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../admin/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="admin-cats">
<?php require_once(__DIR__ . '/../../admin/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/../../admin/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_contact_owner_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<?php
				$settings_pane = 'active';
				$activity_pane = '';
				if(!empty($frags[3]) && !empty($frags[3])) {
					$settings_pane = '';
					$activity_pane = 'active';
				}
				?>

				<ul class="nav nav-tabs" role="tablist" id="settingsTabs">
					<li role="presentation" class="<?= $settings_pane; ?>">
						<a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><?= $txt_settings; ?></a>
					</li>
					<li role="presentation" class="<?= $activity_pane; ?>">
						<a href="#activity" aria-controls="activity" role="tab" data-toggle="tab"><?= $txt_activity; ?></a>
					</li>
				</ul>
			</div>

			<div class="tab-content">

				<div role="tabpanel" class="tab-pane <?= $settings_pane; ?>" id="settings">
					<form method="post" action="<?= $baseurl; ?>/plugins/contact_owner/contact-owner-process-settings.php">
						<input type="hidden" name="csrf_token" value="<?= session_id(); ?>">
						<div class="form-row">
							<label><strong><?= $txt_contact_owner_question; ?></strong></label>
							<input type="text" id="question" name="question" class="form-control" value="<?= $question; ?>" />
						</div>

						<div class="form-row">
							<label><strong><?= $txt_contact_owner_answer; ?></strong></label>
							<input type="text" id="answer" name="answer" class="form-control" value="<?= $answer; ?>" />
						</div>

						<div class="form-row">
							<label><strong><?= $txt_contact_owner_email_subject; ?></strong></label>
							<input type="text" id="email_subject" name="email_subject" class="form-control" value="<?= $email_subject; ?>" />
						</div>

						<div class="form-row submit-row">
							<input type="submit" id="submit" name="submit" class="btn btn-blue btn-less-padding">
						</div>
					</form>
				</div>

				<div role="tabpanel" class="tab-pane <?= $activity_pane; ?>" id="activity">
					<?php
					// count how many
					$query = "SELECT COUNT(*) AS c FROM contact_msgs";
					$stmt = $conn->prepare($query);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$total_rows = $row['c'];

					if($total_rows > 0) {
						?>
						<span><?= $txt_total_rows; ?>: <strong><?= $total_rows; ?></strong></span>

						<div class="table-responsive">
							<table class="table admin-table">
								<tr>
									<th><?= $txt_id; ?></th>
									<th><?= $txt_sender; ?></th>
									<th><?= $txt_created; ?></th>
									<th><?= $txt_place_name; ?></th>
									<th><?= $txt_action; ?></th>
								</tr>

								<?php
								$pager = new DirectoryApp\PageIterator($limit, $total_rows, $page);
								$start = $pager->getStartRow();

								$query = "SELECT
									m.*, p.place_name, c.city_name, c.slug
									FROM contact_msgs m
									LEFT JOIN places p
										ON m.place_id = p.place_id
									LEFT JOIN cities c
										ON p.city_id = c.city_id
									ORDER BY m.id DESC LIMIT :start, :limit";
								$stmt = $conn->prepare($query);
								$stmt->bindValue(':start', $start);
								$stmt->bindValue(':limit', $limit);
								$stmt->execute();

								while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$msg_id       = (!empty($row['id']))           ? $row['id']           : 'undefined';
									$sender_email = (!empty($row['sender_email'])) ? $row['sender_email'] : 'undefined';
									$sender_ip    = (!empty($row['sender_ip']))    ? $row['sender_ip']    : 'undefined';
									$place_id     = (!empty($row['place_id']))     ? $row['place_id']     : 'undefined';
									$msg          = (!empty($row['msg']))          ? $row['msg']          : 'undefined';
									$created      = (!empty($row['created']))      ? $row['created']      : 'undefined';
									$place_name   = (!empty($row['place_name']))   ? $row['place_name']   : 'undefined';
									$city_name    = (!empty($row['city_name']))    ? $row['city_name']    : 'undefined';
									$city_slug    = (!empty($row['city_slug']))    ? $row['city_slug']    : 'undefined';

									// simplify date
									$created = strtotime($created);
									$created = date( 'Y-m-d', $created );

									// limit strings
									if (strlen($place_name) > 20) {
										$place_name = mb_substr($place_name, 0, 20) . '...';
									}

									// sanitize
									$msg        = e(trim($msg));
									$place_name = e(trim($place_name));

									// link to the place's page
									$link_url = $baseurl . '/' . $city_slug . '/place/' . $place_id . '/' . to_slug($place_name);
									?>
									<tr id="tr-msg-id-<?= $msg_id; ?>">
										<td class="nowrap"><?= $msg_id; ?></td>
										<td class="nowrap"><?= $sender_email; ?></td>
										<td class="nowrap"><?= $created; ?></td>
										<td><a href="<?= $link_url; ?>" target="_blank"><?= $place_name; ?></a></td>
										<td class="nowrap">
											<span data-toggle="tooltip" title="<?= $txt_tooltip_expand_review; ?>">
												<a href="#" class="btn btn-default btn-less-padding expand-msg"
													data-msg-id="<?= $msg_id; ?>">
													&nbsp;<i class="fa fa-expand" aria-hidden="true"></i>&nbsp;
												</a>
											</span>
										</td>
									</tr>
									<tr id="expand-msg-<?= $msg_id; ?>" class="msg-text">
										<td colspan="5" class="wrap">
											<div class="msg-text-wrapper"><?= $msg; ?></div>
										</td>
									</tr>
								<?php
								}
								?>
							</table>
						</div><!-- .table-responsive -->

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
									$page_url = "$baseurl/admin/plugin/contact_owner/page/";

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

				</div><!-- .tab-pane #activity -->
			</div><!-- .tab-content -->
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->


<?php require_once(__DIR__ . '/../../admin/_admin_footer.php'); ?>

<!-- javascript -->
<script>
$(document).ready(function(){
	// hide all msg texts
	$('.msg-text').hide();

	// expand msg
	$('.expand-msg').click(function(e) {
		e.preventDefault();
		var msg_id = $(this).data('msg-id');
		$('#expand-msg-' + msg_id).toggle();

	});

	// tabs
	$('#settingsTabs a:first').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

});
</script>
</body>
</html>