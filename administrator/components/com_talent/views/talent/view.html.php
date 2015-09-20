<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
class TalentViewTalent extends JViewLegacy {
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;
	public function display($tpl = null) {
		// Get the Data
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->canDo = TalentHelper::getActions ( $this->item->id );
		$this->heading = $this->item->title;
		
		if (! TalentHelper::canSubmit ()) {
			JError::raiseError ( 500, 'COM_TALENT_NO_PERMISSION' );
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
	protected function addToolBar() {
		$input = JFactory::getApplication ()->input;
		
		// Hide Joomla Administrator Main menu
		$input->set ( 'hidemainmenu', true );
		
		$isNew = ($this->item->id == 0);
		
		JToolBarHelper::title ( $isNew ? JText::_ ( 'COM_TALENT_MANAGER_TALENT_NEW' ) : JText::_ ( 'COM_TALENT_MANAGER_TALENT_EDIT' ), 'talent' );
		// Build the actions for new and existing records.
		if ($isNew) {
			// For new records, check the create permission.
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::apply ( 'talent.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'talent.save', 'JTOOLBAR_SAVE' );
				JToolBarHelper::custom ( 'talent.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
			}
			JToolBarHelper::cancel ( 'talent.cancel', 'JTOOLBAR_CANCEL' );
		} else {
			if ($this->canDo->get ( 'core.edit' )) {
				// We can save the new record
				JToolBarHelper::apply ( 'talent.apply', 'JTOOLBAR_APPLY' );
				JToolBarHelper::save ( 'talent.save', 'JTOOLBAR_SAVE' );
				
				// We can save this record, but check the create permission to see
				// if we can return to make a new one.
				if ($this->canDo->get ( 'core.create' )) {
					JToolBarHelper::custom ( 'talent.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false );
				}
			}
			if ($this->canDo->get ( 'core.create' )) {
				JToolBarHelper::custom ( 'talent.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false );
			}
			JToolBarHelper::cancel ( 'talent.cancel', 'JTOOLBAR_CLOSE' );
		}
	}
	protected function setDocument() {
		$document = JFactory::getDocument ();
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
		if (TalentHelper::isSite ()) {
			$this->type = $this->get ( 'TalentType' );
			$app = JFactory::getApplication ( 'site' );
			$params = $app->getParams ( 'com_talent' );
			$title = JFactory::getApplication ()->getParams ()->get ( 'page_title', '' );
			$pathway = JFactory::getApplication ()->getPathWay ();
			if ($this->type) {
				if (! $title) {
					$title = "{$this->type->title}";
				} else {
					$title = "{$title} - {$this->type->title}";
				}
				$pathway->addItem ( $this->type->title, 'index.php?option=com_talent&view=talents&cid=' . $this->type->id . '&Itemid=' . JFactory::getApplication ()->getMenu ()->getActive ()->id );
			}
			$title = "{$title} - {$this->item->title}";
			$document->setTitle ( $title );
			$pathway->addItem ( $this->item->title, '' );
			if ($params->get ( 'menu-meta_description' )) {
				$document->setDescription ( $params->get ( 'menu-meta_description' ) );
			}
			if ($params->get ( 'menu-meta_keywords' )) {
				$document->setMetadata ( 'keywords', $params->get ( 'menu-meta_keywords' ) );
			}
			if ($params->get ( 'robots' )) {
				$document->setMetadata ( 'robots', $params->get ( 'robots' ) );
			}
			$galleryType = 'pgwSlideshow'; // $params->get('gallery_type', 'pgwSlideshow');
			$folder = strtolower ( $galleryType );
			$height = $params->get ( 'gallery_height', 360, 'uint' );
			$duration = $params->get ( 'gallery_duration', 3000, 'uint' );
			$document->addStyleSheet ( JURI::root () . "components/com_talent/src/css/talent.css" );
			$document->addStyleSheet ( JURI::root () . "media/{$folder}/{$folder}.min.css" );
			$document->addStyleDeclaration ( ".pgwSlideshow .ps-current ul li img {height: {$height}px !important;}" );
			$document->addScript ( JURI::root () . "media/{$folder}/{$folder}.js", null, true );
			$content = 'jQuery(document).ready(function() {var ' . $galleryType . ' = jQuery(".' . $galleryType . '").' . $galleryType . '({height : ' . $height . ',transitionEffect : \'fading\',adaptiveDuration : ' . $duration . '});/*' . $galleryType . '.startSlide();*/});';
			$document->addScriptDeclaration ( $content );
			return;
		}
		$isNew = ($this->item->id == 0);
		$document->setTitle ( $isNew ? JText::_ ( 'COM_TALENT_TALENT_CREATING' ) : JText::_ ( 'COM_TALENT_TALENT_EDITING' ) );
		$document->addScript ( JURI::root () . $this->get ( 'Script' ) );
		JText::script ( 'COM_TALENT_TALENT_ERROR_UNACCEPTABLE' );
	}
}