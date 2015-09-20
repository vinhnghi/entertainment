<?php
defined ( '_JEXEC' ) or die ();
//
JLoader::register ( 'SiteActivityHelper', JPATH_SITE . '/components/com_activity/helpers/activity.php' );
//
class ModActivityHelper {
	public static function getList($itemId, $parent_id, $limit) {
		$query = SiteActivityHelper::getListActivitiesOfTypeQuery ( $parent_id );
		$query->where ( 'a.published = 1' );
		if ($parent_id)
			$query->where ( 'c.published = 1' );
		$query->order ( 'a.created DESC' );
		$db = JFactory::getDbo ();
		$db->setQuery ( $query, 0, $limit );
		$items = $db->loadObjectList ();
		
		$key = "$items.$itemId.$parent_id.$limit";
		$cache = JFactory::getCache ( 'mod_activity', '' );
		$cache->store ( $items, $key );
		
		return $items;
	}
}

