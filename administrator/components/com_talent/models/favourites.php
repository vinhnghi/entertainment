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
	private function getListAgentQuery() {
		$db = JFactory::getDbo ();
		// Initialize variables.
		$query = TalentHelper::getListFavouritesQuery ( null );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
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
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		return $query;
	}
	private function getListTalentQuery($id) {
		$db = JFactory::getDbo ();
		$query = TalentHelper::getListTalentsQuery ( null );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
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
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		return $query;
	}
}