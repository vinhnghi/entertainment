<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

use Joomla\Registry\Registry;

$registry = new Registry ();
$registry->loadString ( $this->activityType->images );
$image = $registry->toArray ();

$readmore = JText::_('Readmore');
$hide = JText::_('Hide description');
$list_image_fulltext = $image['image_fulltext'] ? $image['image_fulltext'] : $image['image_intro'];
$list_description = $this->activityType->introtext.$this->activityType->fulltext;
$num_row_item = $this->params->get( 'num_row_item', 3 );
$item_width = 100/$num_row_item - 2;
$base_url = 'index.php?option=com_activity&view=activity&cid=';

?>
<div class="item-page com_activity_content com_activity_list_content">
	<div class="page-header">
		<h2 class="com_activity_heading"><?php echo $this->heading?></h2>
	</div>	
	<?php if ($list_image_fulltext):?>
	<div class="com_activity_list_image_fulltext"><img alt="<?php echo $image['image_fulltext_alt'];?>" src="<?php echo $list_image_fulltext;?>"/></div>
	<?php endif; ?>	
	<?php if ($list_description):?>
	<div class="com_activity_list_description"><?php echo $this->activityType->introtext.$this->activityType->fulltext;?></div>
	<?php endif; ?>	
<?php if (!empty($this->items)) :foreach ( $this->items as $i => $row ) :$link = JRoute::_ ( "{$base_url}{$this->activityType->id}&id={$row->id}" );$registry->loadString ( $row->images );$image = $registry->toArray ();?>
	<div class="com_activity_list_item" style="width: <?php echo $item_width?>%">
		<div class="com_activity_list_item_title"><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></div>
		<?php if ($image['image_intro']):?>
		<div class="com_activity_list_item_image_intro"><img alt="<?php echo $image['image_intro_alt'];?>" src="<?php echo $image['image_intro'];?>"></div>
		<?php endif; ?>
		<div class="com_activity_list_item_introtext"><?php echo $row->introtext;?></div>
		<?php if ($row->fulltext):?>
		<!-- 
		<div class="com_activity_list_item_fulltext" style="display:none;"><?php echo $row->fulltext;?></div>
		<div class="com_activity_list_item_readmore">
			<a onclick="var me = jQuery(this);me.parent().prev().toggle();me.text(me.text()=='<?php echo $readmore;?>'?'<?php echo $hide;?>':'<?php echo $readmore;?>');return false;"><?php echo JText::_('Readmore')?></a>
		</div>
		 -->
		<?php endif; ?>
	</div>
	<?php if ($i%$num_row_item==$num_row_item-1):?>
	<div class="com_activity_content_clear"></div>
	<?php endif; ?>
<?php endforeach; endif; ?>
</div>