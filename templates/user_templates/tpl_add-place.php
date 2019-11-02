<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="robots" content="noindex">
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>
</head>
<body class="tpl-add-place">
<?php require_once(__DIR__ . '/_user_header.php'); ?>
<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<div class="form-wrapper">
			<form method="post" id="the_form" action="<?= $baseurl; ?>/user/process-add-place.php">
				<input type="hidden" id="submit_token" name="submit_token" value="<?= $submit_token; ?>">
				<input type="hidden" name="csrf_token" value="<?= session_id(); ?>">
				<input type="hidden" id="latlng" name="latlng">
				<input type="hidden" id="plan_id" name="plan_id" value="<?= $plan_id; ?>">

				<p><?= $txt_click_map; ?> (*)</p>
				<div class="alert-bubble" id="validate-latlng"><?= $txt_validate_latlng; ?></div>

				<div id="map-wrapper">
					<div id="map-canvas" style="width:100%; height:100%"></div>
				</div>

				<div class="form-row">
					<div class="form-row-full">
						<div><label for="place_name"><?= $txt_label_place_name; ?></label></div>
						<div><input type="text" id="place_name" name="place_name" required></div>

						<div class="alert-bubble" id="validate-place_name"><?= $txt_validate_place_name; ?></div>
					</div>
				</div>

				<h3><?= $txt_header_address_location; ?></h3>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="address"><?= $txt_label_address; ?> *</label></div>
						<div><input type="text" id="address" name="address" required></div>
						<div class="alert-bubble" id="validate-address"><?= $txt_validate_address; ?></div>
					</div>

					<div class="form-row-half">
						<div><label for="postal_code"><?= $txt_label_postal_code; ?></label></div>
						<div><input type="text" id="postal_code" name="postal_code" /></div>
					</div>

					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="cross_street"><?= $txt_label_cross_street; ?></label></div>
						<div><input type="text" id="cross_street" name="cross_street" /></div>
					</div>

					<div class="form-row-half">
						<div><label for="neighborhood"><?= $txt_label_neighborhood; ?></label></div>
						<div><input type="text" id="neighborhood" name="neighborhood" /></div>
					</div>
					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="city_id"><?= $txt_label_city; ?></label></div>
						<div>
							<select id="city_id" name="city_id"></select>
						</div>
					</div>

					<div class="form-row-half">
						<div><label for="inside"><?= $txt_label_inside; ?> <a class="the-tooltip" data-toggle="tooltip" data-placement="top" title="<?= $txt_tooltip_inside; ?>"><i class="fa fa-question-circle"></i></a></label></div>
						<div><input type="text" id="inside" name="inside" /></div>
					</div>

					<div class="clear"></div>
				</div>

				<h3><?= $txt_header_contact; ?></h3>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="area"><?= $txt_label_phone; ?></label></div>
						<div>
							<input type="text" id="area" name="area" />
							<input type="tel" id="phone" name="phone" />
						</div>
					</div>

					<div class="form-row-half">
						<div><label for="twitter"><?= $txt_label_twitter; ?></label></div>
						<div><input type="text" id="twitter" name="twitter" /></div>
					</div>

					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="website"><?= $txt_label_website; ?></label></div>
						<div><input type="url" id="website" name="website" pattern="https?://.+"></div>
					</div>

					<div class="form-row-half">
						<div><label for="facebook"><?= $txt_label_facebook; ?></label></div>
						<div><input type="text" id="facebook" name="facebook" /></div>
					</div>

					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div class="form-row-half"> &nbsp;
					</div>

					<div class="form-row-half">
						<div><label for="foursq_id"><?= $txt_label_foursquare; ?> <a class="the-tooltip" data-toggle="tooltip" data-placement="top" title="<?= $txt_tooltip_foursquare; ?>"><i class="fa fa-question-circle"></i></a></label></div>
						<div><input type="text" id="foursq_id" name="foursq_id" /></div>
					</div>

					<div class="clear"></div>
				</div>

				<div class="form-row">
					<div class="form-row-full">
						<div><label for="description"><?= $txt_label_description; ?></label></div>
						<div><textarea id="description" name="description" /></textarea></div>
					</div>
				</div>

				<h3><?= $txt_header_categories; ?></h3>

				<div class="form-row">
					<div class="form-row-full">
						<div><select id="category_id" name="category_id">
							<option value="">select category</option>
							<?php get_children(0, 0, 0, $conn); ?>
						</select></div>
						<div class="alert-bubble" id="validate-category_id"><?= $txt_validate_category_id; ?></div>
					</div>
				</div>

				<!-- custom fields -->
				<?php require_once($plugin_dir . '/custom_fields/user-form-block.php'); ?>
				<!-- end custom fields plugin -->

				<h3><?= $txt_header_hours; ?></h3>

				<div id="selected-hours">

				</div>

				<div class="form-row">
					<select class="hours-control" id="hours-weekday">
						<option value="0"><?= $txt_week_mon; ?></option>
						<option value="1"><?= $txt_week_tue; ?></option>
						<option value="2"><?= $txt_week_wed; ?></option>
						<option value="3"><?= $txt_week_thu; ?></option>
						<option value="4"><?= $txt_week_fri; ?></option>
						<option value="5"><?= $txt_week_sat; ?></option>
						<option value="6"><?= $txt_week_sun; ?></option>
					</select>

					<select class="hours-control" id="hours-start">
						<option value="0000">12:00 am (midnight)</option>
						<option value="0030">12:30 am </option>
						<option value="0100">1:00 am </option>
						<option value="0130">1:30 am </option>
						<option value="0200">2:00 am </option>
						<option value="0230">2:30 am </option>
						<option value="0300">3:00 am </option>
						<option value="0330">3:30 am </option>
						<option value="0400">4:00 am </option>
						<option value="0430">4:30 am </option>
						<option value="0500">5:00 am </option>
						<option value="0530">5:30 am </option>
						<option value="0600">6:00 am </option>
						<option value="0630">6:30 am </option>
						<option value="0700">7:00 am </option>
						<option value="0730">7:30 am </option>
						<option value="0800">8:00 am </option>
						<option value="0830">8:30 am </option>
						<option value="0900" selected="">9:00 am </option>
						<option value="0930">9:30 am </option>
						<option value="1000">10:00 am </option>
						<option value="1030">10:30 am </option>
						<option value="1100">11:00 am </option>
						<option value="1130">11:30 am </option>
						<option value="1200">12:00 pm (noon)</option>
						<option value="1230">12:30 pm </option>
						<option value="1300">1:00 pm </option>
						<option value="1330">1:30 pm </option>
						<option value="1400">2:00 pm </option>
						<option value="1430">2:30 pm </option>
						<option value="1500">3:00 pm </option>
						<option value="1530">3:30 pm </option>
						<option value="1600">4:00 pm </option>
						<option value="1630">4:30 pm </option>
						<option value="1700">5:00 pm </option>
						<option value="1730">5:30 pm </option>
						<option value="1800">6:00 pm </option>
						<option value="1830">6:30 pm </option>
						<option value="1900">7:00 pm </option>
						<option value="1930">7:30 pm </option>
						<option value="2000">8:00 pm </option>
						<option value="2030">8:30 pm </option>
						<option value="2100">9:00 pm </option>
						<option value="2130">9:30 pm </option>
						<option value="2200">10:00 pm </option>
						<option value="2230">10:30 pm </option>
						<option value="2300">11:00 pm </option>
						<option value="2330">11:30 pm </option>
					</select>

					<select class="hours-control" id="hours-end">
						<option value="0000">12:00 am (midnight)</option>
						<option value="0030">12:30 am </option>
						<option value="0100">1:00 am </option>
						<option value="0130">1:30 am </option>
						<option value="0200">2:00 am </option>
						<option value="0230">2:30 am </option>
						<option value="0300">3:00 am </option>
						<option value="0330">3:30 am </option>
						<option value="0400">4:00 am </option>
						<option value="0430">4:30 am </option>
						<option value="0500">5:00 am </option>
						<option value="0530">5:30 am </option>
						<option value="0600">6:00 am </option>
						<option value="0630">6:30 am </option>
						<option value="0700">7:00 am </option>
						<option value="0730">7:30 am </option>
						<option value="0800">8:00 am </option>
						<option value="0830">8:30 am </option>
						<option value="0900">9:00 am </option>
						<option value="0930">9:30 am </option>
						<option value="1000">10:00 am </option>
						<option value="1030">10:30 am </option>
						<option value="1100">11:00 am </option>
						<option value="1130">11:30 am </option>
						<option value="1200">12:00 pm (noon)</option>
						<option value="1230">12:30 pm </option>
						<option value="1300">1:00 pm </option>
						<option value="1330">1:30 pm </option>
						<option value="1400">2:00 pm </option>
						<option value="1430">2:30 pm </option>
						<option value="1500">3:00 pm </option>
						<option value="1530">3:30 pm </option>
						<option value="1600">4:00 pm </option>
						<option value="1630">4:30 pm </option>
						<option value="1700" selected="">5:00 pm </option>
						<option value="1730">5:30 pm </option>
						<option value="1800">6:00 pm </option>
						<option value="1830">6:30 pm </option>
						<option value="1900">7:00 pm </option>
						<option value="1930">7:30 pm </option>
						<option value="2000">8:00 pm </option>
						<option value="2030">8:30 pm </option>
						<option value="2100">9:00 pm </option>
						<option value="2130">9:30 pm </option>
						<option value="2200">10:00 pm </option>
						<option value="2230">10:30 pm </option>
						<option value="2300">11:00 pm </option>
						<option value="2330">11:30 pm </option>
					</select>

					<button class="btn-hours-control" type="button" id="btn-add-hours"><?= $txt_btn_add_hours; ?></button>
				</div>

				<div class="form-row">
					<div class="form-row-half">
						<div><label for="hours_note"><?= $txt_label_hours_notes; ?> <a class="the-tooltip" data-toggle="tooltip" data-placement="top" title="<?= $txt_tooltip_hours_note; ?>"><i class="fa fa-question-circle"></i></a></label></div>
						<div><input type="text" id="business_hours_info" name="business_hours_info" /></div>
					</div>

					<div class="form-row-half"> &nbsp;
					</div>

					<div class="clear"></div>
				</div>

				<h3><?= $txt_header_photos; ?></h3>

				<div class="form-row">
					<div><label><?= $txt_form_upload; ?></label></div>
					<div><a id="upload-button" class="btn btn-default"><?= $txt_form_upload_button; ?></a></div>
				</div>

				<div class="form-row">
					<div id="uploaded">
						<!-- uploaded pics -->
					</div>
				</div>

				<div class="form-row submit-row">
					<div><input type="submit" id="submit_button" name="submit_button" value="<?= $txt_form_submit_button; ?>" class="btn btn-blue" /></div>
				</div>
			</form>
		</div><!-- .form-wrapper -->
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/_user_footer.php'); ?>

