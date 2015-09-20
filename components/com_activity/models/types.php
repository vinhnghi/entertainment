<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityModelTypes extends JModelList {
	//
	protected function getListQuery() {
		$db = JFactory::getDbo ();
		$query = SiteActivityHelper::getListActivityTypesQuery ();
		$query->where ( 'a.published = 1' );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		return $query;
	}
	public function getCss() {
		return 'components/com_activity/src/css/activity.css';
	}
}