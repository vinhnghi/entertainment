<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted Access' );
//
use Joomla\Registry\Registry;
//
$talent = $displayData;
$user_details = $talent->user_details;
//
$params = JComponentHelper::getParams ( 'com_talent' );
//
$user = JFactory::getUser ();
$agent = SiteTalentHelper::getAgentByUserId ( $user->id );
$canShow = SiteTalentHelper::canShowTalentInfo ( $user, $talent );
$prefix = $user->guest ? 'public' : 'agent';
//
$favourite = SiteTalentHelper::getFavourite ( $talent->id, $agent->id );
?>
<?php if ($user_details && count($user_details)):?>
<div class="detail_talendetail">
<?php foreach ($user_details as $k =>$v): if ($params->get("{$prefix}_{$k}", false)):?>
	<div class="detail_talendetail_label"><?php echo JText::_( strtoupper("COM_TALENT_$k"))?></div>
	<div class="detail_talendetail_value"><?php echo $v?></div>
	<div class="clearfix"></div>
<?php endif ?>
<?php endforeach?>
<?php if ($canShow):?>
	<div style="display: none !important"><?php echo JHtml::_('grid.id', $displayData->index, $talent->id)?></div>
	<?php echo JHtml::_('jgrid.published', $favourite ? 2 : 0, $displayData->index, 'favourites.', true, 'cb')?>
<?php endif ?>
</div>
<?php endif ?>
