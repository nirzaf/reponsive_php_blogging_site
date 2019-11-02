<nav class="navbar navbar-default">
	<div class="navbar-inner">
		<div class="navbar-header">
			<div id="logo">
				<a href="<?= $baseurl; ?>/"><img src="<?= $baseurl; ?>/imgs/logo.png"></a>
			</div>

			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="glyphicon glyphicon-menu-hamburger"></span>
			</button>
		</div>

		<div class="nav-clearer"></div>

		<div class="right-block">
			<!-- collapsible block -->
			<div class="collapse navbar-collapse" id="bs-navbar">
				<div class="collapse-header clearfix">
					<!-- member drop down menu -->
					<ul class="nav navbar-nav navbar-right" id="main-menu">
						<!-- create listing link -->
						<li>
							<a href="<?= $baseurl; ?>/user/select-plan" class="btn btn-ghost">
							<i class="fa fa-pencil" aria-hidden="true"></i>
							<?= $txt_create_listing ;?></a>
						</li>

						<!-- all categories link -->
						<?php
						if(!empty($_COOKIE['city_id'])) {
							?>
							<li>
								<a href="<?= $baseurl; ?>/all-categories/<?php echo $_COOKIE['city_slug']; ?>/<?php echo $_COOKIE['city_id']; ?>" class="btn btn-ghost">
								<i class="fa fa-flag" aria-hidden="true"></i> <?= $txt_all_categories ;?></a>
							</li>
							<?php
						}
						else {
							?>
							<li>
								<a href="<?= $baseurl; ?>/all-categories" class="btn btn-ghost">
								<i class="fa fa-flag" aria-hidden="true" class="btn btn-ghost"></i> <?= $txt_all_categories ;?></a>
							</li>
							<?php
						}
						?>

						<!-- change city link -->
						<?php
						if(!empty($cookie_city_name)) {
							?>
							<li>
								<a href="<?= $baseurl; ?>/#"
									data-toggle="modal"
									data-target="#change-city-modal" class="btn btn-ghost">
									<i class="fa fa-location-arrow" aria-hidden="true"></i> <?= $cookie_city_name ;?>
								</a>
							</li>
							<?php
						}
						else {
							?>
							<li>
								<a href="<?= $baseurl; ?>/#"
									data-toggle="modal"
									data-target="#change-city-modal" class="btn btn-ghost">
									<i class="fa fa-location-arrow" aria-hidden="true"></i> <?= $txt_set_location ;?>
								</a>
							</li>
							<?php
						}
						?>

						<!-- search link -->
						<li>
							<a href="<?= $baseurl; ?>/plugins/custom_fields/advanced-search" class="btn btn-ghost"><i class="fa fa-search" aria-hidden="true"></i> <?= $txt_advanced_search ;?></a>
						</li>

						<!-- sign in / user drop down link -->
						<?php
						if(!empty($_SESSION['user_connected'])) {
							?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle btn btn-ghost" data-toggle="dropdown" role="button" aria-expanded="false"> <i class="fa fa-user" aria-hidden="true" style="padding:0;vertical-align:middle"></i>
									<?php
									if(!empty($first_name)) {
										echo $first_name;
									}
									elseif(!empty($last_name)) {
										echo $last_name;
									}
									else {
										$email_frags = explode('@', $email);
										echo mb_substr($email_frags[0], 0, 7);
									}
									?>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
									<li><a href="<?= $baseurl; ?>/user/"><?= $txt_usermenu_dashboard;?></a></li>
									<li><a href="<?= $baseurl; ?>/user/logoff"><?= $txt_usermenu_logoff;?></a></li>
									<?php
									if($is_admin) {
										?>
										<li><a href="<?= $baseurl; ?>/admin"><?= $txt_usermenu_admin ;?></a></li>
										<?php
									}
									?>
								</ul>
							</li>
							<?php
						}
						else {
							?>
							<li class="sign-in">
								<a href="<?= $baseurl; ?>/user/login" class="btn btn-ghost btn-sign-in"><i class="fa fa-sign-in" aria-hidden="true"></i> <?= $txt_usermenu_signin ;?></a>
							</li>
							<?php
						}
						?>
					</ul><!-- .nav navbar-nav navbar-right #main-menu -->
				</div><!-- .collapse-header .clearfix -->
			</div><!-- .navbar-collapse -->
		</div><!-- .right-block -->

		<div class="clearfix"></div>
	</div><!-- .navbar-inner -->
</nav>

<div class="search-block">
	<div class="search-block-inner">
		<form id="main-search-form" method="GET" action="<?= $baseurl; ?>/_searchresults.php" role="search">
			<div class="clearfix">
				<input id="query-input" name="query" type="text" autocomplete="off"
				placeholder="<?= $txt_inputplaceholder_keyword; ?>">

				<div id="city-input-wrapper"><select id="city-input" name="city_id" /></select></div>

				<button type="submit" class="btn btn-orange"><i class="fa fa-search"></i></button>
			</div>
		</form>
	</div>
</div>