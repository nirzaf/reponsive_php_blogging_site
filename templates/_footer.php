<div id="footer">
	<div id="footer-inner">
		<div class="footer-inner-left">
			<?= $site_name; ?>
		</div>
		<div class="footer-inner-right">
			<ul>
				<?= show_menu('footer_menu', false); ?>
				<li><a href="<?= $baseurl; ?>/_contact">Contact</a>
			</ul>
		</div>
		<div class="clear"></div>
	</div>
</div>

<!-- modal city selector -->
<div class="modal fade" id="change-city-modal" role="dialog" aria-labelledby="change-city-modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $txt_modalclose; ?></span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_selectyourcity; ?></h3>
			</div>
			<div class="modal-body">
				<form id="city-change-form" method="post">
					<div class="block">
						<select id="city-change" name="city-change"></select>
						<span id="current-location-link"></span>
					</div>
				</form>
			</div><!-- .modal-body -->
			<div class="modal-footer">
				<button type="button" class="btn btn-blue" data-dismiss="modal"><?= $txt_modalclose; ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal" id="clear-city"><?= $txt_clearcity; ?></button>
			</div>
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- end modal -->

<!-- css -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,700,400italic,700italic">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<!-- external javascript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="<?= $baseurl; ?>/templates/lib/raty/jquery.raty.js"></script>
<script src="<?= $baseurl; ?>/lib/jquery-autocomplete/jquery.autocomplete.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/<?= $html_lang; ?>.js"></script>

<!-- city selector in navbar -->
<script>
$('#city-input').select2({
	ajax: {
		url: '<?= $baseurl; ?>/_return_cities_select2.php',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				query: params.term,
				page: params.page
			};
		}
	},
	escapeMarkup: function (markup) { return markup; },
	minimumInputLength: 1,
	dropdownAutoWidth : true,
	placeholder: "<?= $txt_inputplaceholder_city; ?>",
	language: "<?= $html_lang; ?>"
});
</script>

<!-- preselect city in search bar -->
<?php
if(!empty($_COOKIE['city_id'])) {

	$option_text = (!empty($_COOKIE['city_name'])) ? $_COOKIE['city_name'] : '';
	$option_text .= (!empty($_COOKIE['state_abbr'])) ? ', ' . $_COOKIE['state_abbr'] : '';
	?>
	<script>
	// create the option and append to Select2
	var option = new Option('<?= $option_text ?>', '<?= $_COOKIE['city_id'] ?>', true, true);

	$('#city-input').append(option).trigger('change');

	// manually trigger the `select2:select` event
	$('#city-input').trigger({
		type: 'select2:select',
		params: {
			city_id: <?= $_COOKIE['city_id'] ?>
		}
	});
	</script>
	<?php
}
?>

<!-- location modal -->
<script>
$('#city-change').select2({
	ajax: {
		url: '<?= $baseurl; ?>' + '/_return_cities_select2.php',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				query: params.term, // search term
				page: params.page
			};
		}
	},
	escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	minimumInputLength: 1,
	dropdownAutoWidth : true,
	placeholder: "<?= $txt_inputplaceholder_city; ?>",
	language: "<?= $html_lang; ?>"
});

$(document.body).on('change', '#city-change', function(){
	delete_cookie('city_name');
	createCookie('city_id', this.value, 90);

	<?php
	if(basename($_SERVER['SCRIPT_NAME']) == 'list.php') {
		// get category
		if(!empty($cat_id)) {
			?>
			window.location.replace('<?= $baseurl ?>/city/list/cat/c-' + this.value + '-<?= $cat_id ?>-1');
			<?php
		}

		else {
			?>
			location.reload(true);
			<?php
		}
	}

	else {
		?>
		location.reload(true);
		<?php
	}
	?>
});

// insert html for use current location link
if ('geolocation' in navigator && localStorage.clear_loc) {
	var current_location_link = '<br><a href="#"><?= $txt_current_location ?></a>';

	$('#current-location-link').append(current_location_link);

	$(document.body).on('click', '#current-location-link a', function() {
		localStorage.removeItem('clear_loc');
		location.reload(true);
	});
}
</script>

<!-- clear location -->
<script>
$(document.body).on('click', '#clear-city', function(e){
	e.preventDefault();
	delete_cookie('city_id');
	delete_cookie('city_name');
	localStorage.setItem('clear_loc', 1);
	location.reload(true);
});
</script>

<!-- custom functions -->
<script>
function createCookie(name, value, days) {
    var expires;
    var cookie_path;
	var path = "<?= $install_path; ?>";

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    } else {
        expires = "";
    }

	if (path != '') {
		cookie_path = "; path=" + path;
	} else {
		cookie_path = "";
	}

    document.cookie = name + "=" + value + expires + cookie_path;
}
function delete_cookie(name) {
	createCookie(name, "", -100);
}
</script>