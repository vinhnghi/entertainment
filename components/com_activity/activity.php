<?php
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'ActivityHelper', JPATH_ADMINISTRATOR . '/components/com_activity/helpers/activity.php' );
JLoader::register ( 'SiteActivityHelper', JPATH_SITE . '/components/com_activity/helpers/activity.php' );

$controller = JControllerLegacy::getInstance ( 'Activity' );
$controller->execute ( JFactory::getApplication ()->input->get ( 'task' ) );
$controller->redirect ();
