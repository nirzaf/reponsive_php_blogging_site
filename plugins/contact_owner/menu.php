<?php
require __DIR__ . '/translation.php';
$contact_owner_active = 0;
if ( strpos($_SERVER['PHP_SELF'], 'contact_owner') ) {
	$contact_owner_active = 1;
}
?>
<li>
	<a href="<?= $baseurl; ?>/admin/plugin/contact_owner" <?= ($contact_owner_active) ? 'class="active"' : ''; ?>>
		<i class="fa fa-envelope" aria-hidden="true"></i>
		<span class="menu-txt"><?= $txt_contact_owner_menu; ?></span>
	</a>
</li>