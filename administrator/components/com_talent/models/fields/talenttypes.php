<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

JFormHelper::loadFieldClass ( 'list' );
class JFormFieldTalentTypes extends JFormFieldList {
	protected $type = 'TalentTypes';
	protected function getOptions() {
		$options = array ();
		$published = $this->element ['published'] ? $this->element ['published'] : array (
				0,
				1 
		);
		$name = ( string ) $this->element ['name'];
		
		// Let's get the id for the current item, either category or content item.
		$jinput = JFactory::getApplication ()->input;
		
		$oldCat = $this->element ['multiple'] == true ? 0 : $jinput->get ( 'id', 0 );
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT a.id AS value, a.title AS text, a.published' );
		
		$query->from ( '#__talent_type AS a' );
		$query->where ( '(a.id <> ' . $oldCat . ')' );
		// Filter on the published state
		if (is_numeric ( $published )) {
			$query->where ( 'published = ' . ( int ) $published );
		} elseif (is_array ( $published )) {
			JArrayHelper::toInteger ( $published );
			$query->where ( 'published IN (' . implode ( ',', $published ) . ')' );
		}
		$query->order ( 'a.title ASC' );
		
		// Get the options.
		$db->setQuery ( $query );
		
		try {
			$options = $db->loadObjectList ();
		} catch ( RuntimeException $e ) {
			JError::raiseWarning ( 500, $e->getMessage () );
		}
		
		// Pad the option text with spaces using depth level as a multiplier.
		for($i = 0, $n = count ( $options ); $i < $n; $i ++) {
			if ($options [$i]->published == 1) {
				$options [$i]->text = $options [$i]->text;
			} else {
				$options [$i]->text = '[' . $options [$i]->text . ']';
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge ( parent::getOptions (), $options );
		
		return $options;
	}
}