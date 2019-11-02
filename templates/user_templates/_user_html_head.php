<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require_once(__DIR__ . '/../favicon.inc.php'); ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css">
<link rel="stylesheet" href="<?= $baseurl; ?>/templates/css/styles.css">
<link rel="stylesheet" href="<?= $baseurl; ?>/templates/user_templates/_user_styles.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js"></script>
<script>
var baseurl = '<?= $baseurl; ?>';

// add CSRF token in the headers of all requests
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': '<?= session_id() ?>',
		'X-AJAX-Setup': 1
    }
});
</script>
