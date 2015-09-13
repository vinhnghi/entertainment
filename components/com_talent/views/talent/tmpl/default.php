<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

$image = $this->item->images;

$list_image_fulltext = $image ['image_fulltext']; // ? $image['image_fulltext'] : $image['image_intro'];
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
	<div class="detail_talenttext"><?php echo $this->item->introtext.$this->item->fulltext;?></div>
	<div class="com_talent_content_clear"></div>
	<?php echo $this->form->getInput('talentslideshow'); ?>
</div>
