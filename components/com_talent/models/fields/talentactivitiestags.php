<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
JLoader::register ( 'SiteTalentHelper', JPATH_SITE . '/components/com_talent/helpers/talent.php' );
//
class JFormFieldTalentActivitiesTags extends JFormField {
	protected $type = 'TalentActivitiesTags';
	protected $activity_detail_url = 'index.php?option=com_activity&view=activity';
	protected $itemId = '';
	//
	protected function getActivities() {
		return SiteTalentHelper::getTalentActivities ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
	}
	//
	protected function getActivityTag($item) {
		$html = array ();
		$url = JRoute::_ ( "{$this->activity_detail_url}&id={$item->id}" );
		$html [] = "<span class='TalentActivitiesTagsItem'>";
		$html [] = "<a href='{$url}' target='_blank'>";
		$html [] = $item->title;
		$html [] = '</a>';
		$html [] = '</span>';
		$html [] = $this->element ['separator'];
		return implode ( $html, '' );
	}
	//
	protected function getInput() {
		$params = JComponentHelper::getParams ( 'com_talent' );
		$this->itemId = $params->get ( 'itemId', '' );
		$this->activity_detail_url = $params->get ( 'activity_detail_url', '' ) . '&itemId=' . $this->itemId;
		
		$html = array ();
		$items = $this->getActivities ();
		if (count ( $items )) {
			$id = strtolower ( "{$this->element ['name']}" );
			$html [] = "<div class='com_talent_tags'>Activities:";
			$html [] = "<div class='{$this->type}' id='{$id}'>";
			foreach ( $items as $item ) {
				$html [] = $this->getActivityTag ( $item );
			}
			$html [] = '</div>';
			$html [] = '</div>';
		}
		return implode ( $html, '' );
	}
}