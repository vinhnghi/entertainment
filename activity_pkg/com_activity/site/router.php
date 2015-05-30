<?php

defined('_JEXEC') or die;

class ActivityRouter extends JComponentRouterBase
{

	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams('com_activity');
		$advanced = $params->get('sef_advanced_link', 0);

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
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}

		// Are we dealing with an activity or type that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& isset($query['id'])
			&& $menuItem->query['id'] == (int) $query['id'])
		{
			unset($query['view']);

			if (isset($query['typeid']))
			{
				unset($query['typeid']);
			}

			if (isset($query['layout']))
			{
				unset($query['layout']);
			}

			unset($query['id']);

			return $segments;
		}

		if ($view == 'type' || $view == 'activity')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
			}

			unset($query['view']);

			if ($view == 'activity')
			{
				if (isset($query['id']) && isset($query['typeid']) && $query['typeid'])
				{
					$typeid = $query['typeid'];

					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = JFactory::getDbo();
						$dbQuery = $db->getQuery(true)
							->select('alias')
							->from('#__activity')
							->where('id=' . (int) $query['id']);
						$db->setQuery($dbQuery);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
				}
				else
				{
					// We should have these two set for this view.  If we don't, it is an error
					return $segments;
				}
			}
			else
			{
				if (isset($query['id']))
				{
					$typeid = $query['id'];
				}
				else
				{
					// We should have id set for this view.  If we don't, it is an error
					return $segments;
				}
			}

			if ($menuItemGiven && isset($menuItem->query['id']))
			{
				$mCatid = $menuItem->query['id'];
			}
			else
			{
				$mCatid = 0;
			}

			$types = JTypes::getInstance('Activity');
			$type = $types->get($typeid);

			if (!$type)
			{
				// We couldn't find the type we were given.  Bail.
				return $segments;
			}

			$path = array_reverse($type->getPath());

			$array = array();

			foreach ($path as $id)
			{
				if ((int) $id == (int) $mCatid)
				{
					break;
				}

				list($tmp, $id) = explode(':', $id, 2);

				$array[] = $id;
			}

			$array = array_reverse($array);

			if (!$advanced && count($array))
			{
				$array[0] = (int) $typeid . ':' . $array[0];
			}

			$segments = array_merge($segments, $array);

			if ($view == 'activity')
			{
				if ($advanced)
				{
					list($tmp, $id) = explode(':', $query['id'], 2);
				}
				else
				{
					$id = $query['id'];
				}

				$segments[] = $id;
			}

			unset($query['id']);
			unset($query['typeid']);
		}

		if (isset($query['layout']))
		{
			if ($menuItemGiven && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
			}
		}

		$total = count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	public function parse(&$segments)
	{
		$total = count($segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Get the active menu item.
		$item = $this->menu->getActive();
		$params = JComponentHelper::getParams('com_activity');
		$advanced = $params->get('sef_advanced_link', 0);
		$db = JFactory::getDbo();

		// Count route segments
		$count = count($segments);

		/*
		 * Standard routing for activitys.  If we don't pick up an Itemid then we get the view from the segments
		 * the first segment is the view and the last segment is the id of the activity or type.
		 */
		if (!isset($item))
		{
			$vars['view'] = $segments[0];
			$vars['id'] = $segments[$count - 1];

			return $vars;
		}

		/*
		 * If there is only one segment, then it points to either an activity or a type.
		 * We test it first to see if it is a type.  If the id and alias match a type,
		 * then we assume it is a type.  If they don't we assume it is an activity
		 */
		if ($count == 1)
		{
			// We check to see if an alias is given.  If not, we assume it is an activity
			if (strpos($segments[0], ':') === false)
			{
				$vars['view'] = 'activity';
				$vars['id'] = (int) $segments[0];

				return $vars;
			}

			list($id, $alias) = explode(':', $segments[0], 2);

			// First we check if it is a type
			$type = JTypes::getInstance('Activity')->get($id);

			if ($type && $type->alias == $alias)
			{
				$vars['view'] = 'type';
				$vars['id'] = $id;

				return $vars;
			}
			else
			{
				$query = $db->getQuery(true)
					->select($db->quoteName(array('alias', 'typeid')))
					->from($db->quoteName('#__activity'))
					->where($db->quoteName('id') . ' = ' . (int) $id);
				$db->setQuery($query);
				$activity = $db->loadObject();

				if ($activity)
				{
					if ($activity->alias == $alias)
					{
						$vars['view'] = 'activity';
						$vars['typeid'] = (int) $activity->typeid;
						$vars['id'] = (int) $id;

						return $vars;
					}
				}
			}
		}

		/*
		 * If there was more than one segment, then we can determine where the URL points to
		 * because the first segment will have the target type id prepended to it.  If the
		 * last segment has a number prepended, it is an activity, otherwise, it is a type.
		 */
		if (!$advanced)
		{
			$cat_id = (int) $segments[0];

			$activity_id = (int) $segments[$count - 1];

			if ($activity_id > 0)
			{
				$vars['view'] = 'activity';
				$vars['typeid'] = $cat_id;
				$vars['id'] = $activity_id;
			}
			else
			{
				$vars['view'] = 'type';
				$vars['id'] = $cat_id;
			}

			return $vars;
		}

		// We get the type id from the menu item and search from there
		$id = $item->query['id'];
		$type = JTypes::getInstance('Activity')->get($id);

		if (!$type)
		{
			JError::raiseError(404, JText::_('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));

			return $vars;
		}

		$types = $type->getChildren();
		$vars['typeid'] = $id;
		$vars['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = str_replace(':', '-', $segment);

			foreach ($types as $type)
			{
				if ($type->alias == $segment)
				{
					$vars['id'] = $type->id;
					$vars['typeid'] = $type->id;
					$vars['view'] = 'type';
					$types = $type->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				if ($advanced)
				{
					$db = JFactory::getDbo();
					$query = $db->getQuery(true)
						->select($db->quoteName('id'))
						->from('#__activity')
						->where($db->quoteName('typeid') . ' = ' . (int) $vars['typeid'])
						->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
					$db->setQuery($query);
					$cid = $db->loadResult();
				}
				else
				{
					$cid = $segment;
				}

				$vars['id'] = $cid;

				if ($item->query['view'] == 'archive' && $count != 1)
				{
					$vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
					$vars['month'] = $segments[$count - 1];
					$vars['view'] = 'archive';
				}
				else
				{
					$vars['view'] = 'activity';
				}
			}

			$found = 0;
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
