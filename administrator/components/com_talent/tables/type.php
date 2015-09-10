<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableType extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__talent_type', 'id', $db );
	}
}