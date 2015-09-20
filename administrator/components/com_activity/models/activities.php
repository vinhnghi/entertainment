<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityModelActivities extends JModelList {
	//
	protected function getListQuery() {
		$db = JFactory::getDbo ();
		$query = ActivityHelper::getListActivitiesQuery ();
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if (is_numeric ( $published )) {
			$query->where ( 'published = ' . ( int ) $published );
		} elseif ($published === '') {
			$query->where ( '(published IN (0, 1))' );
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		return $query;
	}
}