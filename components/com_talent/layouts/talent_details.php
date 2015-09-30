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
$prefix = $user->guest ? 'public' : ($canShow ? 'agent' : 'public');
//
?>
<?php if ($user_details && count($user_details)):?>
<div class="detail_talendetail">
<?php foreach ($user_details as $k =>$v): if ($params->get("{$prefix}_{$k}", false)): 
if ($k == 'dob') { 
	$date = new DateTime($v);$v = $date->format('d/m/Y'); 
}?>
	<div class="detail_talendetail_label"><?php echo JText::_( strtoupper("COM_TALENT_$k"))?></div>
	<div class="detail_talendetail_value"><?php echo $v?></div>
	<div class="clearfix"></div>
<?php endif ?>
<?php endforeach?>
<?php if ($canShow):?>
	<div style="display: none !important"><?php echo JHtml::_('grid.id', $displayData->index, $talent->id)?></div>
	<?php if ($talent->user_id != $user->id ):?>
	<?php echo SiteTalentHelper::getAddRemoveTalentButton($displayData->index, $agent->id, $talent->id)?>
	<?php endif ?>
<?php endif ?>
</div>
<?php endif ?>
