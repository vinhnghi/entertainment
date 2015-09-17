<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );
//
use Joomla\Registry\Registry;
//
$talent = $displayData;
$user_details = $talent->user_details;
$params = JComponentHelper::getParams ( 'com_talent' );
$user = JFactory::getUser ();
$canShow = SiteTalentHelper::canShowTalentInfo ( $user, $talent );
$prefix = $user->guest ? 'public' : 'agent';
?>
<?php if ($user_details && count($user_details)):?>
<form action="index.php?option=com_talent&view=favourites" method="post"
	name="adminForm">
	<div class="detail_talendetail">
<?php foreach ($user_details as $k =>$v): if ($params->get("{$prefix}_{$k}", false)):?>
	<div class="detail_talendetail_label"><?php echo JText::_( strtoupper("COM_TALENT_$k"))?></div>
		<div class="detail_talendetail_value"><?php echo $v?></div>
		<div class="clearfix"></div>
<?php endif ?>
<?php endforeach?>
<?php if ($canShow):?>
	<?php echo JHtml::_('grid.id', 0, $talent->id)?>
	<?php echo JHtml::_('jgrid.published', 1, 0, 'favourites.', true, 'cb')?>
<?php endif ?>
</div>
<?php endif ?>
	<input type="hidden" name="task" value="" /> <input type="hidden"
		name="boxchecked" value="0" /> 
	<?php echo JHtml::_('form.token'); ?>
</form>