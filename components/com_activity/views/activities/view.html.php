<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class ActivityViewActivities extends JViewLegacy {
	//
	function display($tpl = null) {
		$this->activityType = $this->get ( 'ActivityType' );
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->params = JFactory::getApplication ()->getParams ();
		$this->heading = $this->activityType ? $this->activityType->title : $this->params->get ( 'page_title', JText::_ ( 'COM_ACTIVITY_ACTIVITIES_TITLE' ) );
		// Display the template
		parent::display ( $tpl );
		
		// Set the document
		$this->setDocument ();
	}
	//
	protected function setDocument() {
		$document = JFactory::getDocument ();
		$pathway = JFactory::getApplication ()->getPathWay ();
		$title = $this->params->get ( 'page_title', JText::_ ( 'COM_ACTIVITY_ACTIVITIES_TITLE' ) );
		if ($this->activityType) {
			if (! $title) {
				$title = $this->activityType->title;
			} else {
				$title = "{$title} - {$this->activityType->title}";
			}
			$pathway->addItem ( $this->activityType->title, '' );
		} else {
			$pathway->addItem ( $title, '' );
		}
		$document->setTitle ( $title );
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
		
		if ($this->params->get ( 'menu-meta_description' )) {
			$document->setDescription ( $this->params->get ( 'menu-meta_description' ) );
		}
		if ($this->params->get ( 'menu-meta_keywords' )) {
			$document->setMetadata ( 'keywords', $this->params->get ( 'menu-meta_keywords' ) );
		}
		if ($this->params->get ( 'robots' )) {
			$document->setMetadata ( 'robots', $this->params->get ( 'robots' ) );
		}
	}
}