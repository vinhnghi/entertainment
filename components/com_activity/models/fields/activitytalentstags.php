<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
JLoader::register ( 'SiteActivityHelper', JPATH_SITE . '/components/com_activity/helpers/activity.php' );
//
class JFormFieldActivityTalentsTags extends JFormField {
	protected $type = 'ActivityTalentsTags';
	protected $talent_list_url = 'index.php?option=com_talent&view=talent';
	protected $itemId = '';
	//
	protected function getTalents() {
		return SiteActivityHelper::getActivityTalents ( JFactory::getApplication ()->input->get ( 'id', 0 ), true );
	}
	//
	protected function getTalentTag($item) {
		$html = array ();
		$url = JRoute::_ ( "{$this->talent_detail_url}&id={$item->id}" );
		$html [] = "<span class='ActivityTalentsTagsItem'>";
		$html [] = "<a href='{$url}' target='_blank'>";
		$html [] = $item->title;
		$html [] = '</a>';
		$html [] = '</span>';
		$html [] = $this->element ['separator'];
		return implode ( $html, '' );
	}
	//
	protected function getInput() {
		$params = JComponentHelper::getParams ( 'com_activity' );
		$this->itemId = $params->get ( 'itemId', '' );
		$this->talent_detail_url = $params->get ( 'talent_detail_url', '' ) . '&itemId=' . $this->itemId;
		
		$html = array ();
		$items = $this->getTalents ();
		if (count ( $items )) {
			$id = strtolower ( "{$this->element ['name']}" );
			$html [] = "<div class='com_activity_tags'>Talents:";
			$html [] = "<div class='{$this->type}' id='{$id}'>";
			foreach ( $items as $item ) {
				$html [] = $this->getTalentTag ( $item );
			}
			$html [] = '</div>';
			$html [] = '</div>';
		}
		return implode ( $html, '' );
	}
}