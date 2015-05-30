<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class ActivityModelActivities extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'published', 'a.published',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'images', 'a.images',
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// List state information
		$value = $app->input->get('limit', $app->get('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

		$orderCol = $app->input->get('filter_order', 'a.ordering');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->input->get('filter_order_Dir', 'ASC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'ASC';
		}

		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user = JFactory::getUser();

		if ((! ActivityHelper::getActions ()->get ( 'core.edit.state' )) && (! ActivityHelper::getActions ()->get ( 'core.edit' )))
		{
			// Filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());

		$this->setState('layout', $app->input->getString('layout'));
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . serialize($this->getState('filter.published'));
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . serialize($this->getState('filter.activity_id'));
		$id .= ':' . $this->getState('filter.date_filtering');
		$id .= ':' . $this->getState('filter.date_field');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$user	= JFactory::getUser();

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.introtext, a.fulltext, ' .
					'a.catid, a.created, a.created_by, a.created_by_alias, ' .
					// Use created if modified is 0
					'CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, uam.name as modified_by_name,' .
					// Use created if publish_up is 0
					'a.images, a.metadata, a.metakey, a.metadesc, ' .
					'a.language, ' . $query->length('a.fulltext') . ' AS readmore'
			)
		);

		$query->from('#__activity AS a');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author")
			->select("ua.email AS author_email")
			->join('LEFT', '#__users AS ua ON ua.id = a.created_by')
			->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		// Filter by a single or group of activities.
		$activityId = $this->getState('filter.activity_id');

		if (is_numeric($activityId))
		{
			$type = $this->getState('filter.activity_id.include', true) ? '= ' : '<> ';
			$query->where('a.id ' . $type . (int) $activityId);
		}
		elseif (is_array($activityId))
		{
			JArrayHelper::toInteger($activityId);
			$activityId = implode(',', $activityId);
			$type = $this->getState('filter.activity_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('a.id ' . $type . ' (' . $activityId . ')');
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId))
		{
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$authorWhere = 'a.created_by ' . $type . (int) $authorId;
		}
		elseif (is_array($authorId))
		{
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId)
			{
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$authorWhere = 'a.created_by ' . $type . ' (' . $authorId . ')';
			}
		}

		// Filter by author alias
		$authorAlias = $this->getState('filter.author_alias');
		$authorAliasWhere = '';

		if (is_string($authorAlias))
		{
			$type = $this->getState('filter.author_alias.include', true) ? '= ' : '<> ';
			$authorAliasWhere = 'a.created_by_alias ' . $type . $db->quote($authorAlias);
		}
		elseif (is_array($authorAlias))
		{
			$first = current($authorAlias);

			if (!empty($first))
			{
				JArrayHelper::toString($authorAlias);

				foreach ($authorAlias as $key => $alias)
				{
					$authorAlias[$key] = $db->quote($alias);
				}

				$authorAlias = implode(',', $authorAlias);

				if ($authorAlias)
				{
					$type = $this->getState('filter.author_alias.include', true) ? 'IN' : 'NOT IN';
					$authorAliasWhere = 'a.created_by_alias ' . $type . ' (' . $authorAlias .
						')';
				}
			}
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere))
		{
			$query->where('(' . $authorWhere . ' OR ' . $authorAliasWhere . ')');
		}
		elseif (empty($authorWhere) && empty($authorAliasWhere))
		{
			// If both are empty we don't want to add to the query
		}
		else
		{
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere . $authorAliasWhere);
		}

		// Filter by language
		if ($this->getState('filter.language'))
		{
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

		return $query;
	}

	public function getItems()
	{
		$items = parent::getItems();
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$guest = $user->get('guest');
		$groups = $user->getAuthorisedViewLevels();
		$input = JFactory::getApplication()->input;

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_activity', true);

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			// Unpack readmore and layout params
			$item->alternative_readmore = $activityParams->get('alternative_readmore');
			$item->layout = $activityParams->get('layout');

			$item->params = clone $this->getState('params');

			/*For blogs, activity params override menu item params only if menu param = 'use_activity'
			Otherwise, menu item params control the layout
			If menu item is 'use_activity' and there is no activity param, use global*/
			if (($input->getString('layout') == 'blog') || ($input->getString('view') == 'featured')
				|| ($this->getState('params')->get('layout_type') == 'blog'))
			{
				// Create an array of just the params set to 'use_activity'
				$menuParamsArray = $this->getState('params')->toArray();
				$activityArray = array();

				foreach ($menuParamsArray as $key => $value)
				{
					if ($value === 'use_activity')
					{
						// If the activity has a value, use it
						if ($activityParams->get($key) != '')
						{
							// Get the value from the activity
							$activityArray[$key] = $activityParams->get($key);
						}
						else
						{
							// Otherwise, use the global value
							$activityArray[$key] = $globalParams->get($key);
						}
					}
				}

				// Merge the selected activity params
				if (count($activityArray) > 0)
				{
					$activityParams = new Registry;
					$activityParams->loadArray($activityArray);
					$item->params->merge($activityParams);
				}
			}
			else
			{
				// For non-blog layouts, merge all of the activity params
				$item->params->merge($activityParams);
			}

			// Get display date
			switch ($item->params->get('list_show_date'))
			{
				case 'modified':
					$item->displayDate = $item->modified;
					break;

				case 'published':
				case 'created':
				default:
					$item->displayDate = $item->created;
					break;
			}

			// Compute the asset access permissions.
			// Technically guest could edit an activity, but lets not check that to improve performance a little.
			if (!$guest)
			{
				$asset = 'com_activity.activity.' . $item->id;

				// Check general edit permission first.
				if (ActivityHelper::getActions ( $item->id )->get ( 'core.edit' ))
				{
					$item->params->set('access-edit', true);
				}

				// Now check if edit.own is available.
				elseif(! empty ( $userId ) && ActivityHelper::getActions ( $item->id )->get ( 'core.edit.own' ))
				{
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by)
					{
						$item->params->set('access-edit', true);
					}
				}
			}
		}

		return $items;
	}

	public function getStart()
	{
		return $this->getState('list.start');
	}
}
