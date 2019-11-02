<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/../../templates/_html_head.php'); ?>
<meta name="robots" content="noindex">
<style>
.col-md-4:nth-child(3n+1) {
	clear: both;
}
</style>
</head>
<body class="tpl-select-plan">
<?php require_once(__DIR__ . '/../../templates/_header.php'); ?>

<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

		<div class="block clearfix">
			<div class="list-items">
				<div class="item">
					<div class="item-pic">
						<a href="<?= $baseurl; ?>/<?= $city_slug; ?>/place/<?= $place_id; ?>/<?php echo to_slug($place_name); ?>" title="<?= $place_name; ?>"><img src="<?= $photo_url; ?>" /></a>
					</div><!-- .item-pic -->

					<div class="item-description">
						<div>
							<h2><a href="<?= $baseurl; ?>/<?= $city_slug; ?>/place/<?= $place_id; ?>/<?php echo to_slug($place_name); ?>" title="<?= $place_name; ?>"><?= $place_name; ?></a></h2>
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
								<?php
								if(!empty($address)) {
									?>
									<strong><?= $address; ?></strong>
									<?php
									if(!empty($cross_street))
										echo '(', $cross_street, ')';
									?><br>
									<?php
								}
								if(!empty($city_name)) {
									?>
									<strong><?= $city_name;
									if(!empty($state_abbr)) {
										echo ", ", $state_abbr, " ";
									}
								}
								if(!empty($postal_code)) {
									echo $postal_code;
								}
								?>
								</strong>
							</div>
							<?php
							if(!empty($phone)) {
								?>
								<div class="item-phone">
									<i class="fa fa-phone-square"></i> <?php echo ($phone != '' ? $phone : ''); ?>
								</div>
								<?php
							}
							?>
						</div><!-- .item-info -->

						<?php
						if(isset($tip_text)) {
							echo $tip_text;
						}
						?>
					</div><!-- .item-description -->

					<div class="clear"></div>
				</div><!-- .item  -->
			</div><!-- .list-items -->
		</div>

		<div class="block clearfix">
			<?php
			if(!empty($plans_arr)) {
				foreach($plans_arr as $k => $v) {
					?>
					<div class="col-md-4">
						<div class="panel panel-info plan-box">
							<div class="panel-heading"><h2 class="text-center"><?= $v['plan_name']; ?></h2></div>
							<div class="panel-body text-center">
								<p class="lead" style="font-size:40px"><strong><?= $currency_symbol; ?> <?= $v['plan_price']; ?></strong></p>
							</div>
							<ul class="list-group list-group-flush">
								<li class="list-group-item"><?= $v['plan_description1']; ?></li>
								<li class="list-group-item"><?= $v['plan_description2']; ?></li>
								<li class="list-group-item"><?= $v['plan_description3']; ?></li>
								<li class="list-group-item"><?= $v['plan_description4']; ?></li>
								<li class="list-group-item"><?= $v['plan_description5']; ?></li>
							</ul>
							<div class="panel-footer">
								<a href="<?= $baseurl; ?>/plugins/claim_listings/process-claim/<?= $place_id; ?>/<?= $v['plan_id']; ?>" class="btn btn-lg btn-block btn-blue"><?= $txt_buy_now; ?></a>
							</div>
						</div>
					</div>
					<?php
				}
			}
			else {
				?>
				<div class="alert alert-info" role="alert">
					<?= $txt_no_plans; ?>
				</div>
				<?php
			}
			?>
		</div>
	</div><!-- .full-block -->
</div><!-- .wrapper -->

<?php require_once(__DIR__ . '/../../templates/_footer.php'); ?>
</body>
</html>