<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<?php require_once( __DIR__ . '/../templates/favicon.inc.php'); ?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= $baseurl; ?>/admin/_admin_styles.css">

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
var baseurl = '<?= $baseurl; ?>/admin';

// add CSRF token in the headers of all requests
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': '<?= session_id(); ?>',
		'X-AJAX-Setup': 1
    }
});
</script>
<style>
body {
    opacity: 1;
    transition: 0.2s opacity;
}
body.fade-out {
    opacity: 0;
    transition: none;
}
</style>
