<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivityTalentsTags extends JFormField {
	protected $type = 'ActivityTalentsTags';
	protected function getTalents() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT b.*' );
		$query->from ( '#__activity_talent AS a' );
		$query->leftJoin ( '#__talent AS b ON (a.talent_id=b.id)' );
		$query->where ( '(a.activity_id = ' . $jinput->get ( 'id', 0 ) . ')' );
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	}
	protected function getTalentTag($talent) {
		$html = array ();
		$html [] = "<span class='ActivityTalentsTagsItem'>";
		$html [] = "<a href='#;' onclick='alert(\"TODO: intergrate with talents component to open a link to {$talent->title} page\");return false;'>";
		$html [] = $talent->title;
		$html [] = '</a>';
		$html [] = '</span>';
		$html [] = $this->element ['separator'];
		return implode ( $html, '' );
	}
	protected function getInput() {
		$html = array ();
		$id = strtolower ( "{$this->element ['name']}" );
		$html [] = "<div class='{$this->type}' id='{$id}'>";
		foreach ( $this->getTalents () as $talent ) {
			$html [] = $this->getTalentTag ( $talent );
		}
		$html [] = '</div>';
		return implode ( $html, '' );
	}
}