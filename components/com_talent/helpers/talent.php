<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
// Require helper file
JLoader::register ( 'TalentHelper', JPATH_ADMINISTRATOR . '/components/com_talent/helpers/talent.php' );
JLoader::register ( 'TalentRouter', JPATH_SITE . '/components/com_talent/router.php' );
//
class SiteTalentHelper extends TalentHelper {
	//
	public static function getTalentDetailsHtml($obj) {
		if (is_string ( $obj )) {
			$talent = static::getTalent ( $obj );
			$talent->index = 0;
		} else {
			$talent = static::getTalent ( $obj->id );
			$talent->index = $obj->index;
		}
		
		if ($talent) {
			self::normaliseTalentDetails ( $talent->user_details );
			$layout = new JLayoutFile ( 'talent_details', JPATH_SITE . '/components/com_talent/layouts' );
			return $layout->render ( $talent );
		}
		return '';
	}
	//
	public static function normaliseTalentDetails(&$details) {
		$details ['gender'] = $details ['gender'] ? JText::_ ( 'COM_TALENT_MALE' ) : JText::_ ( 'COM_TALENT_FEMALE' );
	}
	//
	public static function getTalentDetailLink($talent, $type) {
		$cid = $type ? $type->id : 0;
		$base_url = 'index.php?option=com_talent&view=talent&cid=';
		return JRoute::_ ( "{$base_url}{$cid}&id={$talent->id}" );
	}
}
