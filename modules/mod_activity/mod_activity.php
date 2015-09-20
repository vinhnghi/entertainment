<?php
defined ( '_JEXEC' ) or die ();

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$itemId = $params->get ( 'itemId', 0 );
$parent_id = $params->get ( 'parent_id', 0 );
$limit = $params->get ( 'limit', 5 );

$list = ModActivityHelper::getList ( $itemId, $parent_id, $limit );

if (count ( $list )) {
	JHtml::stylesheet ( JURI::root () . 'modules/mod_activity/src/css/activity.css', array (), true );
	require JModuleHelper::getLayoutPath ( 'mod_activity', $params->get ( 'layout', 'default' ) );
}
