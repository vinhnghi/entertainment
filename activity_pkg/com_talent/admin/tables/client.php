<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableClient extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__client', 'id', $db );
	}
}