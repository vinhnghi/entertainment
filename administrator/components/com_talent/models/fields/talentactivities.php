<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class JFormFieldTalentActivities extends JFormField {
	//
	protected $type = 'TalentActivities';
	//
	protected function getJson() {
		$items = TalentHelper::getTalentActivities ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
		return count ( $items ) ? json_encode ( $items ) : '[]';
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