<?php
defined ( '_JEXEC' ) or die ();
?>
<?php

foreach ( $list as $i => $row ) :
	$class = $i == 0 ? 'mod_activity_list_item_first' : '';
	$class = $class . ($i == count ( $list ) - 1 ? ' mod_activity_list_item_last' : '');
	$row->index = $i;
	$link = JRoute::_ ( SiteActivityHelper::getActivityDetailLink ( $row, $parent_id ) . "&itemId=" . $itemId );
	$image = SiteActivityHelper::getIntroImage ( $row->images );
	?>
<div class="mod_activity_list_item <?php echo $class?>">
	<?php if ($image):?>
	<div class="mod_activity_list_item_image_intro">
		<a href="<?php echo $link ?>"><img alt="<?php echo $image->alt?>"
			src="<?php echo $image->src?>"></a>
	</div>
	<?php endif; ?>
	<div class="mod_activity_list_item_title">
		<a href="<?php echo $link ?>"><?php echo $row->title ?></a>
	</div>
	<div class="mod_activity_list_item_introtext"><?php echo SiteActivityHelper::truncate( strip_tags($row->introtext), 20);?></div>
	<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<?php endforeach;?>
