<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentViewFavourites extends JViewLegacy {
	protected $canDo;
	function display($tpl = null) {
		// Get application
		$app = JFactory::getApplication ();
		$context = "talent.list.admin.favourites";
		// Get data from the model
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->state = $this->get ( 'State' );
		$this->filter_order = $app->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'title', 'cmd' );
		$this->filter_order_Dir = $app->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd' );
		$this->filterForm = $this->get ( 'FilterForm' );
		$this->activeFilters = $this->get ( 'ActiveFilters' );
		
		// What Access Permissions does this user have? What can (s)he do?
		$this->canDo = TalentHelper::getActions ();
		
		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( '<br />', $errors ) );
			
			return false;
		}
		// Display the template
		parent::display ( $tpl );
		// Set the document
		$this->setDocument ();
	}
	protected function setDocument() {
		$this->params = JFactory::getApplication ()->getParams ();
		$this->heading = $this->params->get ( 'page_title', JText::_ ( 'COM_TALENT_FAVOURITE_LIST_TITLE' ) );
		
		$document = JFactory::getDocument ();
		
		$title = $this->heading;
		$document->setTitle ( $title );
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
	}
}