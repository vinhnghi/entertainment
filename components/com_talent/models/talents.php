<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	public function getItems() {
		$items = parent::getItems ();
		$agent = SiteTalentHelper::getAgentByUserId ( JFactory::getUser ()->id );
		foreach ( $items as $item ) {
			$item->favourite = SiteTalentHelper::getFavourite ( $item->id, $agent->id );
		}
		return $items;
	}
	public function getTalentType() {
		return SiteTalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	protected function getListQuery() {
		$query = SiteTalentHelper::getListTalentsQuery ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
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

