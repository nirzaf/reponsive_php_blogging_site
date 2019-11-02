<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require_once( __DIR__ . '/favicon.inc.php'); ?>

<!-- css -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css">
<link rel="stylesheet" href="<?= $baseurl; ?>/templates/css/styles.css">

<!-- javascript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js"></script>
<script src="<?= $baseurl; ?>/lib/head.js"></script>

<!-- baseurl -->
<script>
var baseurl = '<?= $baseurl; ?>';
</script>

<!-- geolocation -->
<?php
if(empty($_COOKIE['city_id'])) {
	?>
	<script>
	if ('geolocation' in navigator && !localStorage.clear_loc) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			var url = '<?= $baseurl; ?>/inc/nearest-location.php';

			// debug
			console.log(lat + ', ' + lng);

			// ajax post
			$.post(url, {
				lat: lat,
				lng: lng
			}, function(data) {
				// parse json response
				data = JSON.parse(data);

				// create cookie
				createCookie('city_id', data.city_id, 90);

				// reload
				location.reload(true);
			});
		});
	}
	</script>
	<?php
}
?>