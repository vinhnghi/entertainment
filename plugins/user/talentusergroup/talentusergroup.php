<?php
defined ( 'JPATH_BASE' ) or die ();
class PlgUserTalentUserGroup extends JPlugin {
	private $key = 'talentusergroup';
	private $forms = array (
			'com_users.profile',
			'com_users.user',
			'com_users.registration',
			'com_admin.profile' 
	);
	public function onContentPrepareData($context, $data) {
		// Check we are manipulating a valid form.
		if (! in_array ( $context, $this->forms )) {
			return true;
		}
		
		if (is_object ( $data )) {
			$userId = isset ( $data->id ) ? $data->id : 0;
			
			if (! isset ( $data->{$this->key} ) and $userId > 0) {
				// Load the profile data from the database.
				$db = JFactory::getDbo ();
				$db->setQuery ( 'SELECT profile_value FROM #__user_profiles' . ' WHERE user_id = ' . ( int ) $userId . " AND profile_key = 'profile.{$this->key}'" );
				
				try {
					$data->{$this->key} = $db->loadResult ();
				} catch ( RuntimeException $e ) {
					$this->_subject->setError ( $e->getMessage () );
					return false;
				}
			}
		}
		return true;
	}
	public function onContentPrepareForm($form, $data) {
		if (! ($form instanceof JForm)) {
			$this->_subject->setError ( 'JERROR_NOT_A_FORM' );
			
			return false;
		}
		
		// Check we are manipulating a valid form.
		$name = $form->getName ();
		if (! in_array ( $name, $this->forms )) {
			return true;
		}
		
		if ($this->canShow ( $form )) {
			JForm::addFormPath ( __DIR__ . '/talentusergroups' );
			$form->loadFile ( 'talentusergroup', false );
		}
		
		return true;
	}
	public function onUserBeforeSave($user, $isnew, $data) {
		return true;
	}
	public function getGroupByName($name) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( '*' )->from ( '#__usergroups AS a' );
		$query->where ( 'a.title = ' . $db->quote ( $name ) );
		$db->setQuery ( $query );
		return $db->loadObject ();
	}
	public function isAllowed() {
		// return true;
		$app = JFactory::getApplication ();
		return $app->isSite ();
	}
	public function canShow($form) {
		// return true;
		$name = $form->getName ();
		return $this->isAllowed () && $name == 'com_users.registration';
	}
	public function saveACL($group) {
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
	public function onUserAfterSave($data, $isNew, $result, $error) {
		$userId = JArrayHelper::getValue ( $data, 'id', 0, 'int' );
		if ($userId && $result && isset ( $data [$this->key] ) && (count ( $data [$this->key] ))) {
			try {
				// set user group
				if ($this->isAllowed ()) {
					$groupName = $data [$this->key] [$this->key];
					if ($groupName) {
						$groupName = ucfirst ( $groupName );
						$group = $this->getGroupByName ( $groupName );
						if (! $group) {
							$registeredGroup = $this->getGroupByName ( 'Public' );
							$table = JTable::getInstance ( 'UserGroup' );
							$table->save ( array (
									'parent_id' => $registeredGroup->id,
									'title' => $groupName 
							) );
							$table->rebuild ();
							$group = $this->getGroupByName ( $groupName );
						}
						
						$this->saveACL ( $group );
						JUserHelper::setUserGroups ( $userId, array (
								$group->id 
						) );
						if ($group) {
							$tableName = strtolower ( '#__' . $groupName );
							$db = JFactory::getDBO ();
							$query = $db->getQuery ( true );
							$query->select ( '*' )->from ( $tableName );
							$query->where ( 'user_id = ' . ( int ) $userId );
							$db->setQuery ( $query );
							$user = $db->loadObject ();
							if (! $user) {
								$row = new JObject ();
								$row->user_id = $userId;
								$row->published = 1;
								$ret = $db->insertObject ( $tableName, $row );
								$db->insertid ();
							}
						}
					}
				}
			} catch ( RuntimeException $e ) {
				$this->_subject->setError ( $e->getMessage () );
				return false;
			}
		}
		
		return true;
	}
	public function onUserAfterDelete($user, $success, $msg) {
		if (! $success) {
			return false;
		}
		$userId = JArrayHelper::getValue ( $user, 'id', 0, 'int' );
		if ($userId) {
			try {
				$db = JFactory::getDbo ();
				$db->setQuery ( 'DELETE FROM #__user_profiles WHERE user_id = ' . $userId . " AND profile_key LIKE 'profile.{$this->key}'" );
				$db->execute ();
			} catch ( Exception $e ) {
				$this->_subject->setError ( $e->getMessage () );
				return false;
			}
		}
		return false;
	}
}
