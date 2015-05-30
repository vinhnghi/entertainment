<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityViewTypes extends JViewLegacy 
{
	
	function display($tpl = null) 
	{
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->params = JFactory::getApplication()->getParams();
		$this->heading = $this->params->get('page_title', '') ? $this->params->get('page_title', '') : JText::_ ( 'Activities' );		
		// Display the template
		parent::display ( $tpl );
		
		// Set the document
		$this->setDocument ();
	}
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument ();
		
		$title = $this->heading;
		$document->setTitle ( $title );
		$document->addStyleSheet( JURI::root () . $this->get ( 'Css' ) );
	}
}
