<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Set some global property
$document = JFactory::getDocument ();

// Access check: is this user allowed to access the backend of this component?
if (! JFactory::getUser ()->authorise ( 'core.manage', 'com_talent' )) {
	return JError::raiseWarning ( 404, JText::_ ( 'JERROR_ALERTNOAUTHOR' ) );
}

JLoader::register ( 'TalentHelper', JPATH_ADMINISTRATOR . '/components/com_talent/helpers/talent.php' );

// Get an instance of the controller prefixed by Talent
$controller = JControllerLegacy::getInstance ( 'Talent' );

// Perform the Request task
$input = JFactory::getApplication ()->input;
$controller->execute ( $input->getCmd ( 'task' ) );

// Redirect if set by the controller
$controller->redirect ();