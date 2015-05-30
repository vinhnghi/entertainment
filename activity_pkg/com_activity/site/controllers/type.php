<?php

defined('_JEXEC') or die;

class ActivityControllerType extends JControllerForm
{

	protected $view_item = 'typeform';

	protected $view_list = 'types';

	protected $urlVar = 'a.id';

	public function getModel($name = 'TypeForm', $prefix = 'ActivityModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
	
		return $model;
	}
	
	public function add()
	{
		if (parent::add())
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	protected function allowAdd($data = array())
	{
		if (ActivityHelper::getActions ()->get ( 'core.create' ))
		{
			return true;
		}
		return parent::allowAdd ( $data );
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user     = JFactory::getUser();
		$userId   = $user->get('id');

		// Check general edit permission first.
		if (ActivityHelper::getActions ( $recordId, 'type' )->get ( 'core.edit' ))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if (ActivityHelper::getActions ( $recordId, 'type' )->get ( 'core.edit.own' ))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;

			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function cancel($key = 'a_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit($key = null, $urlVar = 'a_id')
	{
		$result = parent::edit($key, $urlVar);

		if (!$result)
		{
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
	{
		// Need to override the parent method completely.
		$tmpl   = $this->input->get('tmpl');

		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		$itemId	= $this->input->getInt('Itemid');
		$return	= $this->getReturnPage();

		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		if ($return)
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		return;
	}

	public function save($key = null, $urlVar = 'a_id')
	{
		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result)
		{
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}

}
