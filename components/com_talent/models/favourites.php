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
		// Initialize variables.
		$query = TalentHelper::getListTalentsQuery ( null );
		$query->leftJoin ( '#__agent AS e ON d.id=e.user_id' );
		$query->leftJoin ( '#__agent_favourite AS f ON e.id=f.agent_id' );
		$query->where ( 'f.agent_id = ' . $agent->id );
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