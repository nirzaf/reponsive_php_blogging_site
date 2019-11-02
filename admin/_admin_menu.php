<?php
$home_active          = 0;
$listings_active      = 0;
$cats_active          = 0;
$reviews_active       = 0;
$users_active         = 0;
$plans_active         = 0;
$locations_active     = 0;
$settings_active      = 0;
$pages_active         = 0;
$emails_active        = 0;
$txn_history_active   = 0;
$tools_active         = 0;
$custom_fields_active = 0;

if(basename($_SERVER['SCRIPT_NAME']) == 'index.php') {
	$home_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-listings.php') {
	$listings_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-cats.php') {
	$cats_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-reviews.php') {
	$reviews_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-users.php') {
	$users_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-plans.php') {
	$plans_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-locations.php') {
	$locations_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-settings.php') {
	$settings_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-pages.php') {
	$pages_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-create-page.php') {
	$pages_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-emails.php') {
	$emails_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-txn-history.php') {
	$txn_history_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'admin-tools.php') {
	$tools_active = 1;
}
?>
<ul>
	<li>
		<a href="<?= $baseurl; ?>/admin/" <?= ($home_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-home" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_admin_dashboard; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-listings" <?= ($listings_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-list" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_listings; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-cats" <?= ($cats_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-sitemap" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_categories; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-reviews" <?= ($reviews_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-commenting" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_reviews; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-users" <?= ($users_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-users" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_users; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-plans" <?= ($plans_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-sticky-note" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_plans; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-locations/show-cities" <?= ($locations_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-location-arrow" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_locations; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-settings" <?= ($settings_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-cog" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_site_settings; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-pages" <?= ($pages_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-file-text-o" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_pages; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-emails" <?= ($emails_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-envelope-o" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_emails; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-txn-history" <?= ($txn_history_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-money" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_transactions; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/admin/admin-tools" <?= ($tools_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-wrench" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_tools; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/user/select-plan">
			<i class="fa fa-pencil" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_menu_add_listing; ?></span>
		</a>
	</li>

	<?php
	// scan plugin directories, search for menu.php inside each plugin folder
	$scan = glob(__DIR__ . '/../plugins/*', GLOB_ONLYDIR|GLOB_NOSORT);

	foreach($scan as $v) {
		if(file_exists($v . '/menu.php')) {
			include_once($v . '/menu.php');
		}
	}
	?>
</ul>
