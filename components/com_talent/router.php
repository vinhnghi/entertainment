<?php
defined ( '_JEXEC' ) or die ();
JLoader::register ( 'TalentHelper', JPATH_ADMINISTRATOR . '/components/com_talent/helpers/talent.php' );
class TalentRouter extends JComponentRouterBase {
	public function __construct($app = null, $menu = null) {
		parent::__construct ( $app, $menu );
	}
	protected $_separator = "/";
	public function build(&$query) {
		$segments = array ();
		
		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams ( 'com_talent' );
		
		// We need a menu item. Either the one specified in the query, or the current active one if none specified
		if (empty ( $query ['Itemid'] )) {
			$menuItem = $this->menu->getActive ();
			$menuItemGiven = false;
		} else {
			$menuItem = $this->menu->getItem ( $query ['Itemid'] );
			$menuItemGiven = true;
		}
		
		// Check again
		if ($menuItemGiven && isset ( $menuItem ) && $menuItem->component != 'com_talent') {
			$menuItemGiven = false;
			unset ( $query ['Itemid'] );
		}
		
		if (isset ( $query ['view'] ))
			$view = $query ['view'];
		else
			return $segments;
			
			// Are we dealing with an talent that is attached to a menu item?
		if (($menuItem instanceof stdClass) && $menuItem->query ['view'] == $query ['view'] && $menuItem->query ['view'] == 'talents' && isset ( $query ['cid'] ) && $menuItem->query ['cid'] == ( int ) $query ['cid']) {
			unset ( $query ['view'] );
			unset ( $query ['layout'] );
			unset ( $query ['cid'] );
			return $segments;
		}
		
		// Are we dealing with an talent that is attached to a menu item?
		if (($menuItem instanceof stdClass) && $menuItem->query ['view'] == $query ['view'] && $menuItem->query ['view'] == 'talent' && isset ( $query ['id'] ) && $menuItem->query ['id'] == ( int ) $query ['id']) {
			unset ( $query ['view'] );
			unset ( $query ['layout'] );
			unset ( $query ['id'] );
			return $segments;
		}
		unset ( $query ['view'] );
		
		if ($view == 'talent') {
			if (isset ( $query ['cid'] )) {
				$cid = $query ['cid'];
				unset ( $query ['cid'] );
				if (! $cid && $menuItemGiven && isset ( $menuItem->query ['id'] ))
					$cid = $menuItem->query ['id'];
				elseif (! $cid)
					$cid = 0;
				$typeAlias = TalentHelper::getTalentType ( $cid )->alias;
				if ($typeAlias)
					$segments [] = "{$cid}t{$this->_separator}{$typeAlias}";
			}
			
			if (isset ( $query ['id'] ) && $query ['id']) {
				$alias = TalentHelper::getTalent ( $query ['id'] )->alias;
				$segments [] = "{$query ['id']}a{$this->_separator}{$alias}";
			} else {
				return $segments;
			}
			unset ( $query ['id'] );
		} elseif ($view == 'talents') {
			$cid = 0;
			if (isset ( $query ['cid'] ) && $query ['cid']) {
				$cid = $query ['cid'];
			} else {
				if ($menuItemGiven && isset ( $menuItem->query ['cid'] ))
					$cid = $menuItem->query ['cid'];
			}
			unset ( $query ['cid'] );
			$typeAlias = TalentHelper::getTalentType ( $cid )->alias;
			
			if ($typeAlias)
				$segments [] = "{$cid}t{$this->_separator}{$typeAlias}";
			else
				return $segments;
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
		$params = JComponentHelper::getParams ( 'com_talent' );
		$db = JFactory::getDbo ();
		// Count route segments
		$count = count ( $segments );
		if (! isset ( $item )) {
			// view talents
			if ($count == 2) {
				$vars ['view'] = 'talents';
				$vars ['cid'] = ( int ) $segments [0];
			}  // view talent
else {
				$vars ['view'] = 'talent';
				$vars ['cid'] = ( int ) $segments [0];
				$vars ['id'] = ( int ) $segments [2];
			}
		} else {
			$app = JFactory::getApplication ();
			$pathway = $app->getPathway ();
			if ($count == 2) { // view is talents
				if ($segments [0] [count ( $segments ) - 1] == 't') {
					$vars ['view'] = 'talents';
					$vars ['cid'] = ( int ) $segments [0];
					$pathway->addItem ( TalentHelper::getTalentType ( $vars ['cid'] )->title, JRoute::_ ( "index.php?option=com_talent&view=talents&cid={$vars['cid']}" ) );
				} else {
					$vars ['view'] = 'talent';
					$vars ['id'] = ( int ) $segments [0];
					$pathway->addItem ( TalentHelper::getTalent ( $vars ['id'] )->title, JRoute::_ ( "index.php?option=com_talent&view=talent&id={$vars['id']}" ) );
				}
			} else {
				$vars ['view'] = 'talent';
				$vars ['cid'] = ( int ) $segments [0];
				$vars ['id'] = ( int ) $segments [2];
				$pathway->addItem ( TalentHelper::getTalentType ( $vars ['cid'] )->title, JRoute::_ ( "index.php?option=com_talent&view=talents&cid={$vars['cid']}" ) );
				$pathway->addItem ( TalentHelper::getTalent ( $vars ['id'] )->title, JRoute::_ ( "index.php?option=com_talent&view=talent&cid={$vars['cid']}&id={$vars['id']}" ) );
			}
		}
		return $vars;
	}
}
function talentBuildRoute(&$query) {
	$router = new TalentRouter ();
	
	return $router->build ( $query );
}
function talentParseRoute($segments) {
	$router = new TalentRouter ();
	
	return $router->parse ( $segments );
}
