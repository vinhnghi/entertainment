<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class TalentModelType extends JModelAdmin {
	protected $text_prefix = 'COM_TALENT';
	public $typeAlias = 'com_talent.type';
	protected $canDo;
	public function getTable($type = 'Type', $prefix = 'TalentTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'type' )->get ( 'core.delete' );
		}
	}
	protected function canEditState($record) {
		$user = JFactory::getUser ();
		
		// Check for existing article.
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'type' )->get ( 'core.edit.state' );
		} else {
			return parent::canEditState ( 'com_talent' );
		}
	}
	protected function prepareTable($table) {
		$date = JFactory::getDate ();
		$user = JFactory::getUser ();
		$input = JFactory::getApplication ()->input;
		
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
				$query = $db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( '#__talent_type' );
				
				$db->setQuery ( $query );
				$max = $db->loadResult ();
				
				$table->ordering = $max + 1;
			}
		} else {
			// Set the values
			$table->modified = $date->toSql ();
			$table->modified_by = $user->get ( 'id' );
		}
		
		$parentTable = clone $table;
	}
	public function getItem($pk = null) {
		if ($item = TalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'id', 0 ) )) {
			// Convert the metadata field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->metadata );
			$item->metadata = $registry->toArray ();
			
			// Convert the images field to an array.
			$registry = new Registry ();
			$registry->loadString ( $item->images );
			$item->images = $registry->toArray ();
			
			$item->typetext = trim ( $item->fulltext ) != '' ? $item->introtext . "<hr id=\"system-readmore\" />" . $item->fulltext : $item->introtext;
			// Load associated content items
			$app = JFactory::getApplication ();
			$assoc = JLanguageAssociations::isEnabled ();
			
			if ($assoc) {
				$item->associations = array ();
				if ($item->id != null) {
					$associations = JLanguageAssociations::getAssociations ( 'com_talent', '#__talent_type', 'com_talent.type', $item->id );
					foreach ( $associations as $tag => $association ) {
						$item->associations [$tag] = $association->id;
					}
				}
			}
			
			return $item;
		}
		$item = new stdClass ();
		$item->id = '';
		return $item;
	}
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication ()->input;
		
		// Get the form.
		$form = $this->loadForm ( 'com_talent.type', 'type', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		if (empty ( $form )) {
			return false;
		}
		
		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get ( 'a_id' )) {
			$id = $jinput->get ( 'a_id', 0 );
		}  // The back end uses id so we use that the rest of the time and set it to 0 by default.
else {
			$id = $jinput->get ( 'id', 0 );
		}
		// Determine correct permissions to check.
		if ($this->getState ( 'type.id' )) {
			$id = $this->getState ( 'type.id' );
		}
		
		$user = JFactory::getUser ();
		
		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (! TalentHelper::getActions ( ( int ) $id, 'type' )->get ( 'core.edit.state' ))) {
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
		$data = $app->getUserState ( 'com_talent.edit.type.data', array () );
		
		if (empty ( $data )) {
			$data = $this->getItem ();
			
			// Prime some default values.
			if ($this->getState ( 'type.id' ) == 0) {
				$filters = ( array ) $app->getUserState ( 'com_talent.types.filter' );
			}
		}
		
		$this->preprocessData ( 'com_talent.type', $data );
		
		return $data;
	}
	public function save($data) {
		$this->bind ( $data );
		return parent::save ( $data );
	}
	public function bind(&$data) {
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match ( $pattern, $data ['typetext'] );
		if ($tagPos == 0) {
			$data ['introtext'] = $data ['typetext'];
			$data ['fulltext'] = '';
		} else {
			list ( $data ['introtext'], $data ['fulltext'] ) = preg_split ( $pattern, $data ['typetext'], 2 );
		}
		
		if (isset ( $data ['images'] ) && is_array ( $data ['images'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['images'] );
			$data ['images'] = ( string ) $registry;
		}
		
		if (isset ( $data ['metadata'] ) && is_array ( $data ['metadata'] )) {
			$registry = new Registry ();
			$registry->loadArray ( $data ['metadata'] );
			$data ['metadata'] = ( string ) $registry;
		}
	}
	public function getScript() {
		return 'administrator/components/com_talent/src/js/type.js';
	}
}