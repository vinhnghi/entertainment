<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class ActivityViewType extends JViewLegacy {
	//
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;
	//
	public function display($tpl = null) {
		// check user permission
		if (! ActivityHelper::canSubmit ()) {
			JError::raiseError ( 500, 'COM_ACTIVITY_NO_PERMISSION' );
			return false;
		}
		// Get the Data
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->canDo = ActivityHelper::getActions ( $this->item ? $this->item->id : null );
		$this->state = $this->get ( 'State' );
		$this->params = JFactory::getApplication ()->getParams ();
		
		// Check for errors.
		if (count ( $errors = $this->get ( 'Errors' ) )) {
			JError::raiseError ( 500, implode ( '<br />', $errors ) );
			return false;
		}
		// Set the toolbar
		$this->addToolBar ();
		// Display the template
		parent::display ( $tpl );
		// Set the document
		$this->setDocument ();
	}
	//
	protected function addToolBar() {
		$input = JFactory::getApplication ()->input;
		// Hide Joomla Administrator Main menu
		$input->set ( 'hidemainmenu', true );
		$isNew = ($this->item ? $this->item->id == 0 : true);
		JToolBarHelper::title ( $isNew ? JText::_ ( 'COM_ACTIVITY_MANAGER_TYPE_NEW' ) : JText::_ ( 'COM_ACTIVITY_MANAGER_TYPE_EDIT' ), 'type' );
		// Build the actions for new and existing records.
		if ($isNew) {
			// For new records, check the create permission.
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::apply ( 'type.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'type.save', 'JTOOLBAR_SAVE' );
				JToolBarHelper::custom ( 'type.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			JToolBarHelper::cancel ( 'type.cancel', 'JTOOLBAR_CANCEL' );
		} else {
			if ($this->canDo->get ( 'core.edit' )) {
				// We can save the new record
				JToolBarHelper::apply ( 'type.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'type.save', 'JTOOLBAR_SAVE' );
				
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get ( 'core.create' )) {
					JToolBarHelper::custom ( 'type.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
				}
			}
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::custom ( 'type.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );
			}
			JToolBarHelper::cancel ( 'type.cancel', 'JTOOLBAR_CLOSE' );
		}
	}
	protected function setDocument() {
		$isNew = ($this->item->id == 0);
		$document = JFactory::getDocument ();
		$document->setTitle ( $isNew ? JText::_ ( 'COM_ACTIVITY_TYPE_CREATING' ) : JText::_ ( 'COM_ACTIVITY_TYPE_EDITING' ) );
		$document->addScript ( JURI::root () . $this->get ( 'Script' ) );
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
		JText::script ( 'COM_ACTIVITY_TYPE_ERROR_UNACCEPTABLE' );
	}
}