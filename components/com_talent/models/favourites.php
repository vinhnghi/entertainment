<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelFavourites extends JModelList {
	//
	public static function hasIntersection($string1, $string2) {
		$words = array_filter ( explode ( " ", $string2 ) );
		foreach ( $words as $word ) {
			if (stripos ( $string1, $word ) !== false) {
				return true;
			}
		}
		return false;
	}
	//
	public function getItems() {
		$items = parent::getItems ();
		
		global $email, $race, $location, $hair_color, $eye_color, $gender;
		$email = $this->getState ( 'filter.email' );
		$race = $this->getState ( 'filter.race' );
		$location = $this->getState ( 'filter.location' );
		$hair_color = $this->getState ( 'filter.hair_color' );
		$eye_color = $this->getState ( 'filter.eye_color' );
		$gender = $this->getState ( 'filter.gender' );
		
		if ($email || $race || $location || $hair_color || $eye_color || strlen ( $gender )) {
			$items = array_filter ( $items, function ($item) {
				global $email, $race, $location, $hair_color, $eye_color, $gender;
				$talent = SiteTalentHelper::getTalent ( $item->id );
				$user_details = $talent->user_details;
				if ($email && ! TalentModelFavourites::hasIntersection ( $user_details ['email'], $email )) {
					return false;
				}
				if ($race && ! TalentModelFavourites::hasIntersection ( $user_details ['race'], $race )) {
					return false;
				}
				if ($location && ! TalentModelFavourites::hasIntersection ( $user_details ['location'], $location )) {
					return false;
				}
				if ($hair_color && ! TalentModelFavourites::hasIntersection ( $user_details ['hair_color'], $hair_color )) {
					return false;
				}
				if ($eye_color && ! TalentModelFavourites::hasIntersection ( $user_details ['eye_color'], $eye_color )) {
					return false;
				}
				if (strlen ( $gender ) && $user_details ['gender'] != $gender) {
					return false;
				}
				return true;
			} );
		}
		
		return $items;
	}
	//
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
			$query->where ( 'name LIKE ' . $like );
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
	//
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
}