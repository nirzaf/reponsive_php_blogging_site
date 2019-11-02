<script>document.body.className += ' fade-out';</script>
<nav class="navbar navbar-default">
<div class="navbar-inner">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar">
			<span class="sr-only">Toggle navigation</span>
			<span class="glyphicon glyphicon-menu-hamburger"></span>
		</button>
		<div id="logo">
			<a href="<?= $baseurl; ?>/"><img src="<?= $baseurl; ?>/imgs/logo.png" /></a>
		</div>
	</div>

	<!-- Collect the nav links, forms, and other content for toggling -->
	<div class="collapse navbar-collapse" id="bs-navbar">
		<div class="collapse-header clearfix">
			<ul class="nav navbar-nav navbar-right" id="member-menu">
				<?php
				if(!empty($_SESSION['user_connected'])) {
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle btn btn-ghost" data-toggle="dropdown" role="button" aria-expanded="false">
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
							<li><a href="<?= $baseurl; ?>/user/">Dashboard</a></li>
							<li><a href="<?= $baseurl; ?>/user/logoff">Logoff</a></li>
							<?php
							if($is_admin) {
								?>
								<li><a href="<?= $baseurl; ?>/admin">Admin Area</a></li>
								<?php
							}
							?>
						</ul>
					</li>
					<?php
				}
				else {
					?>
					<li class="btn-sign-in"><a href="<?= $baseurl; ?>/user/login"><?= $txt_usermenu_signin ;?></a></li>
					<?php
				}
				?>
			</ul><!-- .nav navbar-nav navbar-right -->
		</div><!-- .collapse-header .clearfix -->
	</div><!-- /.navbar-collapse -->
</div><!-- /.navbar-inner -->
</nav>