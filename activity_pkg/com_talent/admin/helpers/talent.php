<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
abstract class TalentHelper {
	public static function addSubmenu($submenu) {
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_TYPES' ), 'index.php?option=com_talent&view=types', $submenu == 'types' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_TALENTS' ), 'index.php?option=com_talent&view=talents', $submenu == 'talents' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_AGENTS' ), 'index.php?option=com_talent&view=agents', $submenu == 'agents' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_FAVORITES' ), 'index.php?option=com_talent&view=favorites', $submenu == 'favorites' );
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
	public static function getListTypesQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*' 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent_type AS a' );
		return $query;
	}
	public static function getType($id) {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*' 
		);
		
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent_type AS a' );
		$query->where ( 'a.id = ' . ( int ) $id );
		
		$db->setQuery ( $query );
		
		return $db->loadObject ();
	}
	public static function getTalentQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*',
				'd.email',
				'd.id AS user_id',
				'd.name AS title',
				'd.username AS alias' 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__talent AS a' );
		$query->leftJoin ( '#__talent_type_talent AS b ON a.id=b.talent_id' );
		$query->leftJoin ( '#__talent_type AS c ON c.id=b.talent_type_id' );
		$query->leftJoin ( '#__users AS d ON d.id=a.user_id' );
		return $query;
	}
	public static function getTalent($id) {
		$db = JFactory::getDbo ();
		$query = TalentHelper::getTalentQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	public static function getTalentTypes($id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'talent_type_id' 
		) ) );
		$query->from ( $db->quoteName ( '#__talent_type_talent' ) );
		$query->where ( $db->quoteName ( 'talent_id' ) . ' = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadColumn ();
	}
	public static function getListTalentsQuery($cid) {
		$query = TalentHelper::getTalentQuery ();
		if ($cid)
			$query->where ( 'b.talent_type_id = ' . ( int ) $cid );
		return $query;
	}
	public static function getTalentImages($id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'a.*' );
		$query->from ( '#__talent_assets AS a' );
		$query->where ( '(a.talent_id = ' . ( int ) $id . ')' );
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
}