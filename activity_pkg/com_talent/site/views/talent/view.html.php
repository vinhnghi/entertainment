<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentViewTalent extends JViewLegacy {
	protected $form;
	function display($tpl = null) {
		$this->type = $this->get ( 'Type' );
		$this->params = JFactory::getApplication ()->getParams ();
		$this->form = $this->get ( 'Form' );
		$this->item = $this->get ( 'Item' );
		$this->heading = $this->item->title;
		// Display the template
		parent::display ( $tpl );
		// Set the document
		$this->setDocument ();
	}
	protected function setDocument() {
		$document = JFactory::getDocument ();
		$title = $this->params->get ( 'page_title', '' );
		if (! $title) {
			$title = "{$this->type->title} - {$this->item->title}";
		} else {
			$title = "{$title} - {$this->type->title} - {$this->item->title}";
		}
		$document->setTitle ( $title );
		
		$app = JFactory::getApplication ( 'site' );
		$params = $app->getParams ( 'com_talent' );
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
	}
}