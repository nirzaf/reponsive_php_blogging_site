<?php require("libs/fetch_data.php");?>
<?php //code to get the item using its id
include("database/conn.php");//database config file
$id=$_REQUEST['id']; $query="SELECT * from blog_categories where id='".$id."'"; $result=mysqli_query($GLOBALS["___mysqli_ston"],$query) or die ( ((is_object($GLOBALS["___mysqli_ston"]))? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ?$___mysqli_res : true))); 
$row = mysqli_fetch_assoc($result);?>
<!DOCTYPE html>
<html lang="zxx">
<head>
	<title>Category-<?php echo $row['name']; ?>|<?php getwebname("titles");?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link id="browser_favicon" rel="shortcut icon" href="blogadmin/images/<?php geticon("titles"); ?>">
	<meta charset="utf-8" name="description" content="<?php getshortdescription("titles");?>">
	<meta name="keywords" content="<?php getkeywords("titles");?>" />
	<script>
		addEventListener("load", function () {
			setTimeout(hideURLbar, 0);
		}, false);

		function hideURLbar() {
			window.scrollTo(0, 1);
		}
	</script>
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	<link rel="stylesheet" href="css/single.css">
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link href="css/fontawesome-all.css" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800"
	    rel="stylesheet">
</head>
<body>
	<!--Header-->
	<?php include("header.php");?>
	<!--//header-->
	<!--/banner-->
	<div class="banner-inner">
	</div>
	<ol class="breadcrumb">
		<li class="breadcrumb-item">
			<a href="index.php">Home</a>
		</li>
		<li class="breadcrumb-item active"><?php echo $row['name']; ?></li>
	</ol>
	<!--//banner-->
	<!--/main-->
	<section class="main-content-w3layouts-agileits">
		<div class="container">
			<h3 class="tittle">Blog Posts</h3>
			<div class="row inner-sec">
				<!--left-->
				<div class="col-lg-8 left-blog-info-w3layouts-agileits text-left">
					<div class="row mb-4">
						<?php  
							$categoryid=$row['id'];
							getcategoryblogs("blogs",$categoryid);
						?>
					</div>
				</div>
				<!--//left-->
				<!--right-->
				<aside class="col-lg-4 agileits-w3ls-right-blog-con text-right">
					<div class="right-blog-info text-left">
						<h4><strong>Categories</strong></h4>
						<ul class="list-group single">
							<?php countcategories();?>
						</ul>
						<div class="tech-btm widget_social">
							<h4>Stay Connected</h4>
							<ul>

								<li>
									<a class="twitter" href="<?php getlinks("links","twitter");?>">
										<i class="fab fa-twitter"></i>
										<span class="count"></span> Twitter</a>
									</li>
									<li>
										<a class="facebook" href="<?php getlinks("links","facebook");?>">
											<i class="fab fa-facebook-f"></i>
											<span class="count"></span> Facebook</a>
										</li>
										<li>
											<a class="dribble" href="<?php getlinks("links","dribble");?>">
												<i class="fab fa-dribbble"></i>

												<span class="count"></span> Dribble</a>
											</li>
											<li>
												<a class="pin" href="<?php getlinks("links","pinterest");?>">
													<i class="fab fa-pinterest"></i>
													<span class="count"></span> Pinterest</a>
												</li>

											</ul>
										</div>
										<div class="tech-btm">
											<h4>Older Posts</h4>
											<?php getolderposts("blogs");?>
											<!--olderpostsendhere-->
										</div>
									</div>
								</aside>
				<!--//right-->
				</div>
			</div>
	</section>
	<!--//main-->

	<!--footer-->
	<?php include("footer.php");?>
	<!---->

	<!-- js -->
	<script src="js/jquery-2.2.3.min.js"></script>
	<!-- //js -->
	<!--/ start-smoth-scrolling -->
	<script src="js/move-top.js"></script>
	<script src="js/easing.js"></script>
	<script>
		jQuery(document).ready(function ($) {
			$(".scroll").click(function (event) {
				event.preventDefault();
				$('html,body').animate({
					scrollTop: $(this.hash).offset().top
				}, 900);
			});
		});
	</script>
	<!--// end-smoth-scrolling -->

	<script>
		$(document).ready(function () {
			/*
									var defaults = {
							  			containerID: 'toTop', // fading element id
										containerHoverID: 'toTopHover', // fading element hover id
										scrollSpeed: 1200,
										easingType: 'linear' 
							 		};
									*/

			$().UItoTop({
				easingType: 'easeOutQuart'
			});

		});
	</script>
	<a href="#home" class="scroll" id="toTop" style="display: block;">
		<span id="toTopHover" style="opacity: 1;"> </span>
	</a>

	<!-- //Custom-JavaScript-File-Links -->
	<script src="js/bootstrap.js"></script>


</body>

</html>