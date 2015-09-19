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
	protected function getWhereForOneWord($text, $isType = true) {
		$db = JFactory::getDbo ();
		$wheres = array ();
		$text = $db->Quote ( '%' . $db->escape ( $text, true ) . '%', false );
		$wheres2 = array ();
		$wheres2 [] = $isType ? 'LOWER(a.title) LIKE ' . $text : 'LOWER(d.name) LIKE ' . $text;
		$wheres2 [] = 'LOWER(a.introtext) LIKE ' . $text;
		return '(' . implode ( ') OR (', $wheres2 ) . ')';
	}
	//
	protected function getWhere($text, $phrase = '', $isType = true) {
		switch ($phrase) {
			// Search exact
			case 'exact' :
				return self::getWhereForOneWord ( $text, $isType );
			// Search all or any
			case 'all' :
			case 'any' :
			// Set default
			default :
				$wheres = array ();
				$words = explode ( ' ', $text );
				$wheres = array ();
				foreach ( $words as $word ) {
					$wheres [] = self::getWhereForOneWord ( $word, $isType );
				}
				return '(' . implode ( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
		}
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
		$query->where ( '(' . self::getWhere ( $text, $phrase, true ) . ')' );
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
		$query->where ( '(' . self::getWhere ( $text, $phrase, false ) . ')' );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		$query->order ( $order );
		// Set query
		$limit = $this->params->def ( 'search_limit', 50 );
		$db->setQuery ( $query, 0, $limit );
		$talents = $db->loadObjectList ();
		// The 'output' of the displayed link. Again a demonstration from the newsfeed search plugin
		foreach ( $talents as $key => $row ) {
			$talents [$key]->href = 'index.php?option=com_talent&view=talent&id=' . $row->id;
		}
		
		$rs = array_merge ( $types, $talents );
		// Return the search results in an array
		return $rs;
	}
}
