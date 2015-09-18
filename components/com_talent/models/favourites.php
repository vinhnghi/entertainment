<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelFavourites extends JModelList {
	protected function getListQuery() {
		$user = JFactory::getUser ();
		if (! SiteTalentHelper::isAgent ( $user )) {
			$this->setError ( JText::_ ( 'COM_TALENT_ERROR_CANNOT_ACCESS' ) );
			return false;
		}
		
		$agent = SiteTalentHelper::getAgentByUserId ( $user->id );
		
		$query = TalentHelper::getListTalentsQuery ( null );
		$query->innerJoin ( '#__agent_favourite AS z ON z.talent_id = a.id AND z.agent_id = ' . $agent->id );
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
		$user = JFactory::getUser ();
		$agent = SiteTalentHelper::getAgentByUserId ( $user->id );
		foreach ( $ids as $talent_id ) {
			$query->values ( $agent->id . ',' . $talent_id );
		}
		$this->_db->setQuery ( $query );
		$this->_db->execute ();
	}
}