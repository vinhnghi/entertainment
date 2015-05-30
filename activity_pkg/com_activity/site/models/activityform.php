<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR . '/components/com_activity/models/activity.php';

class ActivityModelActivityForm extends ActivityModelActivity
{
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}
