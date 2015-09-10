<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
abstract class TalentHelper {
	public static function addSubmenu($submenu) {
	}
	public static function getActions($messageId = 0, $asset = 'talent') {
		$result = new JObject ();
		
		if (empty ( $messageId )) {
			$assetName = 'com_talent';
		} else {
			$assetName = 'com_talent.' . $asset . '.' . ( int ) $messageId;
		}
		
		$actions = JAccess::getActions ( 'com_talent', 'component' );
		
		foreach ( $actions as $action ) {
			$result->set ( $action->name, JFactory::getUser ()->authorise ( $action->name, $assetName ) );
		}
		
		return $result;
	}
	public static function truncate($string = "", $max_words) {
		$array = array_filter ( explode ( ' ', $string ), 'strlen' );
		if (count ( $array ) > $max_words && $max_words > 0)
			$string = implode ( ' ', array_slice ( $array, 0, $max_words ) ) . '...';
		return $string;
	}
}