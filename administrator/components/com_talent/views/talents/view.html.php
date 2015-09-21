<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class TalentViewTalents extends JViewLegacy {
	//
	protected $canDo;
	//
	function display($tpl = null) {
		if (TalentHelper::isSite ()) {
			$this->type = $this->get ( 'TalentType' );
			$this->items = $this->get ( 'Items' );
			$this->pagination = $this->get ( 'Pagination' );
		} else {
			// Get application
			$app = JFactory::getApplication ();
			$context = "talent.list.admin.talents";
			// Get data from the model
			$this->items = $this->get ( 'Items' );
			$this->pagination = $this->get ( 'Pagination' );
			$this->state = $this->get ( 'State' );
			$this->filter_order = $app->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'title', 'cmd' );
			$this->filter_order_Dir = $app->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd' );
			$this->filterForm = $this->get ( 'FilterForm' );
			$this->activeFilters = $this->get ( 'ActiveFilters' );
			$title = JText::_ ( 'COM_TALENT_MANAGER_TALENTS' );
			if ($this->pagination->total) {
				$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
			}
			JToolBarHelper::title ( $title, 'talent' );
			$this->canDo = TalentHelper::getActions ();
			if (count ( $errors = $this->get ( 'Errors' ) )) {
				JError::raiseError ( 500, implode ( '<br />', $errors ) );
				return false;
			}
			TalentHelper::addSubmenu ( 'talents' );
			$this->addToolBar ();
		}
		// Set the document
		$this->setDocument ();
		// Display the template
		parent::display ( $tpl );
	}
	protected function addToolBar() {
		if ($this->canDo->get ( 'core.create' )) {
			JToolBarHelper::addNew ( 'talent.add', 'JTOOLBAR_NEW' );
		}
		if ($this->canDo->get ( 'core.edit' )) {
			JToolBarHelper::editList ( 'talent.edit', 'JTOOLBAR_EDIT' );
		}
		if ($this->canDo->get ( 'core.delete' )) {
			JToolBarHelper::deleteList ( 'Do you really want to delete?', 'talents.delete', 'JTOOLBAR_DELETE' );
		}
		if ($this->canDo->get ( 'core.admin' )) {
			JToolBarHelper::divider ();
			JToolBarHelper::preferences ( 'com_talent' );
		}
	}
	protected function setDocument() {
		JHtml::stylesheet ( JURI::root () . $this->get ( 'Css' ) );
		$document = JFactory::getDocument ();
		if (TalentHelper::isSite ()) {
			$this->params = $params = JFactory::getApplication ( 'site' )->getParams ( 'com_talent' );
			$this->heading = $this->type ? $this->type->title : $params->get ( 'page_title', '' );
			$title = $params->get ( 'page_title', 'COM_TALENT_TALENTS_TITLE' );
			$pathway = JFactory::getApplication ()->getPathWay ();
			if ($this->type) {
				if (! $title) {
					$title = "{$this->type->title}";
				} else {
					$title = "{$title} - {$this->type->title}";
				}
				$pathway->addItem ( $this->type->title, '' );
			} else {
				$pathway->addItem ( $title, '' );
			}
			$document->setTitle ( $title );
			if ($params->get ( 'menu-meta_description' )) {
				$document->setDescription ( $params->get ( 'menu-meta_description' ) );
			}
			if ($params->get ( 'menu-meta_keywords' )) {
				$document->setMetadata ( 'keywords', $params->get ( 'menu-meta_keywords' ) );
			}
			if ($params->get ( 'robots' )) {
				$document->setMetadata ( 'robots', $params->get ( 'robots' ) );
			}
			return;
		}
		$document->setTitle ( JText::_ ( 'COM_TALENT_ADMINISTRATION' ) );
	}
}