<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableAgent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent', 'id', $db );
	}
}