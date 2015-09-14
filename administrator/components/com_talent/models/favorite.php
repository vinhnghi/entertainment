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
	protected function generateNewTitle($agent_id, $alias, $title) {
		// Alter the title & alias
		$table = $this->getTable ();
		while ( $table->load ( array (
				'alias' => $alias,
				'agent_id' => $agent_id 
		) ) ) {
			$title = JString::increment ( $title );
			$alias = JString::increment ( $alias, 'dash' );
		}
		return array (
				$title,
				$alias 
		);
	}
	protected function prepareTable($table) {
		$user = JFactory::getUser ();
		$input = JFactory::getApplication ()->input;
		// Automatic handling of alias for empty fields
		if (in_array ( $input->get ( 'task' ), array (
				'apply',
				'save',
				'save2new' 
		) )) {
			if (! $table->alias) {
				if (JFactory::getConfig ()->get ( 'unicodeslugs' ) == 1)
					$table->alias = JFilterOutput::stringURLUnicodeSlug ( $table->title );
				else
					$table->alias = JFilterOutput::stringURLSafe ( $table->title );
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
		if (JFactory::getConfig ()->get ( 'unicodeslugs' ) == 1)
			$data ['alias'] = JFilterOutput::stringURLUnicodeSlug ( $data ['title'] );
		else
			$data ['alias'] = JFilterOutput::stringURLSafe ( $data ['title'] );
		
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$tagPos = preg_match ( $pattern, $data ['favoritetext'] );
		if ($tagPos == 0) {
			$data ['introtext'] = $data ['favoritetext'];
			$data ['fulltext'] = '';
		} else {
			list ( $data ['introtext'], $data ['fulltext'] ) = preg_split ( $pattern, $data ['favoritetext'], 2 );
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
		$table = $this->getTable ();
		if ($table->load ( array (
				'alias' => $data ['alias'],
				'agent_id' => $data ['agent_id'] 
		) ) && $table->id != $data ['id']) {
			$this->setError ( JText::_ ( 'This favorite already exists.' ) );
			return false;
		}
		
		return parent::save ( $data );
	}
	public function getScript() {
		return 'administrator/components/com_talent/src/js/favorite.js';
	}
	public function getCss() {
		return 'administrator/components/com_talent/src/css/talent.css';
	}
}