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
// echo '<pre>';print_r($canShow);die;
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
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="">
				<span class="icon-ok"></span><?php echo JText::_('JSAVE')?>
				</button>
		</div>
	</div>
<?php endif ?>
</div>
<?php endif ?>
