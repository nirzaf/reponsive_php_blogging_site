<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html class="no-js" lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="description" content="<?= $txt_meta_desc; ?>" />
<meta name="robots" content="noindex">
<?php require_once('_html_head.php'); ?>
</head>
<body class="tpl-searchresults">
<?php require_once('_header.php'); ?>

<div class="wrapper">
	<h1><?= $txt_main_title; ?></h1>

	<div class="full-block">
		<div class="content-col">
			<div class="list-items">
				<?php
				/*
				--------------------------------------------------
				BEGIN SHOW LIST
				--------------------------------------------------
				*/
				$results_arr = array();
				if($total_rows > 0) {
					$count = ($page - 1) * $limit;

					foreach($list_items as $k => $v) {
						$count++;
						$place_id         = $v['place_id'];
						$place_name       = $v['place_name'];
						$place_slug       = $v['place_slug'];
						$address          = $v['address'];
						$cross_street     = $v['cross_street'];
						$place_city_name  = $v['place_city_name'];
						$place_city_slug  = $v['place_city_slug'];
						$place_state_abbr = $v['place_state_abbr'];
						$postal_code      = $v['postal_code'];
						$area_code        = $v['area_code'];
						$phone            = $v['phone'];
						$lat              = $v['lat'];
						$lng              = $v['lng'];
						$cat_icon         = $v['cat_icon'];
						$photo_url        = $v['photo_url'];
						$rating           = $v['rating'];
						$description      = $v['description'];

						$results_arr[] = array(
							'ad_id'    => $place_id,
							'ad_lat'   => $lat,
							'ad_lng'   => $lng,
							'ad_title' => $place_name,
							'count'    => $count,
							'cat_icon' => $cat_icon);
						$places_names_arr[] = $place_name;
						?>
						<div class="item" data-ad_id="<?= $place_id; ?>">
							<div class="item-pic" id="<?= $place_id; ?>">
								<img src="<?= $photo_url; ?>" />
							</div><!-- .item-pic -->

							<div class="item-description">
								<div class="item-title-row">
									<div class="item-counter"><div class="item-counter-inner"><?= $count; ?></div></div>

									<h2><a href="<?= $baseurl; ?>/<?= $place_city_slug; ?>/place/<?= $place_id; ?>/<?= $place_slug; ?>" title="<?= $place_name; ?>"><?= $place_name; ?></a></h2>
								</div>
								<div class="item-ratings-wrapper">
									<div class="item-rating" data-rating="<?= $rating; ?>">
										<!-- raty plugin placeholder -->
									</div>
									<div class="item-ratings-count">
										<?php // echo $count_rating; ?> <?php // echo ($count_rating == 1 ? 'review' : 'reviews'); ?>
									</div>
									<div class="clear"></div>
								</div><!-- .item-ratings-wrapper -->
								<div class="item-info">
									<div class="item-addr">
										<strong>
											<?= (!empty($address)) ? $address : ''; ?>
										</strong>
										<?= (!empty($cross_street)) ? "($cross_street)" : ''; ?>
										<br>
										<strong>
											<?= (!empty($place_city_name))  ? "$place_city_name," : ''; ?>
											<?= (!empty($place_state_abbr)) ? " $place_state_abbr " : ''; ?>
											<?= (!empty($postal_code))      ? $postal_code : ''; ?>
										</strong>
									</div>

									<div class="item-phone">
										<?= (!empty($phone)) ? '<i class="fa fa-phone-square"></i>' : ''; ?>
										<?= (!empty($area_code)) ? $area_code : ''; ?>
										<?= (!empty($phone)) ? $phone : ''; ?>
									</div>
								</div><!-- .item-info -->

								<?php
								echo (!empty($tip_text)) ? $tip_text : '';
								?>
							</div><!-- .item-description -->

							<div class="clear"></div>
						</div><!-- .item  -->
						<?php
					} //  end foreach($response as $k => $v)
				} // end if($total_rows > 0)
			else {
				// else no results found
				?>
				<div class="empty-cat-template">
					<p><?= $txt_empty_results; ?></p>
				</div>
				<?php
			}
			?>
			</div><!-- .list-items -->

			<?php
			/*
			--------------------------------------------------
			BEGIN PAGER
			--------------------------------------------------
			*/
			?>
			<div id="pager">
				<ul class="pagination">
					<?php
					if(!empty($pager) && $pager->getTotalPages() > 1) {
						$curPage = $page;

						$startPage = ($curPage < 5)? 1 : $curPage - 4;
						$endPage = 8 + $startPage;
						$endPage = ($pager->getTotalPages() < $endPage) ? $pager->getTotalPages() : $endPage;
						$diff = $startPage - $endPage + 8;
						$startPage -= ($startPage - $diff > 0) ? $diff : 0;

						$startPage = ($startPage == 1) ? 2 : $startPage;
						$endPage = ($endPage == $pager->getTotalPages()) ? $endPage - 1 : $endPage;

						if($total_rows > 0) {
							$page_url = "$baseurl/_searchresults.php?city_id=$query_city_id&query=$user_query&page=";

							if ($curPage > 1) {
								?>
								<li><a href="<?= $page_url; ?>1">Page 1</a></li>
								<?php
							}

							if ($curPage > 6) {
								?>
								<li><span>...</span></li>
								<?php
							}

							if ($curPage == 1) {
								?>
								<li class="active"><span>Page 1</span></li>
								<?php
							}

							for($i = $startPage; $i <= $endPage; $i++) {
								if($i == $page) {
									?>
									<li class="active"><span><?= $i; ?></span></li>
									<?php
								}
								else {
									?>
									<li><a href="<?php echo $page_url, $i; ?>"><?= $i; ?></a></li>
									<?php
								}
							}

							if($curPage + 5 < $pager->getTotalPages()) {
								?>
								<li><span>...</span></li>
								<?php
							}

							if($pager->getTotalPages() > 5) {
								$last_page_txt = "Last Page";
							}

							$last_page_txt = ($pager->getTotalPages() > 5) ? "Last Page" : $pager->getTotalPages();

							if($curPage == $pager->getTotalPages()) {
								?>
								<li class="active"><span><?= $last_page_txt; ?></span></li>
								<?php
							}
							else {
								?>
								<li><a href="<?php echo $page_url, $pager->getTotalPages(); ?>"><?= $last_page_txt; ?></a></li>
								<?php
							}
						} //  end if($total_rows > 0)
					} //  end if(isset($pager) && $pager->getTotalPages() > 1)

					if(isset($pager) && $pager->getTotalPages() == 1) {
						?>

						<?php
					}
					?>
				</ul>
			</div><!-- #pager -->
		</div><!-- .content-col -->
		<div class="sidebar">
			<?php
			if($total_rows > 0) {
				?>
				<div class="map-wrapper" id="sticker">
					<div id="map-canvas" style="width:100%; height:100%"></div>
				</div>
				<?php
			}
			?>
		</div><!-- #sidebar -->

		<div class="clear"></div>
	</div><!-- .content-full -->

