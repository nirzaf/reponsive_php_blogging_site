<?php
if(strpos($_SERVER['PHP_SELF'], 'custom_fields')) {
	$custom_fields_active = 1;
}
?>
<li>
	<a href="<?= $baseurl; ?>/admin/plugin/custom_fields" <?= ($custom_fields_active) ? 'class="active"' : ''; ?>>
		<i class="fa fa-plus-circle" aria-hidden="true"></i>
		<span class="menu-txt">Custom Fields</span>
	</a>
</li>