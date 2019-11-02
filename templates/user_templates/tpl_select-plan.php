<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<?php require_once(__DIR__ . '/_user_html_head.php'); ?>

</head>
<body class="tpl-select-plan">
<?php require_once(__DIR__ . '/_user_header.php'); ?>

<div class="wrapper">
	<div class="full-block">
		<h1><?= $txt_main_title; ?></h1>

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
								<a href="<?= $baseurl; ?>/user/add-place/<?= $v['plan_id']; ?>" class="btn btn-lg btn-block btn-blue"><?= $txt_buy_now; ?></a>
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

<?php require_once(__DIR__ . '/_user_footer.php'); ?>
</body>
</html>