</div><!-- .wrapper -->
<?php require_once('_footer.php'); ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= $google_key; ?>"></script>
<script src="<?= $baseurl; ?>/templates/lib/sticky/jquery.sticky.js"></script>

<!-- place markers -->
<script>
var results_obj = <?php echo json_encode($results_arr); ?>;
var infowindow;
var map;

function initialize() {
	markers = {};
	infoboxcontents = {};

	// set map options
	var mapOptions = {
		zoom: 5,
		maxZoom: 15
	};

	// instantiate map
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	var bounds = new google.maps.LatLngBounds();
	var infowindow = new google.maps.InfoWindow();

	// $results_arr[] = array("ad_id" => $place_id, "ad_lat" => $ad_lat, "ad_lng" => $ad_lng, "ad_title" => $ad_title, "count" => $count);

	// set markers
	for (var k in results_obj) {
		var p = results_obj[k];
		var latlng = new google.maps.LatLng(p.ad_lat, p.ad_lng);
		bounds.extend(latlng);

		var marker_icon = '<?= $baseurl; ?>/imgs/marker1.png';

		// place markers
		var marker = new google.maps.Marker({
			position: latlng,
			map: map,
			animation: google.maps.Animation.DROP,
			title: p.ad_title,
			//icon: marker_icon
		});

		markers[p.ad_id] = marker;
		infoboxcontents[p.ad_id] = p.ad_title;

		// click event on markers to show infowindow
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.setContent(this.title);
			infowindow.open(map, this);
		});
	} // end for (var k in results_obj)

	map.fitBounds(bounds);

	$(".list-items .item").mouseover(function() {
		marker = markers[this.getAttribute("data-ad_id")];
		// mycontent = infoboxcontents[this.getAttribute("data-ad_id")];

		mycontent =  '<div class="scrollFix">' + infoboxcontents[this.getAttribute("data-ad_id")] + '</div>';
		// console.log(mycontent);

		infowindow.setContent(mycontent);
		// infowindow.setOptions({maxWidth:300});
		infowindow.open(map, marker);
		marker.setZIndex(10000);
	}); // end mouseover
} //  end initialize()

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<!-- raty -->
<script>
$.fn.raty.defaults.path = '<?= $baseurl; ?>/templates/lib/raty/images';
$('.item-rating').raty({
	readOnly: true,
	score: function(){
		return this.getAttribute('data-rating');
	}
});
</script>

<!-- sidebar sticky -->
<script>
$(document).ready(function(){
	$("#sidebar").sticky({topSpacing:0});
});

$(document).ready(function(){
	$("#sticker").sticky({topSpacing: 24});
});
</script>

<!-- functions -->
<script>
function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
</script>

<!-- preload search values -->
<script>
$(document).ready(function() {
	var query = '<?= e($user_query); ?>';

	if(query !== '') {
		$("#query-input").val(query);
	}

	<?php
	if(!empty($query_city_name) && !empty($query_state_abbr)) {
		?>
		$('#city-input').append('<option value="<?= $query_city_id; ?>" selected="selected"><?= $query_city_name; ?>,  <?= $query_state_abbr; ?></option>');
		$('#city-input').trigger('change');
		<?php
	}
	?>
});
</script>
</body>
</html>