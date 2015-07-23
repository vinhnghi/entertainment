<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivitySlideShow extends JFormField {
	protected $type = 'ActivitySlideShow';
	protected $thumbFolder = 'thumbs';
	protected function getImages() {
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'a.*' );
		$query->from ( '#__activity_assets AS a' );
		$query->where ( '(a.activity_id = ' . $jinput->get ( 'id', 0 ) . ')' );
		$db->setQuery ( $query );
		
		return $db->loadObjectList ();
	}
	protected function generateVideoThumbnail($src) {
		$folder = $_SERVER ['DOCUMENT_ROOT'] . "/" . dirname ( $src );
		$thumbsFolder = $folder . "/$this->thumbFolder";
		$videoName = basename ( $src );
		$videoPath = "$folder/$videoName";
		$thumbName = $videoName . ".jpg";
		$thumbPath = "$thumbsFolder/$thumbName";
		if (! file_exists ( $thumbsFolder )) {
			mkdir ( $thumbsFolder );
		}
		if (! file_exists ( $thumbPath )) {
			$ffmpeg = '/usr/local/bin/ffmpeg';
			$interval = 1;
			$size = '80x80';
			$cmd = "$ffmpeg -i $videoPath -deinterlace -an -ss $interval -f mjpeg -t 1 -s $size -r 1 -y -vcodec mjpeg -f mjpeg $thumbPath 2>&1";
			exec ( $cmd, $output, $retval );
		}
	}
	protected function getImage($image) {
		$html = array ();
		if (file_exists ( $image->src )) {
			$mime = mime_content_type ( $image->src );
			if (strstr ( $mime, "video/" )) {
				$this->generateVideoThumbnail ( $image->src );
				$html [] = "<li><img src='/" . dirname ( $image->src ) . "/$this->thumbFolder/" . basename ( $image->src ) . ".jpg' alt='{$image->alt}' data-description='$image->caption'></li>";
			} else if (strstr ( $mime, "image/" )) {
				$html [] = "<li><img src='/{$image->src}' alt='{$image->alt}' data-description='{$image->caption}'></li>";
			}
		}
		return implode ( $html, '' );
	}
	protected function getInput() {
		$html = array ();
		$images = $this->getImages ();
		if (count ( $images )) {
			$app = JFactory::getApplication ( 'site' );
			$params = $app->getParams ( 'com_activity' );
			$galleryType = $params->get ( 'gallery_type', 'pgwSlideshow' );
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