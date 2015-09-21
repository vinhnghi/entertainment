<?php
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'SiteActivityHelper', JPATH_SITE . '/components/com_activity/helpers/activity.php' );
class ActivityRouter extends JComponentRouterBase {
	protected $_separator = "/";
	//
	public function build(&$query) {
		$segments = array ();
		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams ( 'com_activity' );
		// We need a menu item. Either the one specified in the query, or the current active one if none specified
		if (empty ( $query ['Itemid'] )) {
			$menuItem = $this->menu->getActive ();
			$menuItemGiven = false;
		} else {
			$menuItem = $this->menu->getItem ( $query ['Itemid'] );
			$menuItemGiven = true;
		}
		// Check again
		if ($menuItemGiven && isset ( $menuItem ) && $menuItem->component != 'com_activity') {
			$menuItemGiven = false;
			unset ( $query ['Itemid'] );
		}
		if (isset ( $query ['view'] ))
			$view = $query ['view'];
		else
			return $segments;
			// Are we dealing with an activity that is attached to a menu item?
		if (($menuItem instanceof stdClass) && //
$menuItem->query ['view'] == $query ['view'] && //
$menuItem->query ['view'] == 'talents' && //
isset ( $query ['cid'] ) && //
isset ( $menuItem->query ['cid'] ) && //
$menuItem->query ['cid'] == ( int ) $query ['cid']) {
			unset ( $query ['view'] );
			unset ( $query ['layout'] );
			unset ( $query ['cid'] );
			return $segments;
		}
		
		// Are we dealing with an talent that is attached to a menu item?
		if (($menuItem instanceof stdClass) && //
$menuItem->query ['view'] == $query ['view'] && //
$menuItem->query ['view'] == 'talent' && //
isset ( $query ['id'] ) && //
isset ( $menuItem->query ['id'] ) && //
$menuItem->query ['id'] == ( int ) $query ['id']) {
			unset ( $query ['view'] );
			unset ( $query ['layout'] );
			unset ( $query ['id'] );
			return $segments;
		}
		unset ( $query ['view'] );
		if ($view == 'activity') {
			if (isset ( $query ['cid'] )) {
				$cid = $query ['cid'];
				unset ( $query ['cid'] );
				if (! $cid && $menuItemGiven && isset ( $menuItem->query ['id'] ))
					$cid = $menuItem->query ['id'];
				if ($cid && $type = SiteActivityHelper::getActivityType ( $cid )) {
					$segments [] = "{$cid}t{$this->_separator}{$type->alias}";
				} else {
					$segments [] = "0t{$this->_separator}0t";
				}
			}
			if (isset ( $query ['id'] ) && $query ['id']) {
				$alias = SiteActivityHelper::getActivity ( $query ['id'] )->alias;
				$segments [] = "{$query ['id']}a{$this->_separator}{$alias}";
			} else {
				return $segments;
			}
			unset ( $query ['id'] );
		} elseif ($view == 'activities') {
			$cid = 0;
			if (isset ( $query ['cid'] ) && $query ['cid']) {
				$cid = $query ['cid'];
			} else {
				if ($menuItemGiven && isset ( $menuItem->query ['cid'] ))
					$cid = $menuItem->query ['cid'];
			}
			unset ( $query ['cid'] );
			if ($cid && $type = SiteActivityHelper::getActivityType ( $cid )) {
				$segments [] = "{$cid}t{$this->_separator}{$type->alias}";
			} else {
				return $segments;
			}
		} else {
			$segments [] = $view;
			$segments [] = $query ['layout'];
		}
		
		if (($menuItemGiven && isset ( $menuItem->query ['layout'] ) && isset ( $query ['layout'] )) || (isset ( $query ['layout'] ) && $query ['layout'] == 'default')) {
			unset ( $query ['layout'] );
		}
		
		return $segments;
	}
	public function parse(&$segments) {
		$total = count ( $segments );
		$vars = array ();
		// Get the active menu item.
		$item = $this->menu->getActive ();
		$params = JComponentHelper::getParams ( 'com_activity' );
		$db = JFactory::getDbo ();
		// Count route segments
		$count = count ( $segments );
		if (! isset ( $item )) {
			// view activities
			if ($count == 2) {
				$vars ['view'] = 'activities';
				$vars ['cid'] = ( int ) $segments [0];
			} else { // view activity
				$vars ['view'] = 'activity';
				$vars ['cid'] = ( int ) $segments [0];
				$vars ['id'] = ( int ) $segments [2];
			}
		} else {
			if ($count == 2) { // view is activities
				if ($segments [0] [count ( $segments ) - 1] == 't') {
					$vars ['view'] = 'activities';
					$vars ['cid'] = ( int ) $segments [0];
				} else {
					$vars ['view'] = 'activity';
					$vars ['id'] = ( int ) $segments [0];
				}
			} else {
				$vars ['view'] = 'activity';
				$vars ['cid'] = ( int ) $segments [0];
				$vars ['id'] = ( int ) $segments [2];
			}
		}
		return $vars;
	}
}
function activityBuildRoute(&$query) {
	$router = new ActivityRouter ();
	
	return $router->build ( $query );
}
function activityParseRoute($segments) {
	$router = new ActivityRouter ();
	
	return $router->parse ( $segments );
}
