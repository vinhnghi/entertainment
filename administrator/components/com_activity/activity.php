<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Set some global property
$document = JFactory::getDocument ();

// Access check: is this user allowed to access the backend of this component?
if (! JFactory::getUser ()->authorise ( 'core.manage', 'com_activity' )) {
	return JError::raiseWarning ( 404, JText::_ ( 'JERROR_ALERTNOAUTHOR' ) );
}

// Require helper file
JLoader::register ( 'ActivityHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/activity.php' );
JLoader::register ( 'JToolBarHelper', JPATH_ADMINISTRATOR . '/includes/toolbar.php' );
JLoader::register ( 'JSubMenuHelper', JPATH_ADMINISTRATOR . '/includes/subtoolbar.php' );

// Get an instance of the controller prefixed by Activity
$controller = JControllerLegacy::getInstance ( 'Activity' );

// Perform the Request task
$input = JFactory::getApplication ()->input;
$controller->execute ( $input->getCmd ( 'task' ) );

// Redirect if set by the controller
$controller->redirect ();