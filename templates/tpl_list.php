<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="description" content="<?= $txt_meta_desc; ?>" />
<link rel="canonical" href="<?= $canonical; ?>" />
<?php
if($total_rows < 1 || (isset($dont_index) && $dont_index === true)) {
	?><meta name="robots" content="noindex"><?php
}
?>
<?php require_once('_html_head.php'); ?>

</head>
<body class="tpl-list">
<?php require_once('_header.php'); ?>
<div class="wrapper">
	<h1><?= $txt_html_title; ?></h1>

	<div class="breadcrumbs">
		<?php
		if(!empty($breadcrumbs)) {
			?><a href="<?= $baseurl; ?>/"><?= $txt_breadcrumb_home; ?></a> >
			<?= $breadcrumbs; ?>
		<?php
		}
		?>
		<span id="total-rows"></span>
	</div><!-- .breadcrumbs -->

	<?php
	// populate results count line
	if($total_rows > 0) {
		?>
		<script>
			var total_rows = document.getElementById('total-rows');
			total_rows.innerHTML = '<span class="results-count"><?= $total_rows; ?> <?= $txt_results; ?></span>';
		</script>
		<?php
	}
	?>

	<div class="subcats block">
		<?php
		if(!empty($subcats) && isset($subcats)) {
			foreach ($subcats as $v) {
				$this_cat_id   = $v['cat_id'];
				$this_cat_name = $v['cat_name'];
				$this_cat_slug = to_slug($this_cat_name);
				?>
				<a href="<?= $baseurl; ?>/<?= $loc_slug; ?>/list/<?= $this_cat_slug; ?>/<?= $loc_type; ?>-<?= $loc_id; ?>-<?= $this_cat_id; ?>-1" title="" class="btn btn-even-less-padding btn-default"><?= $this_cat_name; ?></a>
				<?php
			}
		}
		?>
	</div><!-- .subcats -->

	<div class="full-block">
		<div class="content-col">
			<div class="list-items">
				<?php
				foreach($list_items as $k => $v) {
					$place_id         = $v['place_id'];
					$place_name       = $v['place_name'];
					$rating           = $v['rating'];
					$photo_url        = $v['photo_url'];
					$address          = $v['address'];
					$cross_street     = $v['cross_street'];
					$place_city_name  = $v['city_name'];
					$place_city_slug  = $v['city_slug'];
					$place_state_abbr = $v['state_abbr'];
					$postal_code      = $v['postal_code'];
					$area_code        = $v['area_code'];
					$phone            = $v['phone'];
					$tip_text         = $v['tip_text'];
					$is_feat          = $v['is_feat'];
					$div_class        = ($is_feat == 1) ? 'featured-li' : '';
					$count++;
					?>
					<div class="item <?= $div_class; ?>" data-ad_id="<?= $place_id; ?>">
						<div class="item-pic" id="pic-<?= $place_id; ?>">
							<a href="<?= $baseurl; ?>/<?= $place_city_slug; ?>/place/<?= $place_id; ?>/<?php echo to_slug($place_name); ?>" title="<?= $place_name; ?>"><img src="<?= $photo_url; ?>" /></a>
						</div><!-- .item-pic -->

						<div class="item-description">
							<div class="item-title-row">
								<div class="item-counter"><div class="item-counter-inner"><?= $count; ?></div></div>

								<h2><a href="<?= $baseurl; ?>/<?= $place_city_slug; ?>/place/<?= $place_id; ?>/<?php echo to_slug($place_name); ?>" title="<?= $place_name; ?>"><?= $place_name; ?></a></h2>
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
							if(isset($tip_text)) {
								echo $tip_text;
							}
							?>
						</div><!-- .item-description -->

						<div class="clear"></div>
					</div><!-- .item  -->
					<?php
				} //  end foreach($response as $k => $v)
				?>
			</div><!-- .list-items -->

			<?php
			if(empty($list_items)) {
				?>
				<div class="empty-cat-template">
					<p><?= $txt_temp_empty_msg_1; ?></p>
					<p><?= $txt_temp_empty_msg_2; ?></p>
					<div><a href="<?= $baseurl; ?>/user/select-plan" class="btn btn-blue" /><?= $txt_temp_empty_msg_3; ?></a></div>
				</div>
				<?php
			}
			?>
			<!-- begin pagination -->
			<div id="pager">
				<ul class="pagination">
					<?= $pager_template; ?>
				</ul>
			</div><!-- #pager -->
		</div><!-- .content-col -->
		<div class="sidebar">
			<?php
			if(!empty($list_items)) {
				?>
				<div class="map-wrapper" id="sticker">
					<div id="map-canvas" style="width:100%; height:100%"></div>
				</div>
				<?php
			}
			?>
		</div><!-- .sidebar -->

		<div class="clear"></div>
	</div><!-- .full-block -->
</div><!-- .wrapper -->
<?php require_once('_footer.php'); ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= $google_key; ?>"></script>
<script src="<?= $baseurl; ?>/templates/lib/sticky/jquery.sticky.js"></script>

<?php
if(!empty($results_arr)) {
	?>
	<script type="text/javascript">
	// place markers
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
		var map        = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		var bounds     = new google.maps.LatLngBounds();
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
	<?php
}
?>

<script type="text/javascript">
$.fn.raty.defaults.path = '<?= $baseurl; ?>/templates/lib/raty/images';
$('.item-rating').raty({
	readOnly: true,
	score: function(){
		return this.getAttribute('data-rating');
	}
});
</script>

<script>
$(document).ready(function(){
	if($('.full-block').innerWidth() > 760) {
		$("#sticker").sticky({topSpacing: 24});
	}

	else {
		$('.map-wrapper').attr('id', 'sticker-removed');
	}
});

</script>
</body>
</html>