<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );
JHtml::_ ( 'behavior.core' );
JHtml::_ ( 'bootstrap.tooltip' );

//
$image = SiteTalentHelper::getFulltextImage ( $this->type ? $this->type->images : null );
//
$list_description = $this->type ? $this->type->introtext . $this->type->fulltext : '';
$num_row_item = $this->params->get ( 'num_row_item', 3 );
$item_width = 100 / $num_row_item - 2;
?>
<form method="post" id="adminForm" name="adminForm">
	<div class="item-page com_talent_content com_talent_list_content">
		<div class="page-header">
			<h2 class="com_talent_heading"><?php echo $this->heading?></h2>
		</div>	
	<?php if ($image):?>
	<div class="com_talent_list_image_fulltext">
			<img alt="<?php echo $image->alt?>" src="<?php echo $image->src?>" />
		</div>
	<?php endif; ?>	
	<?php if ($list_description):?>
	<div class="com_talent_list_description"><?php echo $this->type->introtext.$this->type->fulltext;?></div>
	<?php endif; ?>	

	<?php
	if (! empty ( $this->items )) :
		foreach ( $this->items as $i => $row ) :
			$row->index = $i;
			$link = SiteTalentHelper::getTalentDetailLink ( $row, $this->type );
			$image = SiteTalentHelper::getIntroImage ( $row->images );
			?>
	<div class="com_talent_list_item" style="width: <?php echo $item_width?>%">
			<div class="com_talent_list_item_title">
				<a href="<?php echo $link ?>"><?php echo $row->title ?></a>
			</div>
		<?php if ($image):?>
		<div class="com_talent_list_item_image_intro">
				<img alt="<?php echo $image->alt?>" src="<?php echo $image->src?>">
			</div>
			<div class="clearfix"></div>
		<?php endif; ?>
		<?php echo SiteTalentHelper::getTalentDetailsHtml($row); ?>
		<div class="clearfix"></div>
			<div class="com_talent_list_item_introtext"><?php echo SiteTalentHelper::truncate( strip_tags($row->introtext), 20);?></div>
		</div>
	<?php if ($i%$num_row_item==$num_row_item-1):?>
	<div class="clearfix"></div>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	</div>

	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="view" value="" /> <input type="hidden" name="boxchecked"
		value="0" /> <input type="hidden" name="filter_order" value="" /> <input
		type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHtml::_('form.token')?>
</form>
