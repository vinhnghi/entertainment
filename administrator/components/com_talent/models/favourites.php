<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelFavourites extends JModelList {
	//
	public function getItems() {
		$items = parent::getItems ();
		$jinput = JFactory::getApplication ()->input;
		$id = $jinput->get ( 'id', 0 );
		if ($id) { // List all talents
			$agent = TalentHelper::getAgentByUserId ( $id );
			foreach ( $items as $item ) {
				$item->favourite = TalentHelper::getFavourite ( $item->id, $agent->id );
			}
		} else {
			foreach ( $items as $item ) {
				$item->count = TalentHelper::getCountTalentsOfAgent ( $item->id );
			}
		}
		return $items;
	}
	//
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		$id = $jinput->get ( 'id', 0 );
		if (! $id) { // List all agents
			return $this->getListAgentQuery ();
		} else { // List all talents
			return $this->getListTalentQuery ( $id );
		}
	}
	//
	private function getListAgentQuery() {
		// Initialize variables.
		$query = TalentHelper::getListAgentsQuery ( null );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $this->_db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if ($published !== null && $published !== '') {
			$query->where ( 'b.published = ' . ( int ) $published );
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		return $query;
	}
	//
	private function getListTalentQuery($id) {
		$query = TalentHelper::getListTalentsQuery ( null );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $this->_db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if ($published !== null && $published !== '') {
			$query->where ( 'a.published = ' . ( int ) $published );
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		return $query;
	}
	//
	public function removeTalentsFromFavourite($ids) {
		$query = $this->_db->getQuery ( true )->delete ( $this->_db->quoteName ( '#__agent_favourite' ) )->where ( $this->_db->quoteName ( 'talent_id' ) . ' IN (' . implode ( ',', $ids ) . ')' );
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
	}
	//
	public function addTalentsToFavourite($ids) {
		$this->removeTalentsFromFavourite ( $ids );
		$query = $this->_db->getQuery ( true );
		$query->insert ( $this->_db->quoteName ( '#__agent_favourite' ) )->columns ( $this->_db->quoteName ( array (
				'agent_id',
				'talent_id' 
		) ) );
		$jinput = JFactory::getApplication ()->input;
		$id = $jinput->get ( 'id', 0 );
		foreach ( $ids as $talent_id ) {
			$query->values ( $id . ',' . $talent_id );
		}
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
	}
}