<!-- Ajax upload -->
<script src="<?= $baseurl; ?>/lib/ajaxupload/ajaxupload.js"></script>
<script>
new AjaxUpload('upload-button', {
	action      : '<?= $baseurl; ?>/user/process-upload.php',
	name        : 'userfile',
	data        : {},
	autoSubmit  : true,
	responseType: false,
	onChange    : function(file, extension){},
	onSubmit    : function(file, ext) {
		// Allow only images. You should add security check on the server-side.
		// Add preloader
		if (ext && /^(jpg|png|jpeg|gif)$/i.test(ext)) {
			$('<div class="thumbs-preloader" id="preloader"><i class="fa fa-spinner fa-spin"></i></div>').appendTo('#uploaded');
			// count number of div.thumbs
			var index = $('.thumbs').length;

			// add 1 to index on submit upload
			index = index + 1;

			// disable upload button after max upload limit is reached
			if(index == <?= $max_pics; ?>) {
				$('#upload_button').text('Limit');
				this.disable();
			}
		}
		else {
			// extension is not allowed
			$('<div id="upload_failed"></div>').appendTo('#uploaded').text('Invalid file type');

			// cancel upload
			return false;
		}
	},
	onComplete: function(file, response) { // response echoed from  process-upload.php
		// closure
		Uploader = this;

		// debug
		console.log(response);

		// check if previous upload failed because of non allowed ext
		// #upload_failed div created by onSumit function above
		if ($('#upload_failed').length) {
			$('#upload_failed').fadeOut("fast", function() { $(this).remove(); });
		}

		// delete preloader spinner
		$('#preloader').fadeOut("fast", function() { $(this).remove(); });

		if(response == 1) {
			// Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
			$('<div id="upload_failed"></div>').appendTo('#uploaded').text('<?= $txt_error_file_size; ?>');
			// cancel upload
			return false;
		}

		else if(response == 10) {
			// Value: 10; custom error code, failed to move file
			$('<div id="upload_failed"></div>').appendTo('#uploaded').text('<?= $txt_error_upload; ?>');
			// cancel upload
			return false;
		}

		else if(response == 11) {
			// Value: 11; custom error code, no submit token
			$('<div id="upload_failed"></div>').appendTo('#uploaded').text('<?= $txt_error_upload; ?>');
			// cancel upload
			return false;
		}

		else if(response == 12) {
			// Value: 12; custom error code, more than max num pics
			$('<div id="upload_failed"></div>').appendTo('#uploaded').text('Error: number of uploads exceeded (max <?= $max_pics; ?>)');
			// cancel upload
			return false;
		}

		else {  // upload success
			var thumb = '<?= $pic_baseurl; ?>/<?= $place_thumb_folder ?>/' + response;

			// store thumb container div in memory
			var temp_thumb_div = $('<div class="thumbs"></div>');

			// display uploaded pic's thumb
			$('<img />').attr('src', thumb).attr('width', '132').appendTo(temp_thumb_div);
			$('<div class="btn-delete-thumb delete_pic"><i class="fa fa-times-circle-o"></i></div>').appendTo(temp_thumb_div);
			$('<input type="hidden" name="uploads[]" />').attr('value', response).appendTo(temp_thumb_div);
			$('#uploaded').append(temp_thumb_div);

			// unbind click event to previous .delete_pic links and attach again so that the click event is not assigned twice to the same .delete_pic link
			$('.delete_pic').unbind('click');

			// make delete link work
			$('.delete_pic').click(function() {
				// get pic filename from hidden input
				var pic = $(this).next().attr('value');

				// remove div.thumbs
				$(this).parent().fadeOut("fast", function() { $(this).remove(); });

				//
				$('<input type="hidden" name="delete_temp_pics[]" />').attr('value', pic).appendTo('#uploaded');

				// re-enable upload button
				$('#upload_button').text('<?= $txt_form_upload_button; ?>');
				Uploader.enable();
			});
		} // end else
	} // end onComplete
});
</script>
<!-- End Ajax Upload -->

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?= $google_key; ?>"></script>
<script>
var map            = null;
var marker         = null;
var markers        = [];
var update_timeout = null;
var geocoder;

