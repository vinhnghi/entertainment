<?php
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'TalentHelper', JPATH_ADMINISTRATOR . '/components/com_talent/helpers/talent.php' );
JLoader::register ( 'TalentRouter', JPATH_COMPONENT_SITE . '/com_talent/router.php' );

$controller = JControllerLegacy::getInstance ( 'Talent' );
$controller->execute ( JFactory::getApplication ()->input->get ( 'task' ) );
$controller->redirect ();
