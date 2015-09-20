<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
JLoader::register ( 'SiteActivityHelper', JPATH_SITE . '/components/com_activity/helpers/activity.php' );
//
class JFormFieldActivityTalentsTags extends JFormField {
	protected $type = 'ActivityTalentsTags';
	protected $talent_list_url = 'index.php?option=com_talent&view=talent';
	//
	protected function getTalents() {
		return SiteActivityHelper::getActivityTalents ( JFactory::getApplication ()->input->get ( 'id', 0 ), true );
	}
	//
	protected function getTalentTag($talent) {
		$html = array ();
		$url = JRoute::_ ( "{$this->talent_detail_url}&id={$talent->id}" );
		// $url = "{$this->talent_detail_url}&id={$talent->id}";
		$html [] = "<span class='ActivityTalentsTagsItem'>";
		$html [] = "<a href='{$url}' target='_blank'>";
		$html [] = $talent->title;
		$html [] = '</a>';
		$html [] = '</span>';
		$html [] = $this->element ['separator'];
		return implode ( $html, '' );
	}
	//
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