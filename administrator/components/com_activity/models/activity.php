<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
//
class ActivityModelActivity extends JModelAdmin {
	//
	public function getActivityType() {
		return SiteActivityHelper::getActivityType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	//
	public function getTable($type = 'Activity', $prefix = 'ActivityTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	//
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			return ActivityHelper::getActions ( ( int ) $record->id, 'activity' )->get ( 'core.delete' );
		}
	}
	//
	protected function canEditState($record) {
		$user = JFactory::getUser ();
		// Check for existing article.
		if (! empty ( $record->id )) {
			return ActivityHelper::getActions ( ( int ) $record->id, 'activity' )->get ( 'core.edit.state' );
		} else {
			return parent::canEditState ( 'com_activity' );
		}
	}
	//
	public function getItem($pk = null) {
		// return true;
		$app = JFactory::getApplication ();
		$jinput = $app->input;
		if ($app->isSite () && $jinput->getCmd ( 'layout', 'default' ) == 'default') {
			// Initialize variables.
			$db = JFactory::getDbo ();
			$query = SiteActivityHelper::getActivityQuery ( $jinput->get ( 'id', 0 ), $jinput->get ( 'cid', 0 ) );
			$query->where ( 'c.published = 1' );
			$query->where ( 'a.published = 1' );
			$db->setQuery ( $query );
			$data = $db->loadObject ();
			if (! $data) {
				throw new Exception ( JText::_ ( 'COM_ACTIVITY_ACTIVITY_NOT_FOUND' ) );
				return;
			}
			return $data;
		}
		
		if ($item = parent::getItem ( $pk )) {
			// Convert the params field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->attribs );
			$item->attribs = $registry->toArray ();
			
			// Convert the metadata field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->metadata );
			$item->metadata = $registry->toArray ();
			
			// Convert the images field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->images );
			$item->images = $registry->toArray ();
			
			// Convert the urls field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->urls );
			$item->urls = $registry->toArray ();
			
			$item->activitytext = trim ( $item->fulltext ) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
			
			if (! empty ( $item->id )) {
				$item->tags = new JHelperTags ();
				$item->tags->getTagIds ( $item->id, 'com_activity.activity' );
			}
			
			// set types
			$db = JFactory::getDbo ();
			$query = $db->getQuery ( true );
			$query->select ( $db->quoteName ( array (
					'activity_type_id' 
			) ) );
			$query->from ( $db->quoteName ( '#__activity_activity_type' ) );
			$query->where ( $db->quoteName ( 'activity_id' ) . ' = ' . ( int ) $item->id );
			$db->setQuery ( $query );
			$item->parent_id = $db->loadColumn ();
		}
		
		// Load associated content items
		$assoc = JLanguageAssociations::isEnabled ();
		
		if ($assoc) {
			$item->associations = array ();
			
			if ($item->id != null) {
				$associations = JLanguageAssociations::getAssociations ( 'com_activity', '#__activity', 'com_activity.activity', $item->id );
				
				foreach ( $associations as $tag => $association ) {
					$item->associations [$tag] = $association->id;
				}
			}
		}
		
		return $item;
	}
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		$form = $this->loadForm ( 'com_activity.activity', 'activity', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		if (empty ( $form )) {
			return false;
		}
		$jinput = JFactory::getApplication ()->input;
		
		if ($jinput->get ( 'a_id' )) { // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
			$id = $jinput->get ( 'a_id', 0 );
		} else { // The back end uses id so we use that the rest of the time and set it to 0 by default.
			$id = $jinput->get ( 'id', 0 );
		}
		// Determine correct permissions to check.
		if ($this->getState ( 'activity.id' )) {
			$id = $this->getState ( 'activity.id' );
		}
		
		$user = JFactory::getUser ();
		
		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && ! ActivityHelper::getActions ( ( int ) $id, 'activity' )->get ( 'core.edit.state' )) {
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
	//
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$app = JFactory::getApplication ();
		$data = $app->getUserState ( 'com_activity.edit.activity.data', array () );
		if (empty ( $data )) {
			$data = $this->getItem ();
			
			// Prime some default values.
			if ($this->getState ( 'activity.id' ) == 0) {
				$filters = ( array ) $app->getUserState ( 'com_activity.activities.filter' );
			}
		}
		$this->preprocessData ( 'com_activity.activity', $data );
		return $data;
	}
	//
	protected function prepareTable($table) {
		$date = JFactory::getDate ();
		$user = JFactory::getUser ();
		$input = JFactory::getApplication ()->input;
		
		// Alter the title for save as copy
		if ($input->get ( 'task' ) == 'save2copy') {
			$origTable = clone $this->getTable ();
			$origTable->load ( $input->getInt ( 'id' ) );
			if ($table->title == $origTable->title)
				list ( $this->title, $table->alias ) = $this->generateNewTitle ( '', $table->alias, $table->title );
			else {
				if ($table->alias == $origTable->alias)
					$table->alias = '';
			}
			$table->published = 0;
		}
		
		// Automatic handling of alias for empty fields
		if (in_array ( $input->get ( 'task' ), array (
				'apply',
				'save',
				'save2new' 
		) ) && ( int ) $input->get ( 'id' ) == 0) {
			if (! $table->alias) {
				if (JFactory::getConfig ()->get ( 'unicodeslugs' ) == 1)
					$table->alias = JFilterOutput::stringURLUnicodeSlug ( $table->title );
				else
					$table->alias = JFilterOutput::stringURLSafe ( $table->title );
				$origTable = clone $this->getTable ();
				$origTable->load ( array (
						'alias' => $table->alias 
				) );
				if ($origTable->load ( array (
						'alias' => $table->alias 
				) )) {
					$msg = JText::_ ( 'Alias already existed so a number was added at the end.' );
				}
				list ( $table->title, $table->alias ) = $this->generateNewTitle ( '', $table->alias, $table->title );
				if (isset ( $msg ))
					JFactory::getApplication ()->enqueueMessage ( $msg, 'warning' );
			}
		}
		
		if (empty ( $table->id )) {
			$table->created = $date->toSql ();
			$table->created_by = $user->get ( 'id' );
			// Set ordering to the last item if not set
			if (empty ( $table->ordering )) {
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( '#__activity' );
				
				$db->setQuery ( $query );
				$max = $db->loadResult ();
				
				$table->ordering = $max + 1;
			}
		} else {
			// Set the values
			$table->modified = $date->toSql ();
			$table->modified_by = $user->get ( 'id' );
		}
	}
	public function save($data) {
		$this->bind ( $data );
		return parent::save ( $data );
	}
	public function bind(&$data, $ignore = '') {
		if (isset ( $data ['created_by_alias'] )) {
			$data ['created_by_alias'] = $filter->clean ( $data ['created_by_alias'], 'TRIM' );
		}
		
		if (isset ( $data ['images'] ) && is_array ( $data ['images'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['images'] );
			$data ['images'] = ( string ) $registry;
		}
		
		if (isset ( $data ['urls'] ) && is_array ( $data ['urls'] )) {
			foreach ( $data ['urls'] as $i => $url ) {
				if ($url != false && ($i == 'urla' || $i == 'urlb' || $i == 'urlc')) {
					$data ['urls'] [$i] = JStringPunycode::urlToPunycode ( $url );
				}
			}
			
			$registry = new Registry ();
			$registry->loadArray ( $data ['urls'] );
			$data ['urls'] = ( string ) $registry;
		}
		
		// Search for the {readmore} tag and split the text up accordingly.
		if (isset ( $data ['activitytext'] )) {
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match ( $pattern, $data ['activitytext'] );
			
			if ($tagPos == 0) {
				$data ['introtext'] = $data ['activitytext'];
				$data ['fulltext'] = '';
			} else {
				list ( $data ['introtext'], $data ['fulltext'] ) = preg_split ( $pattern, $data ['activitytext'], 2 );
			}
		}
		
		if (isset ( $data ['attribs'] ) && is_array ( $data ['attribs'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['attribs'] );
			$data ['attribs'] = ( string ) $registry;
		}
		
		if (isset ( $data ['metadata'] ) && is_array ( $data ['metadata'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['metadata'] );
			$data ['metadata'] = ( string ) $registry;
		}
		return $data;
	}
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache ( 'com_activity' );
	}
	public function getScript() {
		return 'administrator/components/com_activity/src/js/activity.js';
	}
	public function getCss() {
		return 'administrator/components/com_activity/src/css/activity.css';
	}
}