<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

abstract class ActivityHelper 
{
	public static function addSubmenu($submenu) 
	{
		// JSubMenuHelper::addEntry ( JText::_ ( 'COM_ACTIVITY_SUBMENU_TALENTS' ), 'index.php?option=com_activity&view=talents', $submenu == 'talents' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_ACTIVITY_SUBMENU_ACTIVITIES' ), 'index.php?option=com_activity', $submenu == 'activities' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_ACTIVITY_SUBMENU_TYPES' ), 'index.php?option=com_activity&view=types', $submenu == 'types' );
	}
	
	public static function getActions($messageId = 0, $asset = 'activity') 
	{
		$result = new JObject ();
		
		if (empty ( $messageId )) {
			$assetName = 'com_activity';
		} else {
			$assetName = 'com_activity.' . $asset . '.' . ( int ) $messageId;
		}
		
		$actions = JAccess::getActions ( 'com_activity', 'component' );
		
		foreach ( $actions as $action ) {
			$result->set ( $action->name, JFactory::getUser ()->authorise ( $action->name, $assetName ) );
		}
		
		return $result;
	}
}