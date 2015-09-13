<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivityTalents extends JFormField {
	protected $type = 'ActivityTalents';
	protected function getJson() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT b.*, c.name as title, c.email' );
		$query->from ( '#__activity_talent AS a' );
		$query->leftJoin ( '#__talent AS b ON a.talent_id=b.id' );
		$query->leftJoin ( '#__users AS c ON b.user_id=c.id' );
		$query->where ( 'a.activity_id = ' . $jinput->get ( 'id', 0 ) );
// 		$query->where ( 'c.block = 0' );
// 		$query->where ( 'c.activation = ""' );
		$db->setQuery ( $query );
		$list = $db->loadObjectList ();
		return count ( $list ) ? json_encode ( $list ) : '[]';
	}
	protected function getInput() {
		$html = array ();
		$id = strtolower ( "{$this->element ['name']}" );
		$html [] = "<div class='{$this->type}' id='{$id}'>";
		$html [] = '<script type="text/javascript">';
		$html [] = "var {$id}={$this->getJson()};";
		$html [] = '</script>';
		$html [] = '</div>';
		return implode ( $html, '' );
	}
}