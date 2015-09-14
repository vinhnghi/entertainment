<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableTalent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__talent', 'id', $db );
	}
	public function bind($array, $ignore = '') {
		$this->data = $array;
		return parent::bind ( $array, $ignore );
	}
	protected function countTalentImages() {
		$count = 0;
		$images = $this->data ['talentimages'];
		if ($images && count ( $images )) {
			foreach ( $images as $image ) {
				if ($image ['src'])
					$count ++;
			}
		}
		return $count;
	}
	public function store($updateNulls = false) {
		$result = parent::store ( $updateNulls );
		if ($result) {
			$db = JFactory::getDbo ();
			
			if ($this->data) {
				// save talent types
				if ($this->data ['parent_id']) {
					$query = $db->getQuery ( true );
					$query->delete ( $db->quoteName ( '#__talent_type_talent' ) );
					$query->where ( array (
							$db->quoteName ( 'talent_id' ) . '=' . $this->id 
					) );
					$db->setQuery ( $query );
					$db->execute ();
					$columns = array (
							'talent_id',
							'talent_type_id' 
					);
					$query->insert ( $db->quoteName ( '#__talent_type_talent' ) )->columns ( $db->quoteName ( $columns ) );
					foreach ( $this->data ['parent_id'] as $typeid ) {
						$query->values ( $this->id . ',' . $typeid );
					}
					$db->setQuery ( $query );
					$db->execute ();
				}
				// save talent images
				if ($this->data ['talentimages']) {
					$query = $db->getQuery ( true );
					$query->delete ( $db->quoteName ( '#__talent_assets' ) );
					$query->where ( array (
							$db->quoteName ( 'talent_id' ) . '=' . $this->id 
					) );
					$db->setQuery ( $query );
					$db->execute ();
					if ($this->countTalentImages ()) {
						$columns = array (
								'talent_id',
								'src',
								'alt',
								'caption' 
						);
						$query->insert ( $db->quoteName ( '#__talent_assets' ) )->columns ( $db->quoteName ( $columns ) );
						foreach ( $this->data ['talentimages'] as $image ) {
							if ($image ['src']) {
								$query->values ( "{$this->id},'{$image['src']}','{$image['alt']}','{$image['caption']}'" );
							}
						}
						$db->setQuery ( $query );
						$db->execute ();
					}
				}
			}
		}
		return $result;
	}
}