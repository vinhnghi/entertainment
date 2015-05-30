<?php

defined('_JEXEC') or die;

class ActivityRouter extends JComponentRouterBase
{

	protected $_separator = "/";
	
	protected function getAlias($tbl, $id)
	{
		return $this->getRecord($tbl, $id)->alias;
	}

	protected function getRecord($tbl, $id)
	{
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$query->select ( '*' )->from ( $tbl );
		$query->where ( 'id=' . (int)$id );
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams('com_activity');
		
		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $this->menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $this->menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}

		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_activity')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}

		if (isset($query['view']))
			$view = $query['view'];
		else
			return $segments;

		// Are we dealing with an activity that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& $menuItem->query['view'] == 'activities'
			&& isset($query['cid'])
			&& $menuItem->query['cid'] == (int) $query['cid'])
		{
			unset($query['view']);
			unset($query['layout']);
			unset($query['cid']);
			return $segments;
		}

		// Are we dealing with an activity that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& $menuItem->query['view'] == 'activity'
			&& isset($query['id'])
			&& $menuItem->query['id'] == (int) $query['id'])
		{
			unset($query['view']);
			unset($query['layout']);
			unset($query['id']);
			return $segments;
		}
		unset($query['view']);

		if ($view == 'activity')
		{
			if (!$menuItemGiven)
				$segments[] = $view;
			if (isset($query['cid'])) {
				$cid = $query['cid'];
				unset($query['cid']);
				if (!$cid && $menuItemGiven && isset($menuItem->query['id']))
					$cid = $menuItem->query['id'];
				elseif (!$cid)
					$cid = 0;
				$typeAlias = $this->getAlias('#__activity_type', $cid);
				if ( $typeAlias )
					$segments[] = "{$cid}t{$this->_separator}{$typeAlias}";
			}
			
			if (isset($query['id']) && $query['id'])
			{
				$segments[] = "{$query ['id']}a{$this->_separator}{$this->getAlias ( '#__activity', $query['id'] )}";
			}
			else
			{
				return $segments;
			}
			unset($query['id']);
			
		}
		elseif ($view == 'activities')
		{
			$cid = 0;
			if (isset($query['cid']) && $query['cid'])
			{
				$cid = $query['cid'];
			}
			else
			{
				if ($menuItemGiven && isset($menuItem->query['cid']))
					$cid = $menuItem->query['cid'];
			}
			unset($query['cid']);
			$typeAlias = $this->getAlias('#__activity_type', $cid);
			
			if ( $typeAlias )
				$segments[] = "{$cid}t{$this->_separator}{$typeAlias}";
			else
				return $segments;
		}
		else 
		{
			$segments[] = $view;
			$segments[] = $query['layout'];
		}
		
		if (($menuItemGiven && isset($menuItem->query['layout']) && isset($query['layout'])) || (isset($query['layout']) && $query['layout'] == 'default'))
		{
			unset($query['layout']);
		}

		return $segments;
	}

	public function parse(&$segments)
	{
		$total = count($segments);
		$vars = array();
		// Get the active menu item.
		$item = $this->menu->getActive();
		$params = JComponentHelper::getParams('com_activity');
		$db = JFactory::getDbo();
		// Count route segments
		$count = count($segments);
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['cid'] = $segments[$count - 1];
			return $vars;
		}

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		if ($count == 2) { // view is activities
			if ($segments [0][count($segments) - 1] == 't') {
				$vars ['view'] = 'activities';
				$vars ['cid'] = ( int ) $segments [0];
				$pathway->addItem ( $this->getRecord ( '#__activity_type', $vars ['cid'] )->title, JRoute::_ ( "index.php?option=com_activity&view=activities&cid={$vars['cid']}" ) );
			}
			else  {
				$vars ['view'] = 'activity';
				$vars ['id'] = ( int ) $segments [0];
				$pathway->addItem ( $this->getRecord ( '#__activity', $vars ['id'] )->title, JRoute::_ ( "index.php?option=com_activity&view=activity&id={$vars['id']}" ) );
			}
		} else {
			$vars ['view'] = 'activity';
			$vars ['cid'] = ( int ) $segments [0];
			$vars ['id'] = ( int ) $segments [2];
			$pathway->addItem ( $this->getRecord ( '#__activity_type', $vars ['cid'] )->title, JRoute::_ ( "index.php?option=com_activity&view=activities&cid={$vars['cid']}" ) );
			$pathway->addItem ( $this->getRecord ( '#__activity', $vars ['id'] )->title, JRoute::_ ( "index.php?option=com_activity&view=activity&cid={$vars['cid']}&id={$vars['id']}" ) );
			return $vars;
		}
		return $vars;
	}
}

function activityBuildRoute(&$query)
{
	$router = new ActivityRouter;

	return $router->build($query);
}

function activityParseRoute($segments)
{
	$router = new ActivityRouter;

	return $router->parse($segments);
}
