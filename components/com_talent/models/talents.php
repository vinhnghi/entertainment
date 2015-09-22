<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	public function getTalentType() {
		return SiteTalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	public function getItems() {
		$items = parent::getItems ();
		
		global $email, $race, $location, $hair_color, $eye_color, $gender;
		$email = $this->getState ( 'filter.email' );
		$race = $this->getState ( 'filter.race' );
		$location = $this->getState ( 'filter.location' );
		$hair_color = $this->getState ( 'filter.hair_color' );
		$eye_color = $this->getState ( 'filter.eye_color' );
		$gender = $this->getState ( 'filter.gender' );
		
		if ($email || $race || $location || $hair_color || $eye_color || count ( $gender ) == 1) {
			$items = array_filter ( $items, function ($item) {
				global $email, $race, $location, $hair_color, $eye_color, $gender;
				$talent = SiteTalentHelper::getTalent ( $item->id );
				$user_details = $talent->user_details;
				if ($email && ! SiteActivityHelper::hasIntersection ( $user_details ['email'], $email )) {
					return false;
				}
				if ($race && ! SiteActivityHelper::hasIntersection ( $user_details ['race'], $race )) {
					return false;
				}
				if ($location && ! SiteActivityHelper::hasIntersection ( $user_details ['location'], $location )) {
					return false;
				}
				if ($hair_color && ! SiteActivityHelper::hasIntersection ( $user_details ['hair_color'], $hair_color )) {
					return false;
				}
				if ($eye_color && ! SiteActivityHelper::hasIntersection ( $user_details ['eye_color'], $eye_color )) {
					return false;
				}
				if (count ( $gender ) == 1 && $user_details ['gender'] != $gender [0]) {
					return false;
				}
				return true;
			} );
		}
		
		return $items;
	}
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		$query = SiteTalentHelper::getListTalentsQuery ( $jinput->get ( 'cid', 0 ) );
		$query->where ( 'a.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		$params = $jinput->getArray ( array () );
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		if (! empty ( $search )) {
			$like = $this->_db->quote ( '%' . $search . '%' );
			$query->where ( 'name LIKE ' . $like );
		}
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
		$orderCol = $this->state->get ( 'list.ordering', 'name' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		$query->order ( $this->_db->escape ( $orderCol ) . ' ' . $this->_db->escape ( $orderDirn ) );
		return $query;
	}
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
}

