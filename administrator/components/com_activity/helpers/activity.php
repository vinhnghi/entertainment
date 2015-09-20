<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
//
JLoader::register ( 'JToolBarHelper', JPATH_ADMINISTRATOR . '/includes/toolbar.php' );
JLoader::register ( 'JSubMenuHelper', JPATH_ADMINISTRATOR . '/includes/subtoolbar.php' );
//
abstract class ActivityHelper {
	//
	public static function addSubmenu($submenu) {
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_ACTIVITY_SUBMENU_TYPES' ), 'index.php?option=com_activity&view=types', $submenu == 'types' );
		JSubMenuHelper::addEntry ( JText::_ ( 'COM_ACTIVITY_SUBMENU_ACTIVITIES' ), 'index.php?option=com_activity', $submenu == 'activities' );
	}
	//
	public static function isSite($layout = 'default') {
		$app = JFactory::getApplication ();
		$jinput = $app->input;
		return ($app->isSite () && $jinput->getCmd ( 'layout', 'default' ) == $layout);
	}
	//
	public static function canSubmit() {
		$user = JFactory::getUser ();
		$client = static::getClientByUserId ( $user->id );
		if (! $client) {
			JError::raiseError ( 500, JText::_ ( 'COM_ACTIVITY_NO_PERMISSION' ) );
			return false;
		}
		return true;
	}
	//
	public static function getActions($messageId = 0, $asset = 'activity') {
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
	//
	public static function truncate($string = "", $max_words) {
		$array = array_filter ( explode ( ' ', $string ), 'strlen' );
		if (count ( $array ) > $max_words && $max_words > 0)
			$string = implode ( ' ', array_slice ( $array, 0, $max_words ) ) . '...';
		return $string;
	}
	//
	public static function getActivityTypeQuery() {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*' 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__activity_type AS a' );
		$query->where ( 'id <> 1' );
		return $query;
	}
	//
	public static function getListActivityTypesQuery() {
		return static::getActivityTypeQuery ();
	}
	//
	public static function getActivityType($id) {
		$db = JFactory::getDbo ();
		$query = static::getActivityTypeQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	//
	public static function getActivityQuery($id, $cid = null) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*' 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__activity AS a' );
		if ($cid) {
			$fields = array (
					'c.id AS typeid',
					'c.title AS typetitle' 
			);
			$query->select ( implode ( ",", $fields ) );
			$query->innerJoin ( '#__activity_activity_type AS b ON a.id = b.activity_id' );
			$query->innerJoin ( '#__activity_type AS c ON c.id = b.activity_type_id' );
			$query->where ( 'b.activity_type_id = ' . ( int ) $cid );
		}
		if ($id) {
			$query->where ( 'a.id = ' . ( int ) $id );
		}
		return $query;
	}
	//
	public static function getActivity($id, $cid = null) {
		$db = JFactory::getDbo ();
		$query = static::getActivityQuery ( $id, $cid );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	//
	public static function getListActivitiesQuery() {
		$query = static::getActivityQuery ( null, null );
		return $query;
	}
	//
	public static function getListActivitiesOfTypeQuery($cid) {
		$query = static::getActivityQuery ( null, $cid );
		return $query;
	}
	//
	public static function getActivityImages($id) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'a.*' );
		$query->from ( '#__activity_assets AS a' );
		$query->where ( '(a.activity_id = ' . ( int ) $id . ')' );
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
	//
	public static function getActivityTalents($id, $published = false) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT b.*, c.name as title, c.email' );
		$query->from ( '#__activity_talent AS a' );
		$query->leftJoin ( '#__talent AS b ON a.talent_id=b.id' );
		$query->leftJoin ( '#__users AS c ON b.user_id=c.id' );
		$query->where ( 'a.activity_id = ' . ( int ) $id );
		if ($published) {
			$query->where ( 'b.published = 1' );
			$query->where ( 'c.block = 0' );
			$query->where ( 'c.activation = ""' );
		}
		$db->setQuery ( $query );
		return $db->loadObjectList ();
	}
	//
	public static function getClientQuery() {
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
				$db->Quote ( JText::_ ( 'COM_ACTVITY_SEARCH_SECTION_CLIENT' ) ) . ' AS section',
				'"200" AS browsernav' 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__client AS a' );
		$query->innerJoin ( '#__users AS d ON d.id=a.user_id' );
		return $query;
	}
	//
	public static function getClient($id) {
		$db = JFactory::getDbo ();
		$query = static::getClientQuery ();
		$query->where ( 'a.id = ' . ( int ) $id );
		$db->setQuery ( $query );
		return static::updateClientData ( $db->loadObject () );
	}
	//
	public static function getClientByUserId($userId) {
		$db = JFactory::getDbo ();
		$query = static::getClientQuery ();
		$query->where ( 'a.user_id = ' . ( int ) $userId );
		$db->setQuery ( $query );
		return static::updateClientData ( $db->loadObject () );
	}
	//
	public static function updateClientData($client) {
		if ($client) {
			// Convert the metadata field to an array.
			$registry = new Registry ();
			$registry->loadString ( $client->metadata );
			$client->metadata = $registry->toArray ();
			
			// Convert the images field to an array.
			$registry = new Registry ();
			$registry->loadString ( $client->images );
			$client->images = $registry->toArray ();
			$client->clienttext = trim ( $client->fulltext ) != '' ? $client->introtext . "<hr id=\"system-readmore\" />" . $client->fulltext : $client->introtext;
			
			$client->user_details = array (
					'id' => $client->user_id,
					'name' => $client->name,
					'username' => $client->username,
					'email' => $client->email 
			);
			// Load the profile data from the database.
			$db = JFactory::getDbo ();
			$db->setQuery ( 'SELECT profile_key, profile_value FROM #__user_profiles' . ' WHERE user_id = ' . ( int ) $client->user_id . " AND profile_key LIKE 'profile.%'" . ' ORDER BY ordering' );
			$results = $db->loadRowList ();
			// Merge the profile data.
			foreach ( $results as $v ) {
				$k = str_replace ( 'profile.', '', $v [0] );
				$client->user_details [$k] = json_decode ( $v [1], true );
				if ($client->user_details [$k] === null) {
					$client->user_details [$k] = $v [1];
				}
			}
		}
		return $client;
	}
}