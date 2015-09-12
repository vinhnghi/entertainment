<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		$db = JFactory::getDbo ();
		// Initialize variables.
		$query = TalentHelper::getListTalentsQuery ( null );
		
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		if ($published !== null && $published !== '')
			$query->where ( 'a.published = ' . ( int ) $published );
		
		$params = $jinput->getArray ( array () );
		if (isset ( $params ['id'] )) {
			$ids = implode ( ',', $params ['id'] );
			if ($ids)
				$query->where ( "(id NOT IN ({$ids}))" );
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		$query->order ( 'id asc' );
		return $query;
	}
}