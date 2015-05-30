<?php

defined('_JEXEC') or die;

class ActivityViewActivity extends JViewLegacy
{
	protected $item;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$dispatcher	= JEventDispatcher::getInstance();

		$this->item		= $this->get('Item');
		$this->print	= $app->input->getBool('print');
		$this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = $this->item;
		$item->tagLayout = new JLayoutFile('joomla.content.tags');

		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$item->catslug		= $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$item->parent_slug	= $item->parent_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		// No link for ROOT category
		if ($item->parent_alias == 'root')
		{
			$item->parent_slug = null;
		}

		// TODO: Change based on shownoauth
		$item->readmore_link = JRoute::_(ActivityHelperRoute::getActivityRoute($item->slug, $item->catid, $item->language));

		// Merge activity params. If this is single-activity view, menu params override activity params
		// Otherwise, activity params override menu item params
		$this->params = $this->state->get('params');
		$active = $app->getMenu()->getActive();
		$temp = clone $this->params;

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an activity view for this activity, then the menu item params take priority
			if (strpos($currentLink, 'view=activity') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{
				// Load layout from active query (in case it is an alternative menu item)
				if (isset($active->query['layout']))
				{
					$this->setLayout($active->query['layout']);
				}
				// Check for alternative layout of activity
				elseif ($layout = $item->params->get('activity_layout'))
				{
					$this->setLayout($layout);
				}

				// $item->params are the activity params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);
			}
			else
			{
				// Current view is not a single activity, so the activity params take priority here
				// Merge the menu item params with the activity params so that the activity params take priority
				$temp->merge($item->params);
				$item->params = $temp;

				// Check for alternative layouts (since we are not in a single-activity menu item)
				// Single-activity menu item layout takes priority over alt layout for an activity
				if ($layout = $item->params->get('activity_layout'))
				{
					$this->setLayout($layout);
				}
			}
		}
		else
		{
			// Merge so that activity params take priority
			$temp->merge($item->params);
			$item->params = $temp;

			// Check for alternative layouts (since we are not in a single-activity menu item)
			// Single-activity menu item layout takes priority over alt layout for an activity
			if ($layout = $item->params->get('activity_layout'))
			{
				$this->setLayout($layout);
			}
		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the activity (the model has already computed the values).
		if ($item->params->get('access-view') == false && ($item->params->get('show_noauth', '0') == '0'))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return;
		}

		if ($item->params->get('show_intro', '1') == '1')
		{
			$item->text = $item->introtext . ' ' . $item->fulltext;
		}
		elseif ($item->fulltext)
		{
			$item->text = $item->fulltext;
		}
		else
		{
			$item->text = $item->introtext;
		}

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_content.activity', $this->item->id);

		// Process the content plugins.

		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onActivityPrepare', array ('com_content.activity', &$item, &$this->params, $offset));

		$item->event = new stdClass;
		$results = $dispatcher->trigger('onActivityAfterTitle', array('com_content.activity', &$item, &$this->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onActivityBeforeDisplay', array('com_content.activity', &$item, &$this->params, $offset));
		$item->event->beforeDisplayActivity = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onActivityAfterDisplay', array('com_content.activity', &$item, &$this->params, $offset));
		$item->event->afterDisplayActivity = trim(implode("\n", $results));

		// Increment the hit counter of the activity.
		if (!$this->params->get('intro_only') && $offset == 0)
		{
			$model = $this->getModel();
			$model->hit();
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void.
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this activity
		if ($menu && ($menu->query['option'] != 'com_content' || $menu->query['view'] != 'activity' || $id != $this->item->id))
		{
			// If this is not a single activity menu item, set the page title to the activity title
			if ($this->item->title)
			{
				$title = $this->item->title;
			}

			$path = array(array('title' => $this->item->title, 'link' => ''));
			$category = JCategories::getInstance('Activity')->get($this->item->catid);

			while ($category && ($menu->query['option'] != 'com_content' || $menu->query['view'] == 'activity' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ActivityHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		if (empty($title))
		{
			$title = $this->item->title;
		}

		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;
			$this->document->setMetaData('author', $author);
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->title = $this->item->title . ' - ' . $this->item->page_title;
			$this->document->setTitle(
				$this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1)
			);
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}
