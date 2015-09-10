<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableTalent extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__talent', 'id', $db );
	}
}