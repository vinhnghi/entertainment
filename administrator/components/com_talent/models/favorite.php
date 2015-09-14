<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\Registry\Registry;
class TalentModelFavorite extends JModelAdmin {
	protected $text_prefix = 'COM_TALENT';
	public $typeAlias = 'com_talent.favorite';
	public function getTable($type = 'Favorite', $prefix = 'TalentTable', $config = array()) {
		return JTable::getInstance ( $type, $prefix, $config );
	}
	protected function canDelete($record) {
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'favorite' )->get ( 'core.delete' );
		}
	}
	protected function canEditState($record) {
		$user = JFactory::getUser ();
		// Check for existing article.
		if (! empty ( $record->id )) {
			return TalentHelper::getActions ( ( int ) $record->id, 'favorite' )->get ( 'core.edit.state' );
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
				$favorite = $origTable->load ( array (
						'alias' => $table->alias,
						'agent_id' => $table->agent_id 
				) );
				if ($favorite) {
					$msg = JText::_ ( 'Alias already existed so a number was added at the end.' );
				}
				list ( $table->title, $table->alias ) = $this->generateNewTitle ( '', $table->alias, $table->title );
				if (isset ( $msg ))
					JFactory::getApplication ()->enqueueMessage ( $msg, 'warning' );
			}
		}
		
		if (empty ( $table->id )) {
			// Set ordering to the last item if not set
			if (empty ( $table->ordering )) {
				$db = JFactory::getDbo ();
				$query = $db->getQuery ( true )->select ( 'MAX(ordering)' )->from ( '#__agent_favorite' );
				
				$db->setQuery ( $query );
				$max = $db->loadResult ();
				
				$table->ordering = $max + 1;
			}
		}
		return clone $table;
	}
	public function getItem($pk = null) {
		return TalentHelper::getFavorite ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
	}
	public function getForm($data = array(), $loadData = true) {
		$jinput = JFactory::getApplication ()->input;
		
		// Get the form.
		$form = $this->loadForm ( 'com_talent.favorite', 'favorite', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
		if (empty ( $form )) {
			return false;
		}
		$id = $jinput->get ( 'id', 0 );
		// Determine correct permissions to check.
		if ($this->getState ( 'favorite.id' )) {
			$id = $this->getState ( 'favorite.id' );
		}
		
		// Modify the form based on Edit State access controls.
		if ($id != 0 && ! TalentHelper::getActions ( ( int ) $id, 'favorite' )->get ( 'core.edit.state' )) {
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
		$data = $app->getUserState ( 'com_talent.edit.favorite.data', array () );
		
		if (empty ( $data )) {
			$data = $this->getItem ();
			
			// Prime some default values.
			if ($this->getState ( 'favorite.id' ) == 0) {
				$filters = ( array ) $app->getUserState ( 'com_talent.favorites.filter' );
			}
		}
		
		$this->preprocessData ( 'com_talent.favorite', $data );
		
		return $data;
	}
	public function buildData(&$data) {
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match ( $pattern, $data ['talenttext'] );
		if ($tagPos == 0) {
			$data ['introtext'] = $data ['talenttext'];
			$data ['fulltext'] = '';
		} else {
			list ( $data ['introtext'], $data ['fulltext'] ) = preg_split ( $pattern, $data ['talenttext'], 2 );
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
	public function save($data) {
		$this->buildData ( $data );
		return parent::save ( $data );
	}
	public function getScript() {
		return 'administrator/components/com_talent/src/js/talent.js';
	}
	public function getCss() {
		return 'administrator/components/com_talent/src/css/talent.css';
	}
}