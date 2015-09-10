<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

class TalentViewTalents extends JViewLegacy 
{
	
	function display($tpl = null) 
	{
		$this->activityType = $this->get ( 'ActivityType' );
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->params = JFactory::getApplication()->getParams();
		$this->heading = $this->activityType->title;	
		// Display the template
		parent::display ( $tpl );
		
		// Set the document
		$this->setDocument ();
	}
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument ();
		
		$title = $this->params->get('page_title', '');
		if (!$title) {
			$title = $this->activityType->title;
		}
		else {
			$title = "{$title} - {$this->activityType->title}";
		}
		$document->setTitle ( $title  );
		$document->addStyleSheet( JURI::root () . $this->get ( 'Css' ) );
	}
}