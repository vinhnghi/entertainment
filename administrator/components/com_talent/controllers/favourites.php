<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentControllerFavourites extends JControllerAdmin {
	protected $default_view = 'favourites';
	public function getModel($name = 'Favourites', $prefix = 'TalentModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
	public function add() {
		// Check for request forgeries
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		
		$user = JFactory::getUser ();
		$ids = $this->input->get ( 'cid', array (), 'array' );
		
		// Access checks.
		foreach ( $ids as $i => $id ) {
			if (! $user->authorise ( 'core.edit.state', 'com_talent.favourite.' . ( int ) $id )) {
				// Prune items that you can't change.
				unset ( $ids [$i] );
				JError::raiseNotice ( 403, JText::_ ( 'JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED' ) );
			}
		}
		
		if (empty ( $ids )) {
			JError::raiseWarning ( 500, JText::_ ( 'JERROR_NO_ITEMS_SELECTED' ) );
		} else {
			// Get the model.
			$model = $this->getModel ();
			
			// Publish the items.
			if (! $model->addTalentsToFavourite ( $ids )) {
				JError::raiseWarning ( 500, $model->getError () );
			}
			
			$message = JText::_ ( 'COM_TALENT_ADD_TALENT_TO_FAVOURITE_SUCCESSFULLY' );
		}
		
		$this->setRedirect ( JRoute::_ ( 'index.php?option=com_talent&view=favourites&layout=talentlist&id=' . $this->input->get ( 'id' ), false ), $message );
	}
	public function remove() {
		// Check for request forgeries
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		
		$user = JFactory::getUser ();
		$ids = $this->input->get ( 'cid', array (), 'array' );
		
		// Access checks.
		foreach ( $ids as $i => $id ) {
			if (! $user->authorise ( 'core.edit.state', 'com_talent.favourite.' . ( int ) $id )) {
				// Prune items that you can't change.
				unset ( $ids [$i] );
				JError::raiseNotice ( 403, JText::_ ( 'JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED' ) );
			}
		}
		
		if (empty ( $ids )) {
			JError::raiseWarning ( 500, JText::_ ( 'JERROR_NO_ITEMS_SELECTED' ) );
		} else {
			// Get the model.
			$model = $this->getModel ();
			
			// Publish the items.
			if (! $model->removeTalentsFromFavourite ( $ids )) {
				JError::raiseWarning ( 500, $model->getError () );
			}
			
			$message = JText::_ ( 'COM_TALENT_REMOVE_TALENT_FROM_FAVOURITE_SUCCESSFULLY' );
		}
		
		$this->setRedirect ( JRoute::_ ( 'index.php?option=com_talent&view=favourites&layout=talentlist&id=' . $this->input->get ( 'id' ), false ), $message );
	}
}