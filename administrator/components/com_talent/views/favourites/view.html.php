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
		$jinput = JFactory::getApplication ()->input;
		$this->id = $jinput->get ( 'id', 0 );
		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( '<br />', $errors ) );
			
			return false;
		}
		// Set the submenu
		TalentHelper::addSubmenu ( 'favourites' );
		// Set the toolbar and number of found items
		$this->addToolBar ();
		// Display the template
		parent::display ( $tpl );
		// Set the document
		$this->setDocument ();
	}
	protected function addToolBar() {
		$title = JText::_ ( 'COM_TALENT_MANAGER_FAVOURITES' );
		if ($this->pagination->total) {
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}
		JToolBarHelper::title ( $title, 'favourite' );
		$jinput = JFactory::getApplication ()->input;
		$id = $jinput->get ( 'id', 0 );
		if ($this->canDo->get ( 'core.admin' )) {
			JToolBarHelper::divider ();
			JToolBarHelper::preferences ( 'com_talent' );
		}
	}
	protected function setDocument() {
		$document = JFactory::getDocument ();
		$document->setTitle ( JText::_ ( 'COM_TALENT_ADMINISTRATION' ) );
	}
}