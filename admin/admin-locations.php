<?php
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/_admin_inc.php'); // checks session and user id; die() if not admin
require_once($lang_folder . '/admin_translations/trans-locations.php');

// path info
$frags = '';
if(!empty($_SERVER['PATH_INFO'])) {
	$frags = $_SERVER['PATH_INFO'];
}
else {
	if(!empty($_SERVER['ORIG_PATH_INFO'])) {
		$frags = $_SERVER['ORIG_PATH_INFO'];
	}
}
// frags still empty
if(empty($frags)) {
	$frags = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
}
$frags = explode("/", $frags);

// paging vars
$page = !empty($frags[3]) ? $frags[3] : 1;
$limit = $items_per_page;
if($page > 1) {
	$offset = ($page-1) * $limit + 1;
}
else {
	$offset = 1;
}

// show what
$show = !empty($frags[1]) ? $frags[1] : 'show-cities';

// init total_rows
$total_rows_cities    = '';
$total_rows_states    = '';
$total_rows_countries = '';

$total_rows = $total_rows_cities;
if($show == 'show-cities') {
	// count how many cities
	$query = "SELECT COUNT(*) AS total_rows FROM cities";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows_cities = $row['total_rows'];
	$total_rows = $total_rows_cities;

	$cities_arr = array();

	if($total_rows_cities > 0) {
		$pager = new DirectoryApp\PageIterator($limit, $total_rows_cities, $page);
		$start = $pager->getStartRow();

		// select all cities information and put in an array
		$query = "SELECT cities.*, cities_feat.city_id AS feat FROM cities
		LEFT JOIN cities_feat ON cities.city_id = cities_feat.city_id
		ORDER BY city_name LIMIT :start, :limit";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':start', $start);
		$stmt->bindValue(':limit', $limit);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$cur_loop_arr = array(
				'city_id'    => $row['city_id'],
				'city_name'  => $row['city_name'],
				'state_abbr' => $row['state'],
				'state_id'   => $row['state_id'],
				'city_slug'  => $row['slug'],
				'is_feat'    => $row['feat']
			);
			$cities_arr[] = $cur_loop_arr;
		}
	}
}

else if($show == 'show-states') {
	// count how many states
	$query = "SELECT COUNT(*) AS total_rows FROM states";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows_states = $row['total_rows'];
	$total_rows = $total_rows_states;

	$states_arr = array();

	if($total_rows_states > 0) {
		$pager = new DirectoryApp\PageIterator($limit, $total_rows_states, $page);
		$start = $pager->getStartRow();

		// select all states information and put in an array
		$query = "SELECT * FROM states ORDER BY state_name LIMIT :start, :limit";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':start', $start);
		$stmt->bindValue(':limit', $limit);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$cur_loop_arr = array(
				'state_id'     => $row['state_id'],
				'state_name'   => $row['state_name'],
				'state_abbr'   => $row['state_abbr'],
				'state_slug'   => $row['slug'],
				'country_abbr' => $row['country_abbr'],
				'country_id'   => $row['country_id']
			);
			$states_arr[] = $cur_loop_arr;
		}
	}
}

// else show countries
else {
	// count how many countries
	$query = "SELECT COUNT(*) AS total_rows FROM countries";
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_rows_countries = $row['total_rows'];
	$total_rows = $total_rows_countries;

	$countries_arr = array();

	if($total_rows_countries > 0) {
		$pager = new DirectoryApp\PageIterator($limit, $total_rows_countries, $page);
		$start = $pager->getStartRow();

		// select all states information and put in an array
		$query = "SELECT * FROM countries ORDER BY country_name LIMIT :start, :limit";
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':start', $start);
		$stmt->bindValue(':limit', $limit);
		$stmt->execute();
		while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$cur_loop_arr = array(
				'country_id'   => $row['country_id'],
				'country_name' => $row['country_name'],
				'country_abbr' => $row['country_abbr'],
				'country_slug' => $row['slug']
			);
			$countries_arr[] = $cur_loop_arr;
		}
	}
}
?>
<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>" > <![endif]-->
<html lang="<?= $html_lang; ?>" >
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_admin_html_head.php'); ?>
<style>

