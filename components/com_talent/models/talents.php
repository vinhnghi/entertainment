<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	public function getTalentType() {
		return SiteTalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	protected function getListQuery() {
		$db = JFactory::getDbo ();
		$query = SiteTalentHelper::getListTalentsQuery ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'name' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		return $query;
	}
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
	public function getJs() {
		return 'components/com_talent/src/js/talent.js';
	}
}

