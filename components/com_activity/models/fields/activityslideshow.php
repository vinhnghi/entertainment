<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivitySlideShow extends JFormField {
	protected $type = 'ActivitySlideShow';
	protected function getImages() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'a.*' );
		$query->from ( '#__activity_assets AS a' );
		$query->where ( '(a.activity_id = '.$jinput->get ( 'id', 0 ).')' );
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	}
	protected function getImage($image) {
		$html = array ();
		$html [] = "<li><img src='/{$image->src}' alt='{$image->alt}' data-description='{$image->caption}'></li>";
		return implode ( $html, '' );
	}
	protected function getInput() {
		$html = array ();
		$images = $this->getImages ();
		if(count($images)) {
			$app = JFactory::getApplication('site');
			$params = $app->getParams('com_activity');
			$galleryType = $params->get('gallery_type', 'pgwSlideshow');
			$id = strtolower ( "{$this->element ['name']}" );
			$html [] = "<div class='com_activity_images'>Images:";
			$html [] = "<ul class='{$galleryType} {$this->type}' id='{$id}'>";
			foreach ( $images as $image ) {
				$html [] = $this->getImage ( $image );
			}
			$html [] = '</ul>';
			$html [] = '</div>';
		}
		return implode ( $html, '' );
	}
}