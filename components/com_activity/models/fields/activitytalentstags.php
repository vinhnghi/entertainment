<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivityTalentsTags extends JFormField {
	protected $type = 'ActivityTalentsTags';
	protected $talent_list_url = 'index.php?option=com_talent&view=talent';
	protected function getTalents() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'DISTINCT b.*, c.name as title, c.email' );
		$query->from ( '#__activity_talent AS a' );
		$query->leftJoin ( '#__talent AS b ON a.talent_id=b.id' );
		$query->leftJoin ( '#__users AS c ON b.user_id=c.id' );
		$query->where ( 'a.activity_id = ' . $jinput->get ( 'id', 0 ) );
		$query->where ( 'b.published = 1' );
		$query->where ( 'c.block = 0' );
		$query->where ( 'c.activation = ""' );
		
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	}
	protected function getTalentTag($talent) {
		$html = array ();
// 		$url = JRoute::_ ( "{$this->talent_detail_url}&id={$talent->id}" );
		$url = "{$this->talent_detail_url}&id={$talent->id}";
		$html [] = "<span class='ActivityTalentsTagsItem'>";
		$html [] = "<a href='{$url}' target='_blank'>";
		$html [] = $talent->title;
		$html [] = '</a>';
		$html [] = '</span>';
		$html [] = $this->element ['separator'];
		return implode ( $html, '' );
	}
	protected function getInput() {
		$params = JComponentHelper::getParams ( 'com_activity' );
		$this->talent_detail_url = $params->get ( 'talent_detail_url', '' );
		
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