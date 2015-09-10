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
	public static function getTalent($id) {
		if (! $id) {
			throw new Exception ( JText::_ ( 'Talent id not found' ) );
			return;
		}
		
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*',
				'c.id AS cid',
				'c.title AS type',
				'c.alias AS type_alias',
				'd.email',
				'd.id AS user_id',
				'd.name AS title',
				'd.username AS alias' 
		);
		
		$query->select ( implode ( ",", $fields ) )->from ( 'joomla_talent AS a' );
		$query->leftJoin ( 'joomla_talent_type_talent AS b ON a.id=b.talent_id' );
		$query->leftJoin ( 'joomla_talent_type AS c ON c.id=b.talent_type_id' );
		$query->leftJoin ( 'joomla_users AS d ON d.id=a.user_id' );
		$query->where ( 'a.id = ' . ( int ) $id );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		
		$db->setQuery ( $query );
		
		return $db->loadObject ();
	}
}