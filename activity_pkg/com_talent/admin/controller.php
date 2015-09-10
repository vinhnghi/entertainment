<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ();
class ActivityController extends JControllerLegacy {
	protected $default_view = 'activities';
	public function display($cachable = false, $urlparams = array()) {
		$view = $this->input->get ( 'view', 'activities' );
		$layout = $this->input->get ( 'layout', 'activities' );
		$id = $this->input->getInt ( 'id' );
		
		// Check for edit form.
		if ($view == 'activity' && $layout == 'edit' && ! $this->checkEditId ( 'com_activity.edit.activity', $id )) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_UNHELD_ID', $id ) );
			$this->setMessage ( $this->getError (), 'error' );
			$this->setRedirect ( JRoute::_ ( 'index.php?option=com_activity&view=activities', false ) );
			
			return false;
		}
		
		return parent::display ();
	}
}
