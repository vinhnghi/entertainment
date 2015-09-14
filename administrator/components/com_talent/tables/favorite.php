<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableFavorite extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent_favorite', 'id', $db );
	}
	public function bind($array, $ignore = '') {
		$this->data = $array;
		return parent::bind ( $array, $ignore );
	}
	protected function countTalents() {
		$count = 0;
		$talents = $this->data ['favoritetalents'];
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
				// save favorite talents
				$query = $db->getQuery ( true );
				$query->delete ( $db->quoteName ( '#__agent_favorite_talent' ) );
				$query->where ( array (
						$db->quoteName ( 'agent_favorite_id' ) . '=' . $this->id 
				) );
				$db->setQuery ( $query );
				$db->execute ();
				if ($this->countTalents ()) {
					$columns = array (
							'agent_favorite_id',
							'talent_id' 
					);
					$query->insert ( $db->quoteName ( '#__agent_favorite_talent' ) )->columns ( $db->quoteName ( $columns ) );
					foreach ( $this->data ['favoritetalents'] as $talent ) {
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