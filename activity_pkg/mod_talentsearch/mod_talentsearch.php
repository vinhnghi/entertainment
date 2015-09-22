<?php
defined ( '_JEXEC' ) or die ();

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$moduleclass_sfx = htmlspecialchars ( $params->get ( 'moduleclass_sfx' ) );

$itemId = ( int ) $params->get ( 'itemId', 0 );

$displayData = ModTalentSearchHelper::getDisplayData ( $params );

JHtml::stylesheet ( JURI::root () . 'modules/mod_talentsearch/src/css/talentsearch.css', array (), true );
require JModuleHelper::getLayoutPath ( 'mod_talentsearch', $params->get ( 'layout', 'default' ) );
