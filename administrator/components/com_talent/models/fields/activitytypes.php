<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JFormHelper::loadFieldClass ( 'list' );

class JFormFieldActivityTypes extends JFormFieldList 
{
	protected $type = 'ActivityTypes';
	protected function getOptions() 
	{
		$options = array ();
		$published = $this->element ['published'] ? $this->element ['published'] : array (
				0,
				1 
		);
		$name = ( string ) $this->element ['name'];
		
		// Let's get the id for the current item, either category or content item.
		$jinput = JFactory::getApplication ()->input;
		
		$oldCat = $this->element ['multiple'] == true ? 0 : $jinput->get ( 'id', 0 );
		$oldParent = $this->form->getValue ( $name, 0 );
		$extension = $this->element ['extension'] ? ( string ) $this->element ['extension'] : ( string ) $jinput->get ( 'extension', 'com_activity' );

		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT a.id AS value, a.title AS text, a.level, a.published, a.lft' );
		$subQuery = $db->getQuery ( true )->select ( 'id,title,level,published,parent_id,extension,lft,rgt' )->from ( '#__activity_type' );

		// Filter by the extension type
		if ($this->element ['parent'] == true || $jinput->get ( 'option' ) == 'com_activity') {
			$subQuery->where ( '(extension = ' . $db->quote ( $extension ) . ' OR parent_id = 0)' );
		} else {
			$subQuery->where ( '(extension = ' . $db->quote ( $extension ) . ')' );
		}
		
		// Filter on the published state
		if (is_numeric ( $published )) {
			$subQuery->where ( 'published = ' . ( int ) $published );
		} elseif (is_array ( $published )) {
			JArrayHelper::toInteger ( $published );
			$subQuery->where ( 'published IN (' . implode ( ',', $published ) . ')' );
		}
		
		$query->from ( '(' . $subQuery->__toString () . ') AS a' )->join ( 'LEFT', $db->quoteName ( '#__activity_type' ) . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt' );
		$query->where ( '(a.id <> '.$oldCat.')' );
		$query->order ( 'a.lft ASC' );
		
		// Get the options.
		$db->setQuery ( $query );

		try {
			$options = $db->loadObjectList ();
		} catch ( RuntimeException $e ) {
			JError::raiseWarning ( 500, $e->getMessage () );
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for($i = 0, $n = count ( $options ); $i < $n; $i ++) {
			// Translate ROOT
			if ($this->element ['parent'] == true || $jinput->get ( 'option' ) == 'com_activity') {
				if ($options [$i]->level == 0) {
					$options [$i]->text = JText::_ ( 'Undefined' );
				}
			}
			
			if ($options [$i]->published == 1) {
				$options [$i]->text = str_repeat ( '- ', $options [$i]->level ) . $options [$i]->text;
			} else {
				$options [$i]->text = str_repeat ( '- ', $options [$i]->level ) . '[' . $options [$i]->text . ']';
			}
		}
		
		// Get the current user object.
		$user = JFactory::getUser ();
		
		// For new items we want a list of categories you are allowed to create in.
		if ($oldCat == 0) {
			foreach ( $options as $i => $option ) {
				if ($user->authorise ( 'core.create', $extension . '.type.' . $option->value ) != true && $option->level != 0) {
					unset ( $options [$i] );
				}
			}
		} else {
			foreach ( $options as $i => $option ) {
				if ($user->authorise ( 'core.edit.state', $extension . '.type.' . $oldCat ) != true && ! isset ( $oldParent )) {
					if ($option->value != $oldCat) {
						unset ( $options [$i] );
					}
				}
				
				if ($user->authorise ( 'core.edit.state', $extension . '.type.' . $oldCat ) != true && (isset ( $oldParent )) && $option->value != $oldParent) {
					unset ( $options [$i] );
				}
				
				// However, if you can edit.state you can also move this to another category for which you have
				// create permission and you should also still be able to save in the current category.
				if (($user->authorise ( 'core.create', $extension . '.type.' . $option->value ) != true) && ($option->value != $oldCat && ! isset ( $oldParent ))) {
					{
						unset ( $options [$i] );
					}
				}
				
				if (($user->authorise ( 'core.create', $extension . '.type.' . $option->value ) != true) && (isset ( $oldParent )) && $option->value != $oldParent) {
					{
						unset ( $options [$i] );
					}
				}
			}
		}
		
		if (($this->element ['parent'] == true || $jinput->get ( 'option' ) == 'com_activity') && (isset ( $row ) && ! isset ( $options [0] )) && isset ( $this->element ['show_root'] )) {
			if ($row->parent_id == '') {
				$parent = new stdClass ();
				$parent->text = JText::_ ( 'Undefined' );
				array_unshift ( $options, $parent );
			}
			
			array_unshift ( $options, JHtml::_ ( 'select.option', '0', JText::_ ( 'JGLOBAL_ROOT' ) ) );
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge ( parent::getOptions (), $options );
		
		return $options;
	}
}