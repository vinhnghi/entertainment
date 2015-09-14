<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableFavourite extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent_favourite', 'id', $db );
	}
}