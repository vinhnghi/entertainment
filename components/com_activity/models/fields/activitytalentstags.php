<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivityTalentsTags extends JFormField {
	protected $type = 'ActivityTalentsTags';
	protected function getTalents() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		$fields = array (
				'a.*',
				'c.id AS cid',
				'c.title AS type',
				'c.alias AS type_alias',
				'd.email',
				'd.id AS user_id',
				'd.name AS title',
				'd.username AS alias' 
		);
		
		$query->select ( 'DISTINCT ' . implode ( ",", $fields ) )->from ( '#__talent AS a' );
		$query->leftJoin ( '#__talent_type_talent AS b ON a.id=b.talent_id' );
		$query->leftJoin ( '#__talent_type AS c ON c.id=b.talent_type_id' );
		$query->leftJoin ( '#__users AS d ON d.id=a.user_id' );
		$query->leftJoin ( '#__activity_talent AS e ON a.id = b.talent_id' );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		$query->where ( 'd.block = 0' );
		$query->where ( 'd.activation = ""' );
		$query->where ( 'e.activity_id = ' . ( int ) $jinput->get ( 'id', 0 ) );
		
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
		$talents = $this->getTalents ();
		if (count ( $talents )) {
			$id = strtolower ( "{$this->element ['name']}" );
			$html [] = "<div class='com_activity_tags'>Talents:";
			$html [] = "<div class='{$this->type}' id='{$id}'>";
			foreach ( $talents as $talent ) {
				$html [] = $this->getTalentTag ( $talent );
			}
			$html [] = '</div>';
			$html [] = '</div>';
		}
		return implode ( $html, '' );
	}
}