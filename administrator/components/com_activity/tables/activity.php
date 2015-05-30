<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityTableActivity extends JTable 
{
	protected $_data = NULL;
	
	function __construct(&$db) {
		parent::__construct ( '#__activity', 'id', $db );
	}
	
	public function bind($array, $ignore = '') 
	{
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
	
	public function load($pk = null, $reset = true) 
	{
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
	
	protected function _getAssetName() 
	{
		$k = $this->_tbl_key;
		return 'com_activity.message.' . ( int ) $this->$k;
	}
	
	protected function _getAssetTitle() 
	{
		return $this->title;
	}
	
	protected function _getAssetParentId(JTable $table = NULL, $id = NULL) 
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance ( 'Asset' );
		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId ();
		
		// The item has the component as asset-parent
		$assetParent->loadByName ( 'com_activity' );
		
		// Return the found asset-parent-id
		if ($assetParent->id) {
			$assetParentId = $assetParent->id;
		}
		return $assetParentId;
	}
	
	public function store($updateNulls = false)
	{
		$result = parent::store($updateNulls);
		if	($result)
		{
			$db = JFactory::getDbo();
			
			//save activity types
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__activity_activity_type'));
			$query->where(array(
				$db->quoteName('activity_id') .'='. $this->id,
			));
			$db->setQuery($query);
			$db->execute();
			$columns = array('activity_id', 'activity_type_id');
			$query->insert($db->quoteName('#__activity_activity_type'))->columns($db->quoteName($columns));
			foreach ($this->data['parent_id'] as $typeid) {
				$query->values($this->id.','.$typeid);
			}
			$db->setQuery($query);
			$db->execute();
			
			//save activity images
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__activity_assets'));
			$query->where(array(
				$db->quoteName('activity_id') .'='. $this->id,
			));
			$db->setQuery($query);
			$db->execute();
			if ($this->countActivityImages()) {
				$columns = array('activity_id', 'src', 'alt', 'caption');
				$query->insert($db->quoteName('#__activity_assets'))->columns($db->quoteName($columns));
				foreach ($this->data['activityimages'] as $image) {
					if ($image['src']) {
						$query->values("{$this->id},'{$image['src']}','{$image['alt']}','{$image['caption']}'");
					}
				}
				$db->setQuery($query);
				$db->execute();
			}
			
			//save activity talents
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__activity_talent'));
			$query->where(array(
				$db->quoteName('activity_id') .'='. $this->id,
			));
			$db->setQuery($query);
			$db->execute();
			if ($this->countActivityTalents()) {
				$columns = array('activity_id', 'talent_id');
				$query->insert($db->quoteName('#__activity_talent'))->columns($db->quoteName($columns));
				foreach ($this->data['activitytalents'] as $talent) {
					if ((int)$talent > 0)
						$query->values("{$this->id},{$talent}");
				}
				$db->setQuery($query);
				$db->execute();
			}
		}
		return $result;
	}
	
	protected function countActivityImages()
	{
		$count = 0;
		$images = $this->data['activityimages'];
		if ($images && count($images)) {
			foreach ($images as $image) {
				if ($image['src']) 
					$count++;
			}
		}
		return $count;
	}
	
	protected function countActivityTalents()
	{
		$count = 0;
		$talents = $this->data['activitytalents'];
		if ($talents && count($talents)) {
			foreach ($talents as $talent) {
				$count++;
			}
		}
		return $count;
	}
	
}