<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityModelActivities extends JModelList {
	//
	public function getActivityType() {
		return SiteActivityHelper::getActivityType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	//
	protected function getListQuery() {
		$db = JFactory::getDbo ();
		$query = SiteActivityHelper::getListActivitiesOfTypeQuery ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		
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