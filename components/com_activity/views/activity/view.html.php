<?php

defined ( '_JEXEC' ) or die ( 'Restricted access' );

// require_once JPATH_ADMINISTRATOR . '/components/com_activity/views/activity/view.html.php';

class ActivityViewActivity extends JViewLegacy 
{
	protected $form;
	
	function display($tpl = null) 
	{
		$this->activityType = $this->get ( 'ActivityType' );
		$this->params = JFactory::getApplication()->getParams();
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->heading = $this->item->title;
		// Display the template
		parent::display ( $tpl );
		
		// Set the document
		$this->setDocument ();
	}
	
	protected function setDocument() 
	{
		$document = JFactory::getDocument ();
		$title = $this->params->get('page_title', '');
		if (!$title) {
			$title = "{$this->activityType->title} - {$this->item->title}";
		}
		else {
			$title = "{$title} - {$this->activityType->title} - {$this->item->title}";
		}
		$document->setTitle ( $title );

		$app = JFactory::getApplication('site');
		$params = $app->getParams('com_activity');
		$galleryType = $params->get('gallery_type', 'pgwSlideshow');
		$folder = strtolower($galleryType);
		
		$height = $params->get('gallery_height', 600, 'uint');
		$duration = $params->get('gallery_duration', 3000, 'uint');
		
		$document->addStyleSheet( JURI::root () . "components/com_activity/models/forms/activity.css" );
		$document->addStyleSheet( JURI::root () . "media/com_activity/{$folder}/{$folder}.min.css" );
		
		$document->addScript( JURI::root () . "media/com_activity/{$folder}/{$folder}.min.js", null, true );
		
		$content = 'jQuery(document).ready(function() {var ' . $galleryType . ' = jQuery(".' . $galleryType . '").' . $galleryType . '({maxHeight : ' . $height . ',transitionEffect : \'fading\',adaptiveDuration : ' . $duration . '});/*' . $galleryType . '.startSlide();*/});';
		$document->addScriptDeclaration( $content );
	}
}