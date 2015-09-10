<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentTableFavorite extends JTable {
	function __construct(&$db) {
		parent::__construct ( '#__agent_favorite', 'id', $db );
	}
}