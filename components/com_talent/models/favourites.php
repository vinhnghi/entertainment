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
		
		$db = JFactory::getDbo ();
		$query = TalentHelper::getListTalentsQuery ( null );
		$query->innerJoin ( '#__agent_favourite AS z ON z.talent_id = a.id AND z.agent_id = ' . $agent->id );
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
	//
	public function removeTalentsFromFavourite($ids) {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->delete ( $db->quoteName ( '#__agent_favourite' ) )->where ( $db->quoteName ( 'talent_id' ) . ' IN (' . implode ( ',', $ids ) . ')' );
		$db->setQuery ( $query );
		$db->execute ();
	}
	//
	public function addTalentsToFavourite($ids) {
		static::removeTalentsFromFavourite ( $ids );
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->insert ( $db->quoteName ( '#__agent_favourite' ) )->columns ( $db->quoteName ( array (
				'agent_id',
				'talent_id' 
		) ) );
		$user = JFactory::getUser ();
		$agent = SiteTalentHelper::getAgentByUserId ( $user->id );
		foreach ( $ids as $talent_id ) {
			$query->values ( $agent->id . ',' . $talent_id );
		}
		$db->setQuery ( $query );
		$db->execute ();
	}
}