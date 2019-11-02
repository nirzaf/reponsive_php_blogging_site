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
</div><!-- #footer -->

<!-- modal city change -->
<div class="modal fade" id="change-city-modal" role="dialog" aria-labelledby="change-city-modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?= $txt_modalclose; ?></span></button>
				<h3 class="modal-title" id="myModalLabel"><?= $txt_selectyourcity; ?></h3>
			</div>
			<div class="modal-body">
				<form id="city-change-form" method="post">
					<div class="block"><select id="city-change" name="city-change"></select></div>
				</form>
			</div><!-- .modal-body -->
			<div class="modal-footer">
				<button type="button" class="btn btn-blue" data-dismiss="modal"><?= $txt_modalclose; ?></button>
				<button type="button" class="btn btn-default" data-dismiss="modal" id="clear-city"><?= $txt_clearcity; ?></button>
			</div>
		</div><!-- .modal-content -->
	</div><!-- .modal-dialog -->
</div><!-- end modal -->

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="<?= $baseurl; ?>/templates/lib/raty/jquery.raty.js"></script>
<script src="<?= $baseurl; ?>/lib/jquery-autocomplete/jquery.autocomplete.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/i18n/<?= $html_lang; ?>.js"></script>
<script>
/* SELECT2 */
// #city-input (main search form in header)
$('#city-input').select2({
	ajax: {
		url: '<?= $baseurl; ?>/_return_cities_select2.php',
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

// #city-change (in modal triggered by navbar city change)
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
	location.reload(true);
});

$(document.body).on('click', '#clear-city', function(e){
	e.preventDefault();
	delete_cookie('city_id');
	delete_cookie('city_name');
	location.reload(true);
});

/* CUSTOM FUNCTIONS */
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