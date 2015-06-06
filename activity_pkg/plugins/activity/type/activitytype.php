<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class PlgSearchActivityType extends JPlugin {
	function onContentSearchAreas() {
		static $areas = array (
				'activitytype' => 'Activity Type' 
		);
		return $areas;
	}
	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) 
	{
		require_once JPATH_SITE . '/components/com_activity/router.php';
		
		$db = JFactory::getDbo();
		
		$user = JFactory::getUser ();
		$groups = implode ( ',', $user->getAuthorisedViewLevels () );
		
		// If the array is not correct, return it:
		if (is_array ( $areas )) {
			if (! array_intersect ( $areas, array_keys ( $this->onContentSearchAreas () ) )) {
				return array ();
			}
		}
		
		$text = trim ( $text );
		
		if ($text == '') {
			return array ();
		}
		
		$wheres = array ();
		switch ($phrase) {
			// Search exact
			case 'exact' :
				$text = $db->Quote ( '%' . $db->escape ( $text, true ) . '%', false );
				$wheres2 = array ();
				$wheres2 [] = 'LOWER(a.title) LIKE ' . $text;
				$where = '(' . implode ( ') OR (', $wheres2 ) . ')';
				break;
			
			// Search all or any
			case 'all' :
			case 'any' :
			// Set default
			default :
				$words = explode ( ' ', $text );
				$wheres = array ();
				foreach ( $words as $word ) {
					$word = $db->Quote ( '%' . $db->escape ( $word, true ) . '%', false );
					$wheres2 = array ();
					$wheres2 [] = 'LOWER(a.title) LIKE ' . $word;
					$wheres [] = implode ( ' OR ', $wheres2 );
				}
				$where = '(' . implode ( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		
		// Ordering of the results
		switch ($ordering) {
			
			// Alphabetic, ascending
			case 'alpha' :
				$order = 'a.title ASC';
				break;
			
			// Oldest first
			case 'oldest' :
			
			// Popular first
			case 'popular' :
			
			// Newest first
			case 'newest' :
			
			// Default setting: alphabetic, ascending
			default :
				$order = 'a.title ASC';
		}
		
		// Replace nameofplugin
		$section = JText::_ ( 'ActivityType' );
		
		// The database query; differs per situation! It will look something like this (example from newsfeed search plugin):
		$query = $db->getQuery ( true );
		$query->select ( 'a.title AS title, a.title AS text, a.created AS created, a.id' );
		$query->select ( $db->Quote ( $section ) . ' AS section' );
		$query->select ( '"1" AS browsernav' );
		$query->from ( '#__activity_type AS a' );
		$query->where ( '(' . $where . ')' . 'AND a.published = 1' );
		$query->order ( $order );
		
		// Set query
		$db->setQuery ( $query, 0, $limit );
		$rows = $db->loadObjectList ();
		
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $rows as $key => $row ) {
			$rows [$key]->href = 'index.php?option=com_activity&view=activities&cid=' . $row->id;
		}
		
		// Return the search results in an array
		return $rows;
	}
}
