<?php
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

use Joomla\Registry\Registry;

$readmore = JText::_ ( 'Readmore' );
$hide = JText::_ ( 'Hide description' );
$list_image_fulltext = $this->params->get ( 'page_image' );
$list_description = $this->params->get ( 'page_description' );
$num_items = $this->params->get ( 'num_items', 3 );
$item_width = 100 / $num_items - 2;
$base_url = 'index.php?option=com_talent&view=talents&cid=';
$registry = new Registry ();
?>
<div class="item-page com_talent_content com_talent_list_content">
	<div class="page-header">
		<h2 class="com_talent_heading"><?php echo $this->heading?></h2>
	</div>	
	<?php if ($list_image_fulltext):?>
	<div class="com_talent_list_image_fulltext">
		<img alt="" src="<?php echo $list_image_fulltext;?>">
	</div>
	<?php endif; ?>	
	<?php if ($list_description):?>
	<div class="com_talent_list_description"><?php echo $list_description;?></div>
	<?php endif; ?>	
	<div class="clearfix"></div>
<?php if (!empty($this->items)) : foreach ( $this->items as $i => $row ) : $link = JRoute::_ ( $base_url . $row->id );$registry->loadString ( $row->images );$image = $registry->toArray ();?>
	<div class="com_talent_list_item" style="width: <?php echo $item_width?>%">
		<div class="com_talent_list_item_title">
			<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
		</div>
		<?php if ($image['image_intro']):?>
		<div class="com_talent_list_item_image_intro">
			<img alt="<?php echo $image['image_intro_alt'];?>"
				src="<?php echo $image['image_intro'];?>">
		</div>
		<?php endif; ?>
		<div class="com_talent_list_item_introtext"><?php echo SiteTalentHelper::truncate( strip_tags($row->introtext), 20);?></div>
		<?php if ($row->fulltext):?>
		<?php endif; ?>
	</div>
	<?php if ($i%$num_items==$num_items-1):?>
	<div class="clearfix"></div>
	<?php endif; ?>
<?php endforeach; endif; ?>
</div>
