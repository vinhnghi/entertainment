<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTypes extends JModelList {
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		// Initialize variables.
		$query = TalentHelper::getListTalentTypesQuery ();
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $this->_db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if ($published !== null && $published !== '')
			$query->where ( 'a.published = ' . ( int ) $published );
		
		$params = $jinput->getArray ( array () );
		if (isset ( $params ['id'] )) {
			$ids = implode ( ',', array_filter ( explode ( ',', $params ['id'] ), 'strlen' ) );
			if ($ids)
				$query->where ( "(id NOT IN ({$ids}))" );
		}
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		$query->order ( 'id asc' );
		
		return $query;
	}
}