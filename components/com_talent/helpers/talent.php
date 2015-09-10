<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
abstract class TalentHelper {
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
		$query->where ( 'a.published = 1' );
		return $query;
	}
	public static function getType($id) {
		if (! $id) {
			throw new Exception ( JText::_ ( 'Type id not found' ) );
			return;
		}
		
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*' 
		);
		
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent_type AS a' );
		$query->where ( 'a.id = ' . ( int ) $id );
		$query->where ( 'a.published = 1' );
		
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
				'c.id AS cid',
				'c.title AS type',
				'c.alias AS type_alias',
				'd.email',
				'd.id AS user_id',
				'd.name AS title',
				'd.username AS alias' 
		);
		
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent AS a' );
		$query->leftJoin ( '#__talent_type_talent AS b ON a.id=b.talent_id' );
		$query->leftJoin ( '#__talent_type AS c ON c.id=b.talent_type_id' );
		$query->leftJoin ( '#__users AS d ON d.id=a.user_id' );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		return $query;
	}
	public static function getTalent($id) {
		if (! $id) {
			throw new Exception ( JText::_ ( 'Talent id not found' ) );
			return;
		}
		$db = JFactory::getDbo ();
		$query = TalentHelper::getTalentQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	public static function getListTalentsQuery($cid) {
		if (! $cid) {
			throw new Exception ( JText::_ ( 'Type id not found' ) );
			return;
		}
		
		$query = TalentHelper::getTalentQuery ();
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