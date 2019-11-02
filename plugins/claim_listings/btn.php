<?php
if($place_userid == 1 && $userid != 1) {
	?>
	<a href="<?= $baseurl; ?>/plugins/claim_listings/claim/<?= $place_id; ?>"
	class="btn btn-default btn-even-less-padding" style="font-weight: 700;"><em>claim listing</em></a>
<?php
}