</style>
</head>
<body class="admin-locations">
<?php require_once(__DIR__ . '/_admin_header.php'); ?>
<div class="wrapper">
	<div class="menu-box">
		<?php require_once(__DIR__ . '/_admin_menu.php'); ?>
	</div>

	<div class="main-container">
		<h2><?= $txt_main_title; ?></h2>

		<div class="padding">
			<div class="block">
				<strong><?= $txt_show; ?>:</strong><br>
				<a href="<?= $baseurl; ?>/admin/admin-locations/show-cities/" class="btn btn-default btn-less-padding"><?= $txt_cities; ?></a>
				<a href="<?= $baseurl; ?>/admin/admin-locations/show-states/" class="btn btn-default btn-less-padding"><?= $txt_states; ?></a>
				<a href="<?= $baseurl; ?>/admin/admin-locations/show-countries/" class="btn btn-default btn-less-padding"><?= $txt_countries; ?></a>
			</div>

			<div class="block">
				<strong><?= $txt_action; ?>:</strong><br>
				<a href="" class="create-loc-btn btn btn-blue btn-less-padding"
					data-loc-type="city"
					data-modal-title="<?= $txt_create_city; ?>"
					data-toggle="modal"
					data-target="#create-loc-modal"
					><?= $txt_create_city; ?></a>
				<a href="" class="create-loc-btn btn btn-blue btn-less-padding"
					data-loc-type="state"
					data-modal-title="<?= $txt_create_state; ?>"
					data-toggle="modal"
					data-target="#create-loc-modal"
					><?= $txt_create_state; ?></a>
				<a href="" class="create-loc-btn btn btn-blue btn-less-padding"
					data-loc-type="country"
					data-modal-title="<?= $txt_create_country; ?>"
					data-toggle="modal"
					data-target="#create-loc-modal"
					><?= $txt_create_country; ?></a>
			</div>

			<?php
			if($show == 'show-cities') {
				// get list of states to use in jinplace drop down
				$query = "SELECT state_id, state_abbr FROM states";
				$stmt = $conn->prepare($query);
				$stmt->execute();

				$state_select_arr = array();

				$country_select_arr = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$cur_loop_arr = array(
						$row['state_id'], $row['state_abbr']
					);
					$state_select_arr[] = $cur_loop_arr;
				}

				$state_select_json = json_encode($state_select_arr);
				?>
				<span><?= $txt_total_rows; ?>: <strong><?= $total_rows_cities; ?></strong></span>
				<div class="table-responsive">
					<table class="table admin-table">
						<tr>
							<th style="width:15%;min-width:60px;"><?= $txt_city_id; ?></th>
							<th style="width:45%"><?= $txt_city_name; ?></th>
							<th style="width:20%;min-width:84px;"><?= $txt_state; ?></th>
							<th style="width:20%;min-width:80px;"><?= $txt_action; ?></th>
						</tr>

						<?php
						foreach($cities_arr as $k => $v) {
							$city_id    = $v['city_id'];
							$city_name  = $v['city_name'];
							$state_abbr = $v['state_abbr'];
							$state_id   = $v['state_id'];
							$city_slug  = $v['city_slug'];
							$is_feat    = $v['is_feat'];

							// sanitize
							$city_name = e($city_name);
							$city_slug = e($city_slug);
							?>
							<tr id="tr-city-<?= $city_id; ?>">
								<td><?= $city_id; ?></td>
								<td class="nowrap">
									<span class="btn btn-default btn-less-padding" id="activator-<?= $v['city_id']; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-<?= $city_id; ?>"
										data-attribute="city"
										data-object="<?= $city_id; ?>">
										<?= $city_name; ?>
									</div>
								</td>
								<td class="nowrap">
									<span class="btn btn-default btn-less-padding" id="activator-state-dropdown-for-<?= $city_id; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-placeholder="select"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-state-dropdown-for-<?= $city_id; ?>"
										data-type="select"
										data-attribute="city_state"
										data-object="<?= $city_id; ?>"
										data-data='<?= $state_select_json; ?>'>
										<?= $state_abbr; ?>
									</div>
								</td>
								<td class="nowrap">
									<!-- featured_home city toggle -->
									<?php
									if(is_null($is_feat)) {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_featured; ?>">
											<a href="#" class="btn btn-default btn-less-padding featured-home"
												id="featured-home-<?= $city_id; ?>"
												data-city-id="<?= $city_id; ?>"
												data-city-status="not_featured">
												&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									else {
										?>
										<span data-toggle="tooltip"	title="<?= $txt_toggle_featured; ?>">
											<a href="#" class="btn btn-green btn-less-padding featured-home"
												id="featured-home-<?= $city_id; ?>"
												data-city-id="<?= $city_id; ?>"
												data-city-status="featured">
												&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;
											</a>
										</span>
										<?php
									}
									?>

									<!-- remove city -->
									<span data-toggle="tooltip" title="<?= $txt_remove_city; ?>">
										<a href="#" class="btn btn-default btn-less-padding remove-loc"
											data-loc-id="<?= $city_id; ?>"
											data-loc-type="city">
											<i class="fa fa-trash"></i>
										</a>
									</span>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			<?php
			} // end if(!empty($cats_arr))
			?>

			<?php
			if($show == 'show-states') {
				// get list of countries to use in jinplace drop down
				$query = "SELECT country_id, country_abbr FROM countries";
				$stmt = $conn->prepare($query);
				$stmt->execute();

				$country_select_arr = array();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$cur_loop_arr = array(
						$row['country_id'], $row['country_abbr']
					);
					$country_select_arr[] = $cur_loop_arr;
				}

				$country_select_json = json_encode($country_select_arr);
				?>
				<span><?= $txt_total_rows; ?>: <strong><?= $total_rows_states; ?></strong></span>
				<div class="table-responsive">
					<table class="table responsive nowrap admin-table">
						<tr>
							<th><?= $txt_state_id; ?></th>
							<th><?= $txt_state_name; ?></th>
							<th><?= $txt_country; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($states_arr as $k => $v) {
							$state_id     = $v['state_id'];
							$state_name   = $v['state_name'];
							$state_slug   = $v['state_slug'];
							$country_abbr = $v['country_abbr'];
							$country_id   = $v['country_id'];

							// sanitize
							$state_name = e($state_name);
							$state_slug = e($state_slug);
							?>
							<tr id="tr-state-<?= $state_id; ?>">
								<td><?= $state_id; ?></td>
								<td>
									<span class="btn btn-default btn-less-padding" id="activator-state-<?= $state_id; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-state-<?= $state_id; ?>"
										data-attribute="state"
										data-object="<?= $state_id; ?>">
										<?= $state_name; ?>
									</div>
								</td>
								<td>
									<span class="btn btn-default btn-less-padding" id="activator-country-dropdown-for-<?= $state_id; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-placeholder="select"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-country-dropdown-for-<?= $state_id; ?>"
										data-type="select"
										data-attribute="state_country"
										data-object="<?= $state_id; ?>"
										data-data='<?= $country_select_json; ?>'>
										<?= $country_abbr; ?>
									</div>
								</td>
								<td>
									<span data-toggle="tooltip" title="<?= $txt_remove_state; ?>">
										<a href="" class="btn btn-default btn-less-padding remove-loc"
											data-loc-id="<?= $state_id; ?>"
											data-loc-type="state">
											<i class="fa fa-trash"></i>
										</a>
									</span>
								</td>
							</tr>
							<?php

						}
						?>
					</table>
				</div>
			<?php
			} // end if(!empty($states_arr))
			?>

			<?php
			if($show == 'show-countries') {
				?>
				<span><?= $txt_total_rows; ?>: <strong><?= $total_rows_countries; ?></strong></span>
				<div class="table-responsive">
					<table class="table responsive nowrap admin-table">
						<tr>
							<th><?= $txt_country_id; ?></th>
							<th><?= $txt_country_name; ?></th>
							<th><?= $txt_country_code; ?></th>
							<th><?= $txt_action; ?></th>
						</tr>
						<?php
						foreach($countries_arr as $k => $v) {
							$country_id   = $v['country_id'];
							$country_name = $v['country_name'];
							$country_abbr = $v['country_abbr'];

							// sanitize
							$country_name = e($country_name);
							?>
							<tr id="tr-country-<?= $country_id; ?>">
								<td><?= $country_id; ?></td>
								<td>
									<span class="btn btn-less-padding btn-default" id="activator-country-<?= $country_id; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-country-<?= $country_id; ?>"
										data-attribute="country"
										data-object="<?= $country_id; ?>">
										<?= $country_name; ?>
									</div>
								</td>
								<td>
									<span class="btn btn-less-padding btn-default" id="activator-country-abbr-<?= $country_id; ?>">
										<i class="fa fa-pencil"></i>
									</span>
									<div class="editable"
										data-url="<?= $baseurl; ?>/admin/admin-process-edit-loc.php"
										data-activator="#activator-country-abbr-<?= $country_id; ?>"
										data-attribute="country_abbr"
										data-object="<?= $country_id; ?>">
										<?= $country_abbr; ?>
									</div>
								</td>
								<td>
									<span data-toggle="tooltip" title="<?= $txt_remove_country; ?>">
										<a href="" class="btn btn-default btn-less-padding remove-loc"
											data-loc-id="<?= $country_id; ?>"
											data-loc-type="country">
											<i class="fa fa-trash"></i>
										</a>
									</span>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			<?php
			} // end if(!empty($countries_arr))
			?>

			<nav>
				<ul class="pagination">
					<?php
					if(isset($pager) && $pager->getTotalPages() > 1) {
						$curPage = $page;

						$startPage = ($curPage < 21)? 1 : $curPage - 20;
						$endPage = 40 + $startPage;
						$endPage = ($pager->getTotalPages() < $endPage) ? $pager->getTotalPages() : $endPage;
						$diff = $startPage - $endPage + 40;
						$startPage -= ($startPage - $diff > 0) ? $diff : 0;

						$startPage = ($startPage == 1) ? 2 : $startPage;
						$endPage = ($endPage == $pager->getTotalPages()) ? $endPage - 1 : $endPage;

						if($total_rows > 0) {
							$page_url = "$baseurl/admin/admin-locations/$show/page/";

							if ($curPage > 1) {
								?>
								<li><a href="<?= $page_url; ?>1"><?= $txt_pager_page1; ?></a></li>
								<?php
							}
							if ($curPage > 6) {
								?>
								<li><span>...</span></li>
								<?php
							}
							if ($curPage == 1) {
								?>
								<li class="active"><span><?= $txt_pager_page1; ?></span></li>
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
								$last_page_txt = $txt_pager_last_page;
							}

							$last_page_txt = ($pager->getTotalPages() > 5) ? $txt_pager_last_page : $pager->getTotalPages();

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
					?>
				</ul>

				<?php
				if(isset($pager) && $pager->getTotalPages() > 200) {
					$cents = floor($pager->getTotalPages() / 100);
					?>
					<ul class="pagination">
						<li><a href="#"><?= $txt_quick_jump; ?></a></li>
						<?php
						for($i = 1; $i <= $cents; $i++) {
							$j = $i * 100;
							?><li><a href="<?php echo $page_url, $j; ?>"><?= $j; ?></a></li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				?>
			</nav>
		</div><!-- .padding -->
	</div><!-- .main-container -->

	<div class="clear"></div>
</div><!-- .wrapper -->

<!-- modal create location -->
<div class="modal fade" id="create-loc-modal" tabindex="-1" role="dialog" aria-labelledby="Create Location Modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="modal-title"></h3>
			</div>
			<div class="modal-body">

			</div><!-- modal body -->
			<div class="modal-footer">
				<input class="btn btn-less-padding btn-blue" type="submit" id="create-loc-submit">
				<a href="#" class="btn btn-less-padding btn-default" id="modal-cancel" data-dismiss="modal"><?= $txt_cancel; ?></a>
			</div><!-- modal footer -->
		</div>
	</div>
</div>
<!-- end modal create location -->

<?php require_once(__DIR__ . '/_admin_footer.php'); ?>

<!-- javascript -->
<script src="<?= $baseurl; ?>/lib/jinplace/jinplace.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	// when modal activated, call admin-modal-form-create-loc.php to build the location creation form
	$('#create-loc-modal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget); // Button that triggered the modal
		var loc_type = button.data('loc-type'); // Extract info from data-* attributes
		var modal_title = button.data('modal-title'); // Extract info from data-* attributes
		var modal = $(this);

		// If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-modal-form-create-loc.php';

		$.post(post_url, { loc_type: loc_type },
			function(data) {
				modal.find('.modal-body').html(data);
				modal.find('.modal-title').html(modal_title);
			}
		);
	});

	// when modal dismissed, reload
	$('#create-loc-modal').on('hide.bs.modal', function (event) {
		location.reload(true);
	});

	// form in create loc modal submitted
    $('#create-loc-submit').click(function(e){
		e.preventDefault();
		var modal = $('#create-loc-modal');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-create-loc.php';

		$.post(post_url, {
			params: $('form.form-create-loc').serialize(),
			},
			function(data) {
				modal.find('.modal-body').html(data);
				modal.find('#create-loc-submit').remove();
				modal.find('#modal-cancel').empty().text('OK');
			}
		);
    });

	// remove loc
	$('.remove-loc').click(function(e) {
		e.preventDefault();
		var loc_id = $(this).data('loc-id');
		var loc_type = $(this).data('loc-type');
		var post_url = '<?= $baseurl; ?>' + '/admin/admin-process-remove-loc.php';

		switch (loc_type) {
			case 'city':
				var wrapper = '#tr-city-' + loc_id;
				break;
			case 'state':
				var wrapper = '#tr-state-' + loc_id;
				break;
			case 'country':
				var wrapper = '#tr-country-' + loc_id;
				break;
		}

		$.post(post_url, {
			loc_id: loc_id,
			loc_type: loc_type
			},
			function(data) {
				if(data) {
					$(wrapper).empty();
					var loc_removed_row = $('<td colspan="6" class="alert alert-success"></td>');
					$(loc_removed_row).text(data);
					$(loc_removed_row).hide().appendTo(wrapper).fadeIn();
				}
			}
		);
	});

	// toggle city featured
	$('.featured-home').click(function(e) {
		e.preventDefault();
		var city_id     = $(this).data('city-id');
		var post_url    = '<?= $baseurl; ?>' + '/admin/admin-process-toggle-city-featured.php';
		var city_status = $(this).data('city-status');

		$.post(post_url, {
			city_id    : city_id,
			city_status: city_status
			},
			function(data) {
				if(data == 'featured') {
					$('#featured-home-' + city_id).removeClass('btn-default');
					$('#featured-home-' + city_id).addClass('btn-green');
					$('#featured-home-' + city_id).data('city-status', 'featured');
				}
				if(data == 'not_featured') {
					$('#featured-home-' + city_id).removeClass('btn-green');
					$('#featured-home-' + city_id).addClass('btn-default');
					$('#featured-home-' + city_id).data('city-status', 'not_featured');
				}
				//location.reload(true);
			}
		);
	});

	// initialize edit in place
	$('.editable').jinplace();
});

</script>
</body>
</html>