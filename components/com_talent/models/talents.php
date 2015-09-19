<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	public function getTalentType() {
		return SiteTalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	protected function getListQuery() {
		$query = SiteTalentHelper::getListTalentsQuery ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
		$query->where ( 'a.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'name' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		return $query;
	}
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
}

