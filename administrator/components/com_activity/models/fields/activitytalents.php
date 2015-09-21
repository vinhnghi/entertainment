<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class JFormFieldActivityTalents extends JFormField {
	//
	protected $type = 'ActivityTalents';
	//
	protected function getJson() {
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT b.*, c.name as title, c.email' );
		$query->from ( '#__activity_talent AS a' );
		$query->innerJoin ( '#__talent AS b ON a.talent_id=b.id AND b.published = 1' );
		$query->innerJoin ( '#__users AS c ON b.user_id=c.id' );
		$query->where ( 'a.activity_id = ' . JFactory::getApplication ()->input->get ( 'id', 0 ) );
		$db->setQuery ( $query );
		$list = $db->loadObjectList ();
		return count ( $list ) ? json_encode ( $list ) : '[]';
	}
	//
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