// global infowindow object
var infowindow = new google.maps.InfoWindow( {
	size: new google.maps.Size(150,50)
});

// default latitude and longitude
var defaultLatLng = new google.maps.LatLng(<?= $default_latlng; ?>);

// create the map
var mapOptions = {
	zoom                  : 5,
	center                : defaultLatLng,
	mapTypeControl        : true,
	mapTypeControlOptions : {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
	navigationControl     : true,
	mapTypeId             : google.maps.MapTypeId.ROADMAP
}

// init map
map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

// init geocoder service
geocoder = new google.maps.Geocoder();

// when click on map, add marker
google.maps.event.addListener(map, 'click', function(event){
	deleteMarkers();
	update_timeout = setTimeout(function(){
		if (marker) {
			marker.setMap(null);
			marker = null;
		}

		infowindow.close();

		marker = createMarker(event.latLng, "name", "<b><?= $txt_map_marker_location; ?></b><br>" + event.latLng);
		$("#latlng").val(event.latLng);
	}, 200);
});

// on double click
google.maps.event.addListener(map, 'dblclick', function(event) {
	clearTimeout(update_timeout);
});

// address input on blur listener
document.getElementById('address').addEventListener('blur', function(e){
	update_timeout = setTimeout(function(){
		if (marker) {
			marker.setMap(null);
			marker = null;
		}

		infowindow.close();

		codeAddress();
	}, 200);
});

// postal_code input on blur listener
document.getElementById('postal_code').addEventListener('blur', function(e){
	update_timeout = setTimeout(function(){
		if (marker) {
			marker.setMap(null);
			marker = null;
		}

		infowindow.close();

		codeAddress();
	}, 200);
});
</script>
<!-- End Google Maps API -->

<!-- Select2 Library -->
<script>
// initialize Select2 on the <select> element that you want to make awesome.
$('#category_id').select2({
	placeholder: '<?= $txt_placeholder_select_cat; ?>'
});

$('#city_id').select2({
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
	minimumInputLength: 1
});
</script>
<!-- End Select2 Library -->

<!-- Hours Widget -->
<script>
$('#btn-add-hours').click(function() {
	// get values from select fields
	var hours_weekday     = $('#hours-weekday').val();
	var hours_weekday_txt = $('#hours-weekday option:selected').text();
	var hours_start       = $('#hours-start').val();
	var hours_start_txt   = $('#hours-start option:selected').text();
	var hours_end         = $('#hours-end').val();
	var hours_end_txt     = $('#hours-end option:selected').text();
	var hours             = hours_weekday + ',' + hours_start + ',' + hours_end;

	var div_row = '<div class="hours-row"><span class="weekday"><strong>';
	div_row += hours_weekday_txt;
	div_row += '</strong></span> <span class="start">';
	div_row += hours_start_txt;
	div_row += '</span><span>-</span><span class="end">';
	div_row += hours_end_txt;
	div_row += '</span><a class="remove-hours"><i class="fa fa-times"></i></a>';
	div_row += '<input type="hidden" name="business_hours[]" value="' + hours + '"></div>';
	$(div_row).appendTo('#selected-hours');

	$('.remove-hours').click(function() {
		$(this).parent().fadeOut("fast", function() { $(this).remove(); });
	});
}); // end #btn-add-hours click

$('.remove-hours').click(function() {
	$(this).parent().fadeOut("fast", function() { $(this).remove(); });
});
</script>
<!-- End Hours Widget -->

<!-- Delete Pics -->
<script>
	$('.delete_existing_pic').click(function() {
		// get pic filename from hidden input
		var pic = $(this).next().attr('value');

		// remove div.thumbs
		$(this).parent().fadeOut("fast", function() { $(this).remove(); });

		//
		$('<input type="hidden" name="delete_existing_pics[]" />').attr('value', pic).appendTo('#uploaded');

		// re-enable upload button
		$('#upload_button').text('<?= $txt_form_upload_button; ?>');

		Uploader.enable();
	});
</script>
<!-- End Delete Pics -->

<!-- Form Submit -->
<script>
$("#the_form").submit(function() {

});
</script>
<!-- End Form Submit -->

<!-- Tooltips -->
<script>
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
<!-- End Tooltips -->

<!-- Bootstrap modifications -->
<script>
// Allow Bootstrap dropdown menus to have forms/checkboxes inside,
// and when clicking on a dropdown item, the menu doesn't disappear.
$(document).on('click', '.dropdown-menu.dropdown-menu-form', function(e) {
	e.stopPropagation();
});
</script>
<!-- End Bootstrap modifications -->

<!-- Custom Functions -->
<script>
// A function to create the marker and set up the event window function
function createMarker(latlng, name, html) {
	var contentString = html;
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		zIndex: Math.round(latlng.lat()*-100000)<<5
		});

	google.maps.event.addListener(marker, 'click', function() {
		infowindow.setContent(contentString);
		infowindow.open(map,marker);
		});

	google.maps.event.trigger(marker, 'click');
	return marker;
}

// address input, on blur, set marker
function codeAddress() {
	deleteMarkers();
	var address     = document.getElementById("address").value;
	var postal_code = document.getElementById("postal_code").value;
	var address_postal_code = address + ' ' + postal_code;
	geocoder.geocode( { 'address': address_postal_code}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			map.setZoom(12);
			var marker = new google.maps.Marker({
				map     : map,
				position: results[0].geometry.location
			});

			// add this marker to the array
			markers.push(marker);

			// update hiddent latlng input field
			$("#latlng").val(results[0].geometry.location);

		} else {
			console.log("Geocode was not successful for the following reason: " + status);
		}
	});
}

// Sets the map on all markers in the array.
function setMapOnAll(map) {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(map);
	}
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
	setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
	setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
	clearMarkers();
	markers = [];
}
</script>
<!-- End Custom Functions -->
</body>
</html>
