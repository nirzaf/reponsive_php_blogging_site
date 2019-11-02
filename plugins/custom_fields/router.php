<?php
require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../admin/_admin_inc.php');
require_once(__DIR__ . '/translation.php');

if(empty($frags[2])) {
	if($frags[1] == 'custom_fields') {
		require_once(__DIR__ . '/admin-custom-fields.php');
	}
	else {

	}
}
else {
	if($frags[1] == 'custom_fields' && $frags[2] == 'create') {
		require_once(__DIR__ . '/admin-create-custom-field.php');
	}

	if($frags[1] == 'custom_fields' && $frags[2] == 'edit') {
		require_once(__DIR__ . '/admin-edit-custom-field.php');
	}
}