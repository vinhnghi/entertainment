<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

JHtml::_ ( 'formbehavior.chosen', 'select' );
JHtml::_ ( 'behavior.core' );
JHtml::_ ( 'bootstrap.tooltip' );

$image = SiteTalentHelper::getFulltextImage ( $this->item->images );
?>
<form method="post" id="adminForm" name="adminForm">
	<div class="item-page com_talent_content com_talent_list_content">
		<div class="page-header">
			<h2 class="com_talent_heading"><?php echo $this->heading?></h2>
		</div>
	<?php echo $this->form->getInput('talentactivitiestags'); ?>		
	<?php if ($image):?>
	<div class="com_talent_list_image_fulltext">
			<img alt="<?php echo $image->alt?>" src="<?php echo $image->src?>">
		</div>
	<?php endif; ?>
	<?php echo SiteTalentHelper::getTalentDetailsHtml($this->item->id); ?>
	<div class="detail_talenttext"><?php echo $this->item->introtext.$this->item->fulltext;?></div>
		<div class="clearfix"></div>
	<?php echo $this->form->getInput('talentslideshow'); ?>
</div>

	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> 
	<?php echo JHtml::_('form.token')?>
</form>
