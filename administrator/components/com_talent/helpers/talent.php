<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
use Joomla\Registry\Registry;
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
	public static function getListTalentTypesQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*' 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent_type AS a' );
		return $query;
	}
	public static function getTalentType($id) {
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
				'd.name as title',
				'd.username as alias',
				'd.name',
				'd.username',
				'd.email',
				'd.password',
				'd.block',
				'd.activation' 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__talent AS a' );
		$query->leftJoin ( '#__talent_type_talent AS b ON a.id=b.talent_id' );
		$query->leftJoin ( '#__talent_type AS c ON c.id=b.talent_type_id' );
		$query->leftJoin ( '#__users AS d ON d.id=a.user_id' );
		return $query;
	}
	public function updateTalentData($talent) {
		if ($talent) {
			// Convert the metadata field to an array.
			$registry = new Registry ();
			$registry->loadString ( $talent->metadata );
			$talent->metadata = $registry->toArray ();
			
			// Convert the images field to an array.
			$registry = new Registry ();
			$registry->loadString ( $talent->images );
			$talent->images = $registry->toArray ();
			$talent->talenttext = trim ( $talent->fulltext ) != '' ? $talent->introtext . "<hr id=\"system-readmore\" />" . $talent->fulltext : $talent->introtext;
			// set types
			$talent->parent_id = TalentHelper::getTalentTypes ( $talent->id );
			
			$user = JFactory::getUser ( $talent->user_id );
			$talent->user_details = array (
					'id' => $user->id,
					'name' => $user->name,
					'username' => $user->username,
					'email' => $user->email 
			);
			// Load the profile data from the database.
			$db = JFactory::getDbo ();
			$db->setQuery ( 'SELECT profile_key, profile_value FROM #__user_profiles' . ' WHERE user_id = ' . ( int ) $user->id . " AND profile_key LIKE 'profile.%'" . ' ORDER BY ordering' );
			$results = $db->loadRowList ();
			// Merge the profile data.
			foreach ( $results as $v ) {
				$k = str_replace ( 'profile.', '', $v [0] );
				$talent->user_details [$k] = json_decode ( $v [1], true );
				if ($talent->user_details [$k] === null) {
					$talent->user_details [$k] = $v [1];
				}
			}
		} else {
			$talent = new stdClass ();
			$talent->id = '';
		}
		return $talent;
	}
	public static function getTalent($id) {
		$db = JFactory::getDbo ();
		$query = TalentHelper::getTalentQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return TalentHelper::updateTalentData ( $db->loadObject () );
	}
	public static function getTalentByUserId($userId) {
		$db = JFactory::getDbo ();
		$query = TalentHelper::getTalentQuery ();
		$query->where ( 'a.user_id = ' . ( int ) $userId );
		$db->setQuery ( $query );
		return TalentHelper::updateTalentData ( $db->loadObject () );
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
	public static function getTalentUserGroup() {
		$groupName = 'Talent';
		$group = TalentHelper::getGroupByName ( $groupName );
		if (! $group) {
			$registeredGroup = TalentHelper::getGroupByName ( 'Public' );
			$table = JTable::getInstance ( 'UserGroup' );
			$table->save ( array (
					'parent_id' => $registeredGroup->id,
					'title' => $groupName 
			) );
			$table->rebuild ();
			$group = TalentHelper::getGroupByName ( $groupName );
			TalentHelper::saveACL ( $group );
		}
		return $group;
	}
	public static function getGroupByName($name) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( '*' )->from ( '#__usergroups AS a' );
		$query->where ( 'a.title = ' . $db->quote ( $name ) );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	public static function saveACL($group) {
		$groupId = $group->id;
		// save permissions
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'id, rules' )->from ( $db->quoteName ( '#__assets' ) )->where ( $db->quoteName ( 'parent_id' ) . '= 0' );
		$db->setQuery ( $query );
		$asset = $db->loadObject ();
		$rules = new JRegistry ();
		$rules->loadString ( $asset->rules );
		$rule_array = $rules->toArray ();
		
		$rule_array ['core.login.site'] [$groupId] = 1;
		unset ( $rule_array ['core.login.admin'] [$groupId] );
		unset ( $rule_array ['core.login.offline'] [$groupId] );
		$rule_array ['core.admin'] [$groupId] = 0;
		unset ( $rule_array ['core.manage'] [$groupId] );
		$rule_array ['core.create'] [$groupId] = 1;
		$rule_array ['core.delete'] [$groupId] = 1;
		$rule_array ['core.edit'] [$groupId] = 1;
		unset ( $rule_array ['core.edit.state'] [$groupId] );
		$rule_array ['core.edit.own'] [$groupId] = 1;
		
		$rules->loadArray ( $rule_array );
		JTable::addIncludePath ( JPATH_ADMINISTRATOR . '/libraries/joomla/table' );
		$row = JTable::getInstance ( 'Asset' );
		$row->load ( $asset->id );
		$row->rules = $rules->__toString ();
		$row->store ();
		// save view levels
		$rule_array = array (
				( int ) $groupId 
		);
		JTable::addIncludePath ( JPATH_ADMINISTRATOR . '/libraries/joomla/table' );
		$row = JTable::getInstance ( 'Viewlevel' );
		$row->save ( array (
				'title' => $group->title,
				'rules' => json_encode ( array_unique ( $rule_array ) ) 
		) );
	}
}