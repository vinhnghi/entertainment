<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
require_once JPATH_SITE . '/components/com_talent/helpers/talent.php';
//
class PlgSearchTalent extends JPlugin {
	//
	public function onContentSearchAreas() {
		static $areas = array (
				'talents' => 'PLG_SEARCH_TALENT' 
		);
		return $areas;
	}
	//
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
				$wheres2 [] = 'LOWER(title) LIKE ' . $text;
				$wheres2 [] = 'LOWER(text) LIKE ' . $text;
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
					$wheres2 [] = 'LOWER(title) LIKE ' . $word;
					$wheres2 [] = 'LOWER(text) LIKE ' . $word;
					$wheres [] = implode ( ' OR ', $wheres2 );
				}
				$where = '(' . implode ( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
		
		// Ordering of the results
		switch ($ordering) {
			// Oldest first
			case 'oldest' :
				$order = 'created ASC';
			// Newest first
			case 'newest' :
				$order = 'created DESC';
			// Default setting: alphabetic, ascending
			// Alphabetic, ascending
			case 'alpha' :
			default :
				$order = 'title ASC';
		}
		
		// Replace nameofplugin
		$section = JText::_ ( 'PLG_SEARCH_TALENT_SEARCH_SECTION_TYPE' );
		// The database query; differs per situation! It will look something like this (example from newsfeed search plugin):
		$query = SiteTalentHelper::getListTalentTypeQuery ();
		$query->where ( '(' . $where . ')' );
		$query->where ( 'a.published = 1' );
		$query->order ( $order );
		// Set query
		$limit = $this->params->def ( 'search_limit', 50 );
		$db->setQuery ( $query, 0, $limit );
		$types = $db->loadObjectList ();
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $types as $key => $row ) {
			$types [$key]->href = 'index.php?option=com_talent&view=talents&cid=' . $row->id;
		}
		
		// //////////////////////////////////////////
		$section = JText::_ ( 'PLG_SEARCH_TALENT_SEARCH_SECTION_TALENT' );
		// The database query; differs per situation! It will look something like this (example from newsfeed search plugin):
		$query = SiteTalentHelper::getListTalentsQuery ( null );
		$query->where ( '(' . $where . ')' );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		$query->order ( $order );
		// Set query
		$limit = $this->params->def ( 'search_limit', 50 );
		$db->setQuery ( $query, 0, $limit );
		$talents = $db->loadObjectList ();
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $talents as $key => $row ) {
			$talents [$key]->href = 'index.php?option=com_talent&view=talent&cid=&id=' . $row->id;
		}
		
		$rs = array_merge ( $types, $talents );
		// Return the search results in an array
		return $rs;
	}
}
