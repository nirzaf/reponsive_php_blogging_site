<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id
require_once($lang_folder . '/admin_translations/trans-index.php');
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="index">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once('_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding clearfix">
			<div class="col-left">
				<?php
				// count pending ads
				$query = "SELECT email FROM users WHERE id = :userid";
				$stmt = $conn->prepare($query);
				$stmt->bindValue(':userid', $userid);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$email = $row['email'];
				?>
				<table class="table">
					<caption><?= $txt_your_info; ?></caption>
					<tr>
						<td style="width: 40%"><strong><?= $txt_your_id; ?></strong></td>
						<td><?= $userid; ?></td>
					</tr>
					<tr>
						<td><strong><?= $txt_your_email; ?></strong></td>
						<td><?= $email; ?></td>
					</tr>
					<tr>
						<td><strong><?= $txt_your_prof; ?></strong></td>
						<td><a href="<?= $baseurl; ?>/profile/<?= $userid; ?>" target="_blank">/profile/<?= $userid; ?></a></td>
					</tr>
					<tr>
						<td><strong><?= $txt_support; ?></strong></td>
						<td>- <a href="https://codecanyon.net/item/directory-app-business-directory-script/16447338/support" target="_blank"><?= $txt_support_req; ?></a><br>
							- <a href="http://codebasedev.com/docs/directoryapp/">User guide</a>
						</td>
					</tr>
				</table>

			</div>
			<div class="col-right">
				<?php
				// count pending ads
				$query = "SELECT COUNT(*) AS total_ads_pending FROM places WHERE status = 'pending'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_ads_pending = $row['total_ads_pending'];

				// count total ads
				$query = "SELECT COUNT(*) AS total_ads FROM places WHERE status <> 'trashed'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_ads = $row['total_ads'];

				// count total users
				$query = "SELECT COUNT(*) AS total_users FROM users WHERE status <> 'trashed'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_users = $row['total_users'];

				// count total reviews
				$query = "SELECT COUNT(*) AS total_reviews FROM reviews WHERE status <> 'trashed'";
				$stmt = $conn->prepare($query);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$total_reviews = $row['total_reviews'];
				?>
				<table class="table">
					<caption><?= $txt_site_stats; ?></caption>
					<tr>
						<td><strong>Script Version</strong></td>
						<td>1.09</td>
					</tr>
					<tr>
						<td><strong><?= $txt_pending_mod; ?></strong></td>
						<td><?= $total_ads_pending; ?></td>
					</tr>
					<tr>
						<td><strong><?= $txt_total_list; ?></strong></td>
						<td><?= $total_ads; ?></td>
					</tr>
					<tr>
						<td><strong><?= $txt_total_users; ?></strong></td>
						<td><?= $total_users; ?></td>
					</tr>
					<tr>
						<td><strong><?= $txt_total_reviews; ?></strong></td>
						<td><?= $total_reviews; ?></td>
					</tr>
				</table>
			</div>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->
<?php require_once(__DIR__ . '/_admin_footer.php'); ?>
</body>
</html>