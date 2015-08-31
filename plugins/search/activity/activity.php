<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class PlgSearchActivity extends JPlugin {
	function onContentSearchAreas() {
		static $areas = array (
				'activity' => 'Search - Activity/Activity Type' 
		);
		return $areas;
	}
	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null) {
		$db = JFactory::getDbo ();
		
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
		$section = JText::_ ( 'Activity Type' );
		
		// The database query; differs per situation! It will look something like this (example from newsfeed search plugin):
		$query = $db->getQuery ( true );
		$query->select ( 'a.title AS title, a.id, a.introtext as text, a.created' );
		$query->select ( $db->Quote ( $section ) . ' AS section' );
		$query->select ( '"1" AS browsernav' );
		$query->from ( '#__activity_type AS a' );
		$query->where ( '(' . $where . ')' . 'AND a.published = 1' );
		$query->order ( $order );
		
		// Set query
		$limit = $this->params->def ( 'search_limit', 50 );
		$db->setQuery ( $query, 0, $limit );
		$types = $db->loadObjectList ();
		
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $types as $key => $row ) {
			$types [$key]->href = 'index.php?option=com_activity&view=activities&cid=' . $row->id;
		}

		$section = JText::_ ( 'Activity' );
		// The database query; differs per situation! It will look something like this (example from newsfeed search plugin):
		$query = $db->getQuery ( true );
		$query->select ( 'a.title AS title, a.id, a.introtext as text, a.created, c.id as cid' );
		$query->select ( $db->Quote ( $section ) . ' AS section' );
		$query->select ( '"1" AS browsernav' );
		$query->from ( '#__activity AS a' );
		$query->leftJoin ( '#__activity_activity_type AS b ON a.id=b.activity_id' );
		$query->leftJoin ( '#__activity_type AS c ON c.id=b.activity_type_id' );
		$query->where ( '(' . $where . ')' . 'AND a.published = 1' );
		$query->order ( $order );
		
		// Set query
		$limit = $this->params->def ( 'search_limit', 50 );
		$db->setQuery ( $query, 0, $limit );
		$activities = $db->loadObjectList ();
		
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $activities as $key => $row ) {
			$activities [$key]->href = 'index.php?option=com_activity&view=activity&cid=' . $row->cid . '&id=' . $row->id;
		}
		
		
		// Return the search results in an array
		return array_merge($types, $activities);
	}
}
