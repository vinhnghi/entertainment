<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );

use Joomla\Registry\Registry;

$registry = new Registry ();
$registry->loadString ( $this->item->images );
$image = $registry->toArray ();

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
	<?php echo $this->form->getInput('talentslideshow'); ?>
</div>
