<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTypes extends JModelList {
	protected function getListQuery() {
		// Initialize variables.
		$query = SiteTalentHelper::getListTalentTypesQuery ();
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		return $query;
	}
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
}