<?php

defined('_JEXEC') or die;

class ActivityController extends JControllerLegacy
{
	public function __construct($config = array())
	{
		$this->input = JFactory::getApplication()->input;

		// Article frontpage Editor pagebreak proxying:
		if ($this->input->get('view') === 'activity' && $this->input->get('layout') === 'pagebreak')
		{
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		// Article frontpage Editor activity proxying:
		elseif ($this->input->get('view') === 'activities' && $this->input->get('layout') === 'modal')
		{
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;
		$id    = $this->input->getInt('a_id');
		$vName = $this->input->getCmd('view', 'types');
		$this->input->set('view', $vName);

		$user = JFactory::getUser();

		if ($user->get('id')
			|| ($this->input->getMethod() == 'POST'
			&& $vName == 'type'))
		{
			$cachable = false;
		}

		$safeurlparams = array(
			'typeid' => 'INT',
			'id' => 'INT',
			'cid' => 'ARRAY',
			'year' => 'INT',
			'month' => 'INT',
			'limit' => 'UINT',
			'limitstart' => 'UINT',
			'showall' => 'INT',
			'return' => 'BASE64',
			'filter' => 'STRING',
			'filter_order' => 'CMD',
			'filter_order_Dir' => 'CMD',
			'filter-search' => 'STRING',
			'print' => 'BOOLEAN',
			'lang' => 'CMD',
			'Itemid' => 'INT');

		// Check for edit form.
		if ($vName == 'activityform' && !$this->checkEditId('com_activity.edit.activity', $id))
		{
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}
		elseif ($vName == 'typeform' && !$this->checkEditId('com_activity.edit.type', $id))
		{
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}
