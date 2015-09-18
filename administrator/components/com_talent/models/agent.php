<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\Registry\Registry;
class TalentModelAgent extends JModelAdmin {
	protected $text_prefix = 'COM_TALENT';
	public $typeAlias = 'com_talent.agent';
	protected $profileFields = array (
			'tel',
			'location' 
	);
	public function getTable($type = 'Agent', $prefix = 'TalentTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'agent' )->get ( 'core.delete' );
		}
	}
	protected function canEditState($record) {
		$user = JFactory::getUser ();
		// Check for existing article.
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'agent' )->get ( 'core.edit.state' );
		} else {
			return parent::canEditState ( 'com_talent' );
		}
	}
	protected function prepareTable($table) {
		$date = JFactory::getDate ();
		$user = JFactory::getUser ();
		$input = JFactory::getApplication ()->input;
		if (empty ( $table->id )) {
			// Set ordering to the last item if not set
			if (empty ( $table->ordering )) {
				$query = $this->_db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( '#__agent' );
				$this->_db->setQuery ( $query );
				$max = $this->_db->loadResult ();
				$table->ordering = $max + 1;
			}
		}
	}
	public function getItem($pk = null) {
		return TalentHelper::getAgent ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
	}
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication ()->input;
		
		// Get the form.
		$form = $this->loadForm ( 'com_talent.agent', 'agent', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		if (empty ( $form )) {
			return false;
		}
		$id = $jinput->get ( 'id', 0 );
		// Determine correct permissions to check.
		if ($this->getState ( 'agent.id' )) {
			$id = $this->getState ( 'agent.id' );
		}
		
		// Modify the form based on Edit State access controls.
		if ($id != 0 && ! TalentHelper::getActions ( ( int ) $id, 'agent' )->get ( 'core.edit.state' )) {
			// Disable fields for display.
			$form->setFieldAttribute ( 'ordering', 'disabled', 'true' );
			$form->setFieldAttribute ( 'published', 'disabled', 'true' );
			
			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute ( 'ordering', 'filter', 'unset' );
			$form->setFieldAttribute ( 'published', 'filter', 'unset' );
		}
		
		return $form;
	}
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$app = JFactory::getApplication ();
		$data = $app->getUserState ( 'com_talent.edit.agent.data', array () );
		
		if (empty ( $data )) {
			$data = $this->getItem ();
			
			// Prime some default values.
			if ($this->getState ( 'agent.id' ) == 0) {
				$filters = ( array ) $app->getUserState ( 'com_talent.agents.filter' );
			}
		}
		
		$this->preprocessData ( 'com_talent.agent', $data );
		
		return $data;
	}
	public function buildData(&$data) {
		$user_data = array (
				'id' => $data ['user_details'] ['id'],
				'groups' => array (
						TalentHelper::getAgentUserGroup ()->id 
				),
				'name' => $data ['user_details'] ['name'],
				'username' => $data ['user_details'] ['username'],
				'email' => $data ['user_details'] ['email'] 
		);
		if ($data ['user_details'] ['password']) {
			$user_data ['password'] = $data ['user_details'] ['password'];
		}
		$data ['user'] = $user_data;
		
		$profile_data = array (
				'talentusergroup' => 'Agent' 
		);
		foreach ( $this->profileFields as $field ) {
			$profile_data [$field] = $data ['user_details'] [$field];
		}
		$data ['profile'] = $profile_data;
		
		$agent_data = array (
				'id' => $data ['id'],
				'parent_id' => $data ['parent_id'],
				'published' => $data ['published'],
				'metakey' => $data ['metakey'],
				'metadesc' => $data ['metadesc'] 
		);
		
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match ( $pattern, $data ['agenttext'] );
		if ($tagPos == 0) {
			$agent_data ['introtext'] = $data ['agenttext'];
			$agent_data ['fulltext'] = '';
		} else {
			list ( $agent_data ['introtext'], $agent_data ['fulltext'] ) = preg_split ( $pattern, $data ['agenttext'], 2 );
		}
		
		if (isset ( $data ['images'] ) && is_array ( $data ['images'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['images'] );
			$agent_data ['images'] = ( string ) $registry;
		}
		
		if (isset ( $data ['metadata'] ) && is_array ( $data ['metadata'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['metadata'] );
			$agent_data ['metadata'] = ( string ) $registry;
		}
		$data ['agent'] = $agent_data;
	}
	public function save($data) {
		$this->buildData ( $data );
		// save user
		$this->saveUser ( $data ['user'] );
		// save user profile
		$this->saveProfile ( $data ['profile'] );
		// save agent
		$this->saveAgent ( $data ['agent'] );
		return true;
	}
	public function saveUser($data) {
		$pk = (! empty ( $data ['id'] )) ? $data ['id'] : ( int ) $this->getState ( 'user.id' );
		$user = JUser::getInstance ( $pk );
		$my = JFactory::getUser ();
		// Make sure that we are not removing ourself from Super Admin group
		$iAmSuperAdmin = $my->authorise ( 'core.admin' );
		if ($iAmSuperAdmin && $my->get ( 'id' ) == $pk) {
			// Check that at least one of our new groups is Super Admin
			$stillSuperAdmin = false;
			$myNewGroups = $data ['groups'];
			foreach ( $myNewGroups as $group ) {
				$stillSuperAdmin = ($stillSuperAdmin) ? ($stillSuperAdmin) : JAccess::checkGroup ( $group, 'core.admin' );
			}
			if (! $stillSuperAdmin) {
				$this->setError ( JText::_ ( 'COM_USERS_USERS_ERROR_CANNOT_DEMOTE_SELF' ) );
				return false;
			}
		}
		// Bind the data.
		if (! $user->bind ( $data )) {
			$this->setError ( $user->getError () );
			return false;
		}
		// Store the data.
		if (! $user->save ()) {
			$this->setError ( $user->getError () );
			return false;
		}
		$this->setState ( 'user.id', $user->id );
		return true;
	}
	public function saveProfile($data) {
		$userId = ( int ) $this->getState ( 'user.id' );
		$query = $this->_db->getQuery ( true )->delete ( $this->_db->quoteName ( '#__user_profiles' ) )->where ( $this->_db->quoteName ( 'user_id' ) . ' = ' . ( int ) $userId )->where ( $this->_db->quoteName ( 'profile_key' ) . ' LIKE ' . $this->_db->quote ( 'profile.%' ) );
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
		
		$tuples = array ();
		$order = 1;
		foreach ( $data as $k => $v ) {
			$tuples [] = '(' . $userId . ', ' . $this->_db->quote ( 'profile.' . $k ) . ', ' . $this->_db->quote ( json_encode ( $v ) ) . ', ' . ($order ++) . ')';
		}
		$this->_db->setQuery ( 'INSERT INTO #__user_profiles VALUES ' . implode ( ', ', $tuples ) );
		$this->_db->execute ();
		return true;
	}
	public function saveAgent($data) {
		$data ['user_id'] = ( int ) $this->getState ( 'user.id' );
		return parent::save ( $data );
	}
	public function delete(&$pks) {
		$user = JFactory::getUser ();
		$table = JTable::getInstance ( 'User', 'JTable' );
		$pks = ( array ) $pks;
		
		// Check if I am a Super Admin
		$iAmSuperAdmin = $user->authorise ( 'core.admin' );
		
		if (in_array ( $user->id, $pks )) {
			$this->setError ( JText::_ ( 'COM_USERS_USERS_ERROR_CANNOT_DELETE_SELF' ) );
			
			return false;
		}
		// Iterate the items to delete each one.
		foreach ( $pks as $i => $pk ) {
			$agent = TalentHelper::getAgent ( $pk );
			if ($agent) {
				$userId = $agent->user_id;
				if ($table->load ( $userId )) {
					// Access checks.
					$allow = $user->authorise ( 'core.delete', 'com_users' );
					// Don't allow non-super-admin to delete a super admin
					$allow = (! $iAmSuperAdmin && JAccess::check ( $userId, 'core.admin' )) ? false : $allow;
					if ($allow) {
						// Get users data for the users to delete.
						$user_to_delete = JFactory::getUser ( $userId );
						// Fire the before delete event.
						if (! $table->delete ( $userId )) {
							$this->setError ( $table->getError () );
							return false;
						} else {
							$this->deleteProfile ( $userId );
							$this->deleteAgent ( $userId );
						}
					} else {
						// Prune items that you can't change.
						unset ( $pks [$i] );
						JError::raiseWarning ( 403, JText::_ ( 'JERROR_CORE_DELETE_NOT_PERMITTED' ) );
					}
				} else {
					$this->setError ( $table->getError () );
					return false;
				}
			}
		}
		return true;
	}
	public function deleteProfile($userId) {
		$query = $this->_db->getQuery ( true )->delete ( $this->_db->quoteName ( '#__user_profiles' ) )->where ( $this->_db->quoteName ( 'user_id' ) . ' = ' . ( int ) $userId )->where ( $this->_db->quoteName ( 'profile_key' ) . ' LIKE ' . $this->_db->quote ( 'profile.%' ) );
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
	}
	public function deleteAgent($userId) {
		$agent = TalentHelper::getAgentByUserId ( $userId );
		$query = $this->_db->getQuery ( true )->delete ( $this->_db->quoteName ( '#__agent' ) )->where ( $this->_db->quoteName ( 'user_id' ) . ' = ' . ( int ) $userId );
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
	}
	public function getScript() {
		return 'administrator/components/com_talent/src/js/talent.js';
	}
	public function getCss() {
		return 'administrator/components/com_talent/src/css/talent.css';
	}
}