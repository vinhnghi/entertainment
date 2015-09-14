<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableFavourite extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent_favourite', 'id', $db );
	}
	public function bind($array, $ignore = '') {
		$this->data = $array;
		return parent::bind ( $array, $ignore );
	}
	protected function countTalents() {
		$count = 0;
		$talents = $this->data ['favouritetalents'];
		if ($talents && count ( $talents )) {
			foreach ( $talents as $talent ) {
				$count ++;
			}
		}
		return $count;
	}
	public function store($updateNulls = false) {
		$result = parent::store ( $updateNulls );
		if ($result) {
			if ($this->data) {
				$db = JFactory::getDbo ();
				// save favourite talents
				$query = $db->getQuery ( true );
				$query->delete ( $db->quoteName ( '#__agent_favourite_talent' ) );
				$query->where ( array (
						$db->quoteName ( 'agent_favourite_id' ) . '=' . $this->id 
				) );
				$db->setQuery ( $query );
				$db->execute ();
				if ($this->countTalents ()) {
					$columns = array (
							'agent_favourite_id',
							'talent_id' 
					);
					$query->insert ( $db->quoteName ( '#__agent_favourite_talent' ) )->columns ( $db->quoteName ( $columns ) );
					foreach ( $this->data ['favouritetalents'] as $talent ) {
						if (( int ) $talent > 0)
							$query->values ( "{$this->id},{$talent}" );
					}
					$db->setQuery ( $query );
					$db->execute ();
				}
			}
		}
		return $result;
	}
}