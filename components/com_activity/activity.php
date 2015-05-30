<?php
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'ActivityRouter', JPATH_COMPONENT_SITE . '/com_activity/router.php' );
JLoader::register ( 'ActivityHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/activity.php' );
JLoader::register ( 'JToolBarHelper', JPATH_ADMINISTRATOR . '/includes/toolbar.php' );
JLoader::register ( 'JSubMenuHelper', JPATH_ADMINISTRATOR . '/includes/subtoolbar.php' );

$controller = JControllerLegacy::getInstance ( 'Activity' );
$controller->execute ( JFactory::getApplication ()->input->get ( 'task' ) );
$controller->redirect ();
