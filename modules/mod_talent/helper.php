<?php
defined ( '_JEXEC' ) or die ();
//
JLoader::register ( 'SiteTalentHelper', JPATH_SITE . '/components/com_talent/helpers/talent.php' );
//
class ModTalentHelper {
	public static function getList($itemId, $displaytype, $limit) {
		$query = SiteTalentHelper::getTalentQuery ();
		$query->where ( 'a.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		$query->order ( 'a.modified DESC' );
		$db = JFactory::getDbo ();
		$db->setQuery ( $query, 0, $limit );
		$items = $db->loadObjectList ();
		
		if ($displaytype) {
			foreach ( $items as $item ) {
				$item->count = SiteTalentHelper::countTalentInFavourite ( $item->id );
			}
			usort ( $items, function ($a, $b) {
				return $a->count > $b->count;
			} );
		}
		
		$key = "$items.$itemId.$displaytype.$limit";
		$cache = JFactory::getCache ( 'mod_talent', '' );
		$cache->store ( $items, $key );
		
		return $items;
	}
}

