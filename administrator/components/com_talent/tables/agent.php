<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableAgent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent', 'id', $db );
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
}