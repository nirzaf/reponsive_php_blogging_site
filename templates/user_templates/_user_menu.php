<?php
$menu_home_active       = 0;
$menu_my_profile_active = 0;
$menu_my_places_active  = 0;
$menu_my_reviews_active = 0;
$menu_edit_pass_active  = 0;

if(basename($_SERVER['SCRIPT_NAME']) == 'index.php') {
	$menu_home_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'my-profile.php') {
	$menu_my_profile_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'my-places.php') {
	$menu_my_places_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'my-reviews.php') {
	$menu_my_reviews_active = 1;
}

if(basename($_SERVER['SCRIPT_NAME']) == 'edit-pass.php') {
	$menu_edit_pass_active = 1;
}
?>
<ul>
	<li>
		<a href="<?= $baseurl; ?>/user/my-profile" <?= ($menu_my_profile_active) ?  'class="active"' : ''; ?>>
			<i class="fa fa-user" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_my_profile; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/user/my-places/page/1" <?= ($menu_my_places_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-list" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_my_places; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/user/my-reviews/page/1" <?= ($menu_my_reviews_active) ? 'class="active"' : ''; ?>>
			<i class="fa fa-commenting" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_my_reviews; ?></span>
		</a>
	</li>
	<li>
		<a href="<?= $baseurl; ?>/user/select-plan">
			<i class="fa fa-pencil" aria-hidden="true"></i>
			<span class="menu-txt"><?= $txt_add_place; ?></span>
		</a>
	</li>

	<?php
	if($hybridauth_provider_name == 'local' || $is_admin) { ?>
		<li>
			<a href="<?= $baseurl; ?>/user/edit-pass" <?= ($menu_edit_pass_active) ? 'class="active"' : ''; ?>>
				<i class="fa fa-lock" aria-hidden="true"></i>
				<span class="menu-txt"><?= $txt_change_pass; ?></span>
			</a>
		</li>
		<?php
	}
	?>
</ul>