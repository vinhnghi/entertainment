<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

// require_once JPATH_ADMINISTRATOR . '/components/com_activity/views/activity/view.html.php';

class ActivityViewActivity extends JViewLegacy 
{
	protected $form;
	
	function display($tpl = null) 
	{
		$this->activityType = $this->get ( 'ActivityType' );
		$this->params = JFactory::getApplication()->getParams();
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->heading = $this->item->title;
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
			$title = "{$this->activityType->title} - {$this->item->title}";
		}
		else {
			$title = "{$title} - {$this->activityType->title} - {$this->item->title}";
		}
		$document->setTitle ( $title );
		$document->addStyleSheet( JURI::root () . $this->get ( 'Css' ) );
		
	}
}