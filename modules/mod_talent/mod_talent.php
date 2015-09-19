<?php
defined ( '_JEXEC' ) or die ();

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$itemId = ( int ) $params->get ( 'itemId', 0 );
$displaytype = ( int ) $params->get ( 'displaytype', 0 );
$limit = ( int ) $params->get ( 'limit', 5 );

$list = ModTalentHelper::getList ( $itemId, $displaytype, $limit );

if (count ( $list )) {
	JHtml::stylesheet ( JURI::root () . 'modules/mod_talent/src/css/talent.css', array (), true );
	require JModuleHelper::getLayoutPath ( 'mod_talent', $params->get ( 'layout', 'default' ) );
}
