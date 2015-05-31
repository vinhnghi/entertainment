<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

use Joomla\Registry\Registry;

$registry = new Registry ();
$registry->loadString ( $this->item->images );
$image = $registry->toArray ();

$list_image_fulltext = $image['image_fulltext'];// ? $image['image_fulltext'] : $image['image_intro'];
$list_description = $this->activityType->introtext.$this->activityType->fulltext;

?>
<div class="item-page com_activity_content com_activity_list_content">
	<div class="page-header">
		<h2 class="com_activity_heading"><?php echo $this->heading?></h2>
	</div>
	<?php echo $this->form->getInput('activitytalentstags'); ?>
	<?php if ($list_image_fulltext):?>
	<div class="com_activity_list_image_fulltext"><img alt="<?php echo $image['image_fulltext_alt'];?>" src="<?php echo $list_image_fulltext;?>"></div>
	<?php endif; ?>
	<div class="detail_activitytext"><?php echo $this->item->introtext.$this->item->fulltext;?></div>
	<?php echo $this->form->getInput('activityslideshow'); ?>
</div>
