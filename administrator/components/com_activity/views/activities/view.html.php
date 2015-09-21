<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityViewActivities extends JViewLegacy {
	protected $canDo;
	function display($tpl = null) {
		if (ActivityHelper::isSite ()) {
			$this->activityType = $this->get ( 'ActivityType' );
			$this->items = $this->get ( 'Items' );
			$this->pagination = $this->get ( 'Pagination' );
			$this->params = JFactory::getApplication ()->getParams ();
			$this->heading = $this->activityType ? $this->activityType->title : $this->params->get ( 'page_title', JText::_ ( 'COM_ACTIVITY_ACTIVITIES_TITLE' ) );
		} else {
			$app = JFactory::getApplication ();
			$context = "activity.list.admin.activities";
			$this->items = $this->get ( 'Items' );
			$this->pagination = $this->get ( 'Pagination' );
			$this->state = $this->get ( 'State' );
			$this->filter_order = $app->getUserStateFromRequest ( $context . 'filter_order', 'filter_order', 'title', 'cmd' );
			$this->filter_order_Dir = $app->getUserStateFromRequest ( $context . 'filter_order_Dir', 'filter_order_Dir', 'asc', 'cmd' );
			$this->filterForm = $this->get ( 'FilterForm' );
			$this->activeFilters = $this->get ( 'ActiveFilters' );
			$title = JText::_ ( 'COM_ACTIVITY_MANAGER_ACTIVITIES' );
			if ($this->pagination->total) {
				$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
			}
			JToolBarHelper::title ( $title, 'activity' );
			$this->canDo = ActivityHelper::getActions ();
			if (count ( $errors = $this->get ( 'Errors' ) )) {
				JError::raiseError ( 500, implode ( '<br />', $errors ) );
				return false;
			}
			ActivityHelper::addSubmenu ( 'activities' );
			$this->addToolBar ();
		}
		// Set the document
		$this->setDocument ();
		// Display the template
		parent::display ( $tpl );
	}
	protected function addToolBar() {
		if ($this->canDo->get ( 'core.create' )) {
			JToolBarHelper::addNew ( 'activity.add', 'JTOOLBAR_NEW' );
		}
		if ($this->canDo->get ( 'core.edit' )) {
			JToolBarHelper::editList ( 'activity.edit', 'JTOOLBAR_EDIT' );
		}
		if ($this->canDo->get ( 'core.delete' )) {
			JToolBarHelper::deleteList ( 'Do you really want to delete?', 'activities.delete', 'JTOOLBAR_DELETE' );
		}
		if ($this->canDo->get ( 'core.admin' )) {
			JToolBarHelper::divider ();
			JToolBarHelper::preferences ( 'com_activity' );
		}
	}
	protected function setDocument() {
		$document = JFactory::getDocument ();
		if (ActivityHelper::isSite ()) {
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
			return;
		}
		$document->setTitle ( JText::_ ( 'COM_ACTIVITY_ADMINISTRATION' ) );
	}
}