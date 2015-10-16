<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		// Initialize variables.
		$query = TalentHelper::getListTalentsQuery ( $jinput->get ( 'cid', $this->getState ( 'filter.cid', 0 ) ) );
		
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $this->_db->quote ( '%' . $search . '%' );
			$query->where ( 'name LIKE ' . $like );
		}
		
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if ($published !== null && $published !== '')
			$query->where ( 'a.published = ' . ( int ) $published );
		
		$params = $jinput->getArray ( array () );
		if (isset ( $params ['id'] )) {
			$ids = array ();
			foreach ( $params ['id'] as $id ) {
				if (is_numeric ( $id )) {
					array_push ( $ids, $id );
				}
			}
			$ids = implode ( ',', $ids );
			if ($ids)
				$query->where ( "(a.id NOT IN ({$ids}))" );
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		$query->order ( 'id asc' );
		
		return $query;
	}
}