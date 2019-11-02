<!DOCTYPE html>
<!--[if IE 9]><html class="lt-ie10" lang="<?= $html_lang; ?>"> <![endif]-->
<html lang="<?= $html_lang; ?>">
<head>
<title><?= $txt_html_title; ?> - <?= $site_name; ?></title>
<meta name="description" content="<?= $txt_meta_desc; ?>" />
<?php require_once('_html_head.php'); ?>
<style>
[class^="flaticon-"]:before, [class*=" flaticon-"]:before,
[class^="flaticon-"]:after, [class*=" flaticon-"]:after {
	font-size: 24px;
}

ul.cat-main-parent {
	padding-left: 0;
	margin-bottom: 0;
}

.cat-tree {
	list-style-type: none;
}

.cat-tree a {
	color: #404040;
	text-decoration: none;
}

.cat-main-parent {
	-webkit-column-count: 3;
	-moz-column-count: 3;
	column-count: 3;
}

.cat-main-parent > li {
	margin-bottom: 24px;
}

@media only screen and (max-width: 740px) {
	.cat-main-parent {
		-webkit-column-count: 2;
		-moz-column-count: 2;
		column-count: 2;
	}
}

@media only screen and (max-width: 540px) {
	.cat-main-parent {
		-webkit-column-count: 1;
		-moz-column-count: 1;
		column-count: 1;
	}
}
</style>
</head>
<body class="tpl-all-categories">
<?php require_once('_header.php'); ?>

<div class="wrapper">
	<h1><?= $txt_main_title; ?></h1>
	<div class="full-block main-categories">
		<div class="block">
			<?= $suggest_cats_in_city; ?>
		</div>

		<div class="tree">
			<!-- start showing categories tree -->

			<?php
			// recursive function to loop through the array of categories grouped by parent
			function show_cats2($cats_grouped_by_parent, $parent_id = 0) {
				global $baseurl;
				global $loc_slug;
				global $loc_type;
				global $loc_id;
				global $cat_items_count;

				if($parent_id == 0) {
					echo '<ul class="cat-tree cat-main-parent">';
				} else {
					echo '<ul class="cat-tree">';
				}

				/*
$tree .= '<a href="' . $baseurl . '/' . $loc_slug . '/list/' . $cat_slug3 . '/' . $loc_type . '-' . $loc_id . '-' . $cat_id3 . '-1">' . $plural_name3 . "($this_cat_count3)" . '</a>';
				*/

				if(!empty($cats_grouped_by_parent)) {
					foreach ($cats_grouped_by_parent[$parent_id] as $v) {
						$this_cat_slug     = (!empty($v['plural_name'])) ? to_slug($v['plural_name']) : to_slug($v['cat_name']);
						$this_cat_name     = (!empty($v['plural_name'])) ? $v['plural_name'] : $v['cat_name'];
						$this_cat_id       = $v['cat_id'];
						$this_iconfont_tag = (!empty($v['iconfont_tag'])) ? $v['iconfont_tag'] : '';
						$this_cat_count    = (!empty($cat_items_count[$this_cat_id])) ? $cat_items_count[$this_cat_id] : 0;

						echo '<li data-cat-id="' . $v['cat_id'] . '"> ';
							echo "<a href='$baseurl/$loc_slug/list/$this_cat_slug/$loc_type-$loc_id-$this_cat_id-1'>
								$this_iconfont_tag $this_cat_name ($this_cat_count)</a>";



							//if there are children
							if (!empty($cats_grouped_by_parent[$this_cat_id])) {
								show_cats2($cats_grouped_by_parent, $this_cat_id);
							}
						echo '</li>';
					}
				}

				echo '</ul>';
			}
			// show cats
			if(!empty($cats_grouped_by_parent)) {
				show_cats2($cats_grouped_by_parent, 0);
			}
			?>

			<div class="clear"></div>
		</div>
	</div><!-- .full-block .main-categories -->
</div><!-- .wrapper -->

<?php require_once('_footer.php'); ?>

</body>
</html>