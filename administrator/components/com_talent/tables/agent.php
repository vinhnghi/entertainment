<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableAgent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent', 'id', $db );
	}
	public function bind($array, $ignore = '') {
		$this->data = $array;
		return parent::bind ( $array, $ignore );
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
}