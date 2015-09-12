<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableTalent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__talent', 'id', $db );
	}
	public function bind($array, $ignore = '') {
		$this->data = $array;
		if (isset ( $array ['params'] ) && is_array ( $array ['params'] )) {
			// Convert the params field to a string.
			$parameter = new JRegistry ();
			$parameter->loadArray ( $array ['params'] );
			$array ['params'] = ( string ) $parameter;
		}
		
		// Bind the rules.
		if (isset ( $array ['rules'] ) && is_array ( $array ['rules'] )) {
			$rules = new JAccessRules ( $array ['rules'] );
			$this->setRules ( $rules );
		}
		
		return parent::bind ( $array, $ignore );
	}
	public function load($pk = null, $reset = true) {
		if (parent::load ( $pk, $reset )) {
			// Convert the params field to a registry.
			$params = new JRegistry ();
			$params->loadString ( $this->params, 'JSON' );
			
			$this->params = $params;
			return true;
		} else {
			return false;
		}
	}
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_talent.message.' . ( int ) $this->$k;
	}
	protected function _getAssetTitle() {
		return $this->title;
	}
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL) {
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance ( 'Asset' );
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId ();
		
		// The item has the component as asset-parent
		$assetParent->loadByName ( 'com_talent' );
		
		// Return the found asset-parent-id
		if ($assetParent->id) {
			$assetParentId = $assetParent->id;
		}
		return $assetParentId;
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