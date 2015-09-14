<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

$image = $this->item->images;

$list_image_fulltext = $image ['image_fulltext']; // ? $image['image_fulltext'] : $image['image_intro'];

$user_details = $this->item->user_details;

$params = JComponentHelper::getParams ( 'com_talent' );

$user = JFactory::getUser ();

$canShow = $user->guest ? true : TalentHelper::canShowTalentInfo ( $user, $this->item );

$prefix = $user->guest ? 'public' : 'agent';

?>
<div class="item-page com_talent_content com_talent_list_content">
	<div class="page-header">
		<h2 class="com_talent_heading"><?php echo $this->heading?></h2>
	</div>
	<?php if ($list_image_fulltext):?>
	<div class="com_talent_list_image_fulltext">
		<img alt="<?php echo $image['image_fulltext_alt'];?>"
			src="<?php echo $list_image_fulltext;?>">
	</div>
	<?php endif; ?>
	<?php if ($user_details && count($user_details)):?>
	<div class="detail_talendetail">
	<?php foreach ($user_details as $k =>$v): if ($canShow && $params->get("{$prefix}_{$k}", false)):?>
		<div class="detail_talendetail_label"><?php echo JText::_( strtoupper("COM_TALENT_$k"))?></div>
		<div class="detail_talendetail_value"><?php echo $v?></div>
		<div class="com_talent_content_clear"></div>
	<?php endif; ?>
	<?php endforeach;?>
	</div>
	<?php endif; ?>
	<div class="detail_talenttext"><?php echo $this->item->introtext.$this->item->fulltext;?></div>
	<div class="com_talent_content_clear"></div>
	<?php echo $this->form->getInput('talentslideshow'); ?>
</div>
