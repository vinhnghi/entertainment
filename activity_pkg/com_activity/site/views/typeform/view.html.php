<?php

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_activity/views/type/view.html.php';

class ActivityViewTypeForm extends ActivityViewType
{
	protected $return_page;
	protected $user;
	protected $state;

	public function display($tpl = null)
	{
		// Get model data.
		$this->state		= $this->get('State');
		$this->form			= $this->get('TypeForm');
		$this->return_page	= $this->get('ReturnPage');
		parent::display($tpl);
		$this->_prepareDocument();
	}

	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		$this->user   = JFactory::getUser();
		// Create a shortcut to the parameters.
		$params	= &$this->state->params;
		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
		$this->params = $params;
		// Override global params with activity specific params
		$this->params->merge($this->item->params);
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('Edit an activity type'));
		}

		$title = $this->params->def('page_title', JText::_('Edit an activity type'));

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
	