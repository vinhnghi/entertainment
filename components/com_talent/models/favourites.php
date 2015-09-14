<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelFavourites extends JModelList {
	protected function getListQuery() {
		$user = JFactory::getUser ();
		if (! TalentHelper::isAgent ( $user )) {
			$this->setError ( JText::_ ( 'COM_TALENT_ERROR_CANNOT_ACCESS' ) );
			return false;
		}
		
		$agent = TalentHelper::getAgentByUserId ( $user->id );
		
		$jinput = JFactory::getApplication ()->input;
		$db = JFactory::getDbo ();
		// Initialize variables.
		$query = TalentHelper::getListFavouritesQuery ( null );
		
		$query->where ( 'a.agent_id = ' . ( int ) $agent->id );
		
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
		$query->order ( 'id asc' );
		
		return $query;
	}
}