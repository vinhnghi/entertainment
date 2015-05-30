<?php

defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$list		= ModActivityHelper::getList($params);
$base		= ModActivityHelper::getBase($params);
$active		= ModActivityHelper::getActive($params);
$active_id 	= $active->id;
$path		= $base->tree;

$showAll	= $params->get('showAllChildren');
$class_sfx	= htmlspecialchars($params->get('class_sfx'));

if (count($list))
{
	require JModuleHelper::getLayoutPath('mod_activity', $params->get('layout', 'default'));
}
