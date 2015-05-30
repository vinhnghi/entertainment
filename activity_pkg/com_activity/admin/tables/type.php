<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityTableType extends JTable
{
	function __construct(&$db) {
		parent::__construct ( '#__activity_type', 'id', $db );
	}
	
}