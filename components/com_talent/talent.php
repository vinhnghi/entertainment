<?php
defined ( '_JEXEC' ) or die ();

JLoader::register ( 'SiteTalentHelper', JPATH_SITE . '/components/com_talent/helpers/talent.php' );

$controller = JControllerLegacy::getInstance ( 'Talent' );
$controller->execute ( JFactory::getApplication ()->input->get ( 'task' ) );
$controller->redirect ();
