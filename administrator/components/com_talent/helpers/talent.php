<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
JFactory::getLanguage ()->load ( 'com_talent' );
//
abstract class TalentHelper {
	public static function addSubmenu($submenu) {
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_TYPES' ), 'index.php?option=com_talent&view=types', $submenu == 'types' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_TALENTS' ), 'index.php?option=com_talent&view=talents', $submenu == 'talents' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_AGENTS' ), 'index.php?option=com_talent&view=agents', $submenu == 'agents' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_TALENT_SUBMENU_FAVOURITES' ), 'index.php?option=com_talent&view=favourites', $submenu == 'favourites' );
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
	public static function getListTalentTypeQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*',
				'a.introtext AS text',
				$db->Quote ( JText::_ ( 'COM_TALENT_SEARCH_SECTION_TYPE' ) ) . ' AS section',
				'"100" AS browsernav' 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__talent_type AS a' );
		return $query;
	}
	public static function getListTalentTypesQuery() {
		return static::getListTalentTypeQuery ();
	}
	public static function getTalentType($id) {
		$db = JFactory::getDbo ();
		$query = static::getListTalentTypeQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	//
	public static function getTalentQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*',
				'a.introtext AS text',
				'd.name as title',
				'd.username as alias',
				'd.name',
				'd.username',
				'd.email',
				'd.password',
				'd.block',
				'd.activation',
				'd.registerDate as created',
				$db->Quote ( JText::_ ( 'COM_TALENT_SEARCH_SECTION_TALENT' ) ) . ' AS section',
				'"101" AS browsernav' 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__talent AS a' );
		$query->innerJoin ( '#__talent_type_talent AS b ON a.id=b.talent_id' );
		$query->innerJoin ( '#__talent_type AS c ON c.id=b.talent_type_id' );
		$query->innerJoin ( '#__users AS d ON d.id=a.user_id' );
		return $query;
	}
	//
	public static function updateTalentData($talent) {
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
			$talent->parent_id = static::getTalentTypes ( $talent->id );
			
			$talent->user_details = array (
					'id' => $talent->user_id,
					'name' => $talent->name,
					'username' => $talent->username,
					'email' => $talent->email 
			);
			// Load the profile data from the database.
			$db = JFactory::getDbo ();
			$db->setQuery ( 'SELECT profile_key, profile_value FROM #__user_profiles' . ' WHERE user_id = ' . ( int ) $talent->user_id . " AND profile_key LIKE 'profile.%'" . ' ORDER BY ordering' );
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
	//
	public static function getTalent($id) {
		$db = JFactory::getDbo ();
		$query = static::getTalentQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return static::updateTalentData ( $db->loadObject () );
	}
	//
	public static function getTalentUserId($id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'a.user_id' )->from ( '#__talent AS a' );
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	//
	public static function getTalentByUserId($userId) {
		$db = JFactory::getDbo ();
		$query = static::getTalentQuery ();
		$query->where ( 'a.user_id = ' . ( int ) $userId );
		$db->setQuery ( $query );
		return static::updateTalentData ( $db->loadObject () );
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
		$query = static::getTalentQuery ();
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
	public static function isAgent($user) {
		if ($user) {
			$agentUserGroup = static::getAgentUserGroup ();
			$groups = isset ( $user->groups ) ? $user->groups : array ();
			if ($groups && in_array ( $agentUserGroup->id, $groups )) {
				return true;
			}
		}
		return false;
	}
	public static function canShowTalentInfo($user, $talent) {
		// echo '<pre>';print_r($user);die;
		if ($user && $talent) {
			if ($user->id == $talent->user_id) {
				return true;
			}
			$agentUserGroup = static::getAgentUserGroup ();
			$groups = isset ( $user->groups ) ? $user->groups : array ();
			
			if ($groups && in_array ( $agentUserGroup->id, $groups )) {
				return true;
			}
		}
		return false;
	}
	public static function getTalentUserGroup() {
		$groupName = 'Talent';
		$group = static::getGroupByName ( $groupName );
		if (! $group) {
			$registeredGroup = static::getGroupByName ( 'Public' );
			$table = JTable::getInstance ( 'UserGroup' );
			$table->save ( array (
					'parent_id' => $registeredGroup->id,
					'title' => $groupName 
			) );
			$table->rebuild ();
			$group = static::getGroupByName ( $groupName );
			static::saveACL ( $group );
		}
		return $group;
	}
	public static function getAgentUserGroup() {
		$groupName = 'Agent';
		$group = static::getGroupByName ( $groupName );
		if (! $group) {
			$registeredGroup = static::getGroupByName ( 'Public' );
			$table = JTable::getInstance ( 'UserGroup' );
			$table->save ( array (
					'parent_id' => $registeredGroup->id,
					'title' => $groupName 
			) );
			$table->rebuild ();
			$group = static::getGroupByName ( $groupName );
			static::saveACL ( $group );
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
		$rule_array ['core.edit.state'] [$groupId] = 1;
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
		$row = JTable::getInstance ( 'ViewLevel' );
		$row->save ( array (
				'title' => $group->title,
				'rules' => json_encode ( array_unique ( $rule_array ) ) 
		) );
	}
	// for agent
	public static function getAgentQuery() {
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
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__agent AS a' );
		$query->innerJoin ( '#__users AS d ON d.id=a.user_id' );
		return $query;
	}
	public static function updateAgentData($agent) {
		if ($agent) {
			// Convert the metadata field to an array.
			$registry = new Registry ();
			$registry->loadString ( $agent->metadata );
			$agent->metadata = $registry->toArray ();
			
			// Convert the images field to an array.
			$registry = new Registry ();
			$registry->loadString ( $agent->images );
			$agent->images = $registry->toArray ();
			$agent->agenttext = trim ( $agent->fulltext ) != '' ? $agent->introtext . "<hr id=\"system-readmore\" />" . $agent->fulltext : $agent->introtext;
			
			$agent->user_details = array (
					'id' => $agent->user_id,
					'name' => $agent->name,
					'username' => $agent->username,
					'email' => $agent->email 
			);
			// Load the profile data from the database.
			$db = JFactory::getDbo ();
			$db->setQuery ( 'SELECT profile_key, profile_value FROM #__user_profiles' . ' WHERE user_id = ' . ( int ) $agent->user_id . " AND profile_key LIKE 'profile.%'" . ' ORDER BY ordering' );
			$results = $db->loadRowList ();
			// Merge the profile data.
			foreach ( $results as $v ) {
				$k = str_replace ( 'profile.', '', $v [0] );
				$agent->user_details [$k] = json_decode ( $v [1], true );
				if ($agent->user_details [$k] === null) {
					$agent->user_details [$k] = $v [1];
				}
			}
		} else {
			$agent = new stdClass ();
			$agent->id = '';
		}
		return $agent;
	}
	//
	public static function getAgent($id) {
		$db = JFactory::getDbo ();
		$query = static::getAgentQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return static::updateAgentData ( $db->loadObject () );
	}
	//
	public static function getAgentUserId($id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'a.user_id' )->from ( '#__agent AS a' );
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadResult ();
	}
	//
	public static function getAgentByUserId($userId) {
		$db = JFactory::getDbo ();
		$query = static::getAgentQuery ();
		$query->where ( 'a.user_id = ' . ( int ) $userId );
		$db->setQuery ( $query );
		return static::updateAgentData ( $db->loadObject () );
	}
	public static function getListAgentsQuery($cid) {
		$query = static::getAgentQuery ();
		return $query;
	}
	// for agent favourite
	public static function getFavouriteQuery() {
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'd.*',
				'd.name AS title',
				'd.username AS alias',
				'b.published',
				"count(talent_id) AS count" 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__agent_favourite AS a' );
		$query->leftJoin ( '#__agent AS b ON b.id=a.agent_id' );
		$query->leftJoin ( '#__users AS d ON d.id=b.user_id' );
		$query->group ( 'agent_id' );
		
		return $query;
	}
	public static function getListFavouritesQuery($cid) {
		$query = static::getFavouriteQuery ();
		return $query;
	}
	//
	public static function getFavourite($talent_id, $agent_id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'a.id' )->from ( '#__agent_favourite AS a' );
		$query->where ( 'a.talent_id = ' . ( int ) $talent_id );
		$query->where ( 'a.agent_id = ' . ( int ) $agent_id );
		return $db->setQuery ( $query )->loadResult ();
	}
	//
	public static function getCountTalentsOfAgent($agent_id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'count(a.talent_id)' )->from ( '#__agent_favourite AS a' );
		$query->where ( 'a.agent_id = ' . ( int ) $agent_id );
		return $db->setQuery ( $query )->loadResult ();
	}
	//
	public static function getAddRemoveTalentButton($i, $agent_id, $talent_id) {
		$favourite = static::getFavourite ( $talent_id, $agent_id );
		$options = array (
				'active_title' => $favourite ? 'COM_TALENT_REMOVE_FROM_FAVOURITE' : 'COM_TALENT_ADD_TO_FAVOURITE',
				'inactive_title' => $favourite ? 'COM_TALENT_REMOVE_FROM_FAVOURITE' : 'COM_TALENT_ADD_TO_FAVOURITE',
				'tip' => true,
				'active_class' => $favourite ? 'remove' : 'plus-2',
				'inactive_class' => $favourite ? 'remove' : 'plus-2',
				'enabled' => true,
				'translate' => true,
				'checkbox' => 'cb',
				'prefix' => 'favourites.' 
		);
		return JHtml::_ ( 'jgrid.action', $i, $favourite ? 'remove' : 'add', $options );
	}
	//
	public static function countTalentInFavourite($talent_id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( 'count(a.talent_id)' )->from ( '#__agent_favourite AS a' );
		$query->where ( 'a.talent_id = ' . ( int ) $talent_id );
		return $db->setQuery ( $query )->loadResult ();
	}
}