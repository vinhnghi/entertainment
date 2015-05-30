<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

class JHelperActivity
{
	public static function addSubmenu($vName)
	{
	}
	public static function _getActions($categoryId = 0, $id = 0, $assetName = '')
	{
		// Log usage of deprecated function
		JLog::add(__METHOD__ . '() is deprecated, use JHelperActivity::getActions() with new arguments order instead.', JLog::WARNING, 'deprecated');

		// Reverted a change for version 2.5.6
		$user	= JFactory::getUser();
		$result	= new JObject;

		$path = JPATH_ADMINISTRATOR . '/components/' . $assetName . '/access.xml';

		if (empty($id) && empty($categoryId))
		{
			$section = 'component';
		}
		elseif (empty($id))
		{
			$section = 'category';
			$assetName .= '.category.' . (int) $categoryId;
		}
		else
		{
			// Used only in com_content
			$section = 'article';
			$assetName .= '.article.' . (int) $id;
		}

		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='" . $section . "']/");

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getActions($component = '', $section = '', $id = 0)
	{
		// Check for deprecated arguments order
		if (is_int($component) || is_null($component))
		{
			$result = self::_getActions($component, $section, $id);

			return $result;
		}

		$user	= JFactory::getUser();
		$result	= new JObject;

		$path = JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml';

		if ($section && $id)
		{
			$assetName = $component . '.' . $section . '.' . (int) $id;
		}
		else
		{
			$assetName = $component;
		}

		$actions = JAccess::getActionsFromFile($path, "/access/section[@name='component']/");

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getCurrentLanguage($detectBrowser = true)
	{
		$app = JFactory::getApplication();
		$langCode = $app->input->cookie->getString(JApplicationHelper::getHash('language'));

		// No cookie - let's try to detect browser language or use site default
		if (!$langCode)
		{
			if ($detectBrowser)
			{
				$langCode = JLanguageHelper::detectLanguage();
			}
			else
			{
				$langCode = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
			}
		}

		return $langCode;
	}

	public static function getLanguageId($langCode)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('lang_id')
			->from('#__languages')
			->where($db->quoteName('lang_code') . ' = ' . $db->quote($langCode));
		$db->setQuery($query);

		$id = $db->loadResult();

		return $id;
	}

	public function getRowData(JTable $table)
	{
		$data = new JHelper;

		return $data->getRowData($table);
	}
}
