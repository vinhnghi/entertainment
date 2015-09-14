<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldFavouriteTalents extends JFormField {
	protected $type = 'FavouriteTalents';
	protected function getJson() {
		$query = TalentHelper::getListTalentQueryOfFavourite ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
		$query->order ( 'title ASC' );
		$db = JFactory::getDbo ();
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