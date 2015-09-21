<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class ActivityViewActivity extends JViewLegacy {
	//
	protected $form;
	protected $item;
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
		$this->canDo = ActivityHelper::getActions ( $this->item->id );
		$this->state = $this->get ( 'State' );
		$this->heading = $this->item->title;
		if (ActivityHelper::isSite ( 'default' )) {
			$this->params = JFactory::getApplication ( 'site' )->getParams ();
			$this->activityType = $this->get ( 'ActivityType' );
		}
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
		
		$isNew = ($this->item->id == 0);
		
		JToolBarHelper::title ( $isNew ? JText::_ ( 'COM_ACTIVITY_MANAGER_ACTIVITY_NEW' ) : JText::_ ( 'COM_ACTIVITY_MANAGER_ACTIVITY_EDIT' ), 'activity' );
		// Build the actions for new and existing records.
		if ($isNew) {
			// For new records, check the create permission.
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::apply ( 'activity.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'activity.save', 'JTOOLBAR_SAVE' );
				JToolBarHelper::custom ( 'activity.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			JToolBarHelper::cancel ( 'activity.cancel', 'JTOOLBAR_CANCEL' );
		} else {
			if ($this->canDo->get ( 'core.edit' )) {
				// We can save the new record
				JToolBarHelper::apply ( 'activity.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'activity.save', 'JTOOLBAR_SAVE' );
				
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get ( 'core.create' )) {
					JToolBarHelper::custom ( 'activity.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
				}
			}
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::custom ( 'activity.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );
			}
			JToolBarHelper::cancel ( 'activity.cancel', 'JTOOLBAR_CLOSE' );
		}
	}
	protected function setDocument() {
		// return true;
		$document = JFactory::getDocument ();
		if (ActivityHelper::isSite ( 'default' )) {
			$app = JFactory::getApplication ();
			$title = $this->params->get ( 'page_title', JText::_ ( 'COM_TALENT_ACTIVITY_TITLE' ) );
			$pathway = JFactory::getApplication ()->getPathWay ();
			if ($this->activityType) {
				if (! $title) {
					$title = "{$this->activityType->title}";
				} else {
					$title = "{$title} - {$this->activityType->title}";
				}
				$pathway->addItem ( $this->activityType->title, 'index.php?option=com_activity&view=activities&cid=' . $this->activityType->id . '&Itemid=' . JFactory::getApplication ()->getMenu ()->getActive ()->id );
			}
			$title = "{$title} - {$this->item->title}";
			$document->setTitle ( $title );
			$pathway->addItem ( $this->item->title, '' );
			if ($this->params->get ( 'menu-meta_description' )) {
				$document->setDescription ( $this->params->get ( 'menu-meta_description' ) );
			}
			if ($this->params->get ( 'menu-meta_keywords' )) {
				$document->setMetadata ( 'keywords', $this->params->get ( 'menu-meta_keywords' ) );
			}
			if ($this->params->get ( 'robots' )) {
				$document->setMetadata ( 'robots', $this->params->get ( 'robots' ) );
			}
			
			$params = $app->getParams ( 'com_activity' );
			$galleryType = 'pgwSlideshow'; // $params->get('gallery_type', 'pgwSlideshow');
			$folder = strtolower ( $galleryType );
			$height = $params->get ( 'gallery_height', 360, 'uint' );
			$duration = $params->get ( 'gallery_duration', 3000, 'uint' );
			$document->addStyleSheet ( JURI::root () . "components/com_activity/models/forms/activity.css" );
			$document->addStyleSheet ( JURI::root () . "media/{$folder}/{$folder}.min.css" );
			$document->addStyleDeclaration ( ".pgwSlideshow .ps-current ul li img {height: {$height}px !important;}" );
			$document->addScript ( JURI::root () . "media/{$folder}/{$folder}.js", null, true );
			$content = 'jQuery(document).ready(function() {var ' . $galleryType . ' = jQuery(".' . $galleryType . '").' . $galleryType . '({height : ' . $height . ',transitionEffect : \'fading\',adaptiveDuration : ' . $duration . '});/*' . $galleryType . '.startSlide();*/});';
			$document->addScriptDeclaration ( $content );
			
			return;
		}
		
		$isNew = ($this->item->id == 0);
		$document->setTitle ( $isNew ? JText::_ ( 'COM_ACTIVITY_ACTIVITY_CREATING' ) : JText::_ ( 'COM_ACTIVITY_ACTIVITY_EDITING' ) );
		
		$params = JComponentHelper::getParams ( 'com_activity' );
		$talent_list_url = $params->get ( 'talent_list_url', '' );
		$document->addScriptDeclaration ( "window.talentListURL = '{$talent_list_url}'" );
		
		$document->addScript ( JURI::root () . $this->get ( 'Script' ) );
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
	}
}