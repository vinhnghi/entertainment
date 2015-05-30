<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class ActivityModelActivity extends JModelItem
{

	protected $_context = 'com_activity.activity';

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('activity.id', $pk);

		$offset = $app->input->getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		if ((! ActivityHelper::getActions ()->get ( 'core.edit.state' )) && (! ActivityHelper::getActions ()->get ( 'core.edit' )))
		{
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}

	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('activity.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
					->select(
						$this->getState(
							'item.select', 'a.id, a.asset_id, a.title, a.alias, a.introtext, a.fulltext, ' .
							// If badcats is not null, this means that the activity is inside an unpublished category
							// In this case, the state is set to 0 to indicate Unpublished (even if the activity state is Published)
							'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
							'a.catid, a.created, a.created_by, a.created_by_alias, ' .
							// Use created if modified is 0
							'CASE WHEN a.modified = ' . $db->quote($db->getNullDate()) . ' THEN a.created ELSE a.modified END as modified, ' .
							'a.modified_by, ' .
							'a.images, a.urls, a.attribs, a.ordering, ' .
							'a.metakey, a.metadesc, a.metadata, a.language'
						)
					);
				$query->from('#__activity AS a');

				// Join on user table.
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = a.created_by');

				// Filter by language
				if ($this->getState('filter.language'))
				{
					$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				}

				$query->where('a.id = ' . (int) $pk);

				// Filter by published state.
				$published = $this->getState('filter.published');

				if (is_numeric($published))
				{
					$query->where('(a.published = 1)');
				}
				
				trace((string)$query);

				$db->setQuery($query);

				$data = $db->loadObject();

				if (empty($data))
				{
					return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived)))
				{
					return JError::raiseError(404, JText::_('COM_CONTENT_ERROR_ARTICLE_NOT_FOUND'));
				}

				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$registry = new Registry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				// Technically guest could edit an activity, but lets not check that to improve performance a little.
				if (!$user->get('guest'))
				{
					$userId = $user->get('id');
					$asset = 'com_activity.activity.' . $data->id;

					// Check general edit permission first.
					if (ActivityHelper::getActions ( $item->id )->get ( 'core.edit' ))
					{
						$data->params->set('access-edit', true);
					}

					// Now check if edit.own is available.
					elseif (!empty($userId) && ActivityHelper::getActions ( $item->id )->get ( 'core.edit.own' ))
					{
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by)
						{
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access'))
				{
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

}
