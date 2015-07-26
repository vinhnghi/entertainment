<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JFormFieldActivitySlideShow extends JFormField {
	protected $type = 'ActivitySlideShow';
	protected $thumbFolder = 'thumbs';
	protected function getMimeType($filename) {
		$realpath = realpath ( $filename );
		if (function_exists ( 'mime_content_type' )) {
			return mime_content_type ( $realpath );
		} else {
			$idx = explode ( '.', $realpath );
			$count_explode = count ( $idx );
			$idx = strtolower ( $idx [$count_explode - 1] );
			$mimet = array (
					'ai' => 'application/postscript',
					'aif' => 'audio/x-aiff',
					'aifc' => 'audio/x-aiff',
					'aiff' => 'audio/x-aiff',
					'asc' => 'text/plain',
					'atom' => 'application/atom+xml',
					'avi' => 'video/x-msvideo',
					'bcpio' => 'application/x-bcpio',
					'bmp' => 'image/bmp',
					'cdf' => 'application/x-netcdf',
					'cgm' => 'image/cgm',
					'cpio' => 'application/x-cpio',
					'cpt' => 'application/mac-compactpro',
					'crl' => 'application/x-pkcs7-crl',
					'crt' => 'application/x-x509-ca-cert',
					'csh' => 'application/x-csh',
					'css' => 'text/css',
					'dcr' => 'application/x-director',
					'dir' => 'application/x-director',
					'djv' => 'image/vnd.djvu',
					'djvu' => 'image/vnd.djvu',
					'doc' => 'application/msword',
					'dtd' => 'application/xml-dtd',
					'dvi' => 'application/x-dvi',
					'dxr' => 'application/x-director',
					'eps' => 'application/postscript',
					'etx' => 'text/x-setext',
					'ez' => 'application/andrew-inset',
					'gif' => 'image/gif',
					'gram' => 'application/srgs',
					'grxml' => 'application/srgs+xml',
					'gtar' => 'application/x-gtar',
					'hdf' => 'application/x-hdf',
					'hqx' => 'application/mac-binhex40',
					'html' => 'text/html',
					'html' => 'text/html',
					'ice' => 'x-conference/x-cooltalk',
					'ico' => 'image/x-icon',
					'ics' => 'text/calendar',
					'ief' => 'image/ief',
					'ifb' => 'text/calendar',
					'iges' => 'model/iges',
					'igs' => 'model/iges',
					'jpe' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'jpg' => 'image/jpeg',
					'js' => 'application/x-javascript',
					'kar' => 'audio/midi',
					'latex' => 'application/x-latex',
					'm3u' => 'audio/x-mpegurl',
					'man' => 'application/x-troff-man',
					'mathml' => 'application/mathml+xml',
					'me' => 'application/x-troff-me',
					'mesh' => 'model/mesh',
					'mid' => 'audio/midi',
					'midi' => 'audio/midi',
					'mif' => 'application/vnd.mif',
					'mov' => 'video/quicktime',
					'movie' => 'video/x-sgi-movie',
					'mp2' => 'audio/mpeg',
					'mp3' => 'audio/mpeg',
					'mpe' => 'video/mpeg',
					'mpeg' => 'video/mpeg',
					'mpg' => 'video/mpeg',
					'mpga' => 'audio/mpeg',
					'ms' => 'application/x-troff-ms',
					'msh' => 'model/mesh',
					'mxu m4u' => 'video/vnd.mpegurl',
					'nc' => 'application/x-netcdf',
					'oda' => 'application/oda',
					'ogg' => 'application/ogg',
					'pbm' => 'image/x-portable-bitmap',
					'pdb' => 'chemical/x-pdb',
					'pdf' => 'application/pdf',
					'pgm' => 'image/x-portable-graymap',
					'pgn' => 'application/x-chess-pgn',
					'php' => 'application/x-httpd-php',
					'php4' => 'application/x-httpd-php',
					'php3' => 'application/x-httpd-php',
					'phtml' => 'application/x-httpd-php',
					'phps' => 'application/x-httpd-php-source',
					'png' => 'image/png',
					'pnm' => 'image/x-portable-anymap',
					'ppm' => 'image/x-portable-pixmap',
					'ppt' => 'application/vnd.ms-powerpoint',
					'ps' => 'application/postscript',
					'qt' => 'video/quicktime',
					'ra' => 'audio/x-pn-realaudio',
					'ram' => 'audio/x-pn-realaudio',
					'ras' => 'image/x-cmu-raster',
					'rdf' => 'application/rdf+xml',
					'rgb' => 'image/x-rgb',
					'rm' => 'application/vnd.rn-realmedia',
					'roff' => 'application/x-troff',
					'rtf' => 'text/rtf',
					'rtx' => 'text/richtext',
					'sgm' => 'text/sgml',
					'sgml' => 'text/sgml',
					'sh' => 'application/x-sh',
					'shar' => 'application/x-shar',
					'shtml' => 'text/html',
					'silo' => 'model/mesh',
					'sit' => 'application/x-stuffit',
					'skd' => 'application/x-koan',
					'skm' => 'application/x-koan',
					'skp' => 'application/x-koan',
					'skt' => 'application/x-koan',
					'smi' => 'application/smil',
					'smil' => 'application/smil',
					'snd' => 'audio/basic',
					'spl' => 'application/x-futuresplash',
					'src' => 'application/x-wais-source',
					'sv4cpio' => 'application/x-sv4cpio',
					'sv4crc' => 'application/x-sv4crc',
					'svg' => 'image/svg+xml',
					'swf' => 'application/x-shockwave-flash',
					't' => 'application/x-troff',
					'tar' => 'application/x-tar',
					'tcl' => 'application/x-tcl',
					'tex' => 'application/x-tex',
					'texi' => 'application/x-texinfo',
					'texinfo' => 'application/x-texinfo',
					'tgz' => 'application/x-tar',
					'tif' => 'image/tiff',
					'tiff' => 'image/tiff',
					'tr' => 'application/x-troff',
					'tsv' => 'text/tab-separated-values',
					'txt' => 'text/plain',
					'ustar' => 'application/x-ustar',
					'vcd' => 'application/x-cdlink',
					'vrml' => 'model/vrml',
					'vxml' => 'application/voicexml+xml',
					'wav' => 'audio/x-wav',
					'wbmp' => 'image/vnd.wap.wbmp',
					'wbxml' => 'application/vnd.wap.wbxml',
					'wml' => 'text/vnd.wap.wml',
					'wmlc' => 'application/vnd.wap.wmlc',
					'wmlc' => 'application/vnd.wap.wmlc',
					'wmls' => 'text/vnd.wap.wmlscript',
					'wmlsc' => 'application/vnd.wap.wmlscriptc',
					'wmlsc' => 'application/vnd.wap.wmlscriptc',
					'wrl' => 'model/vrml',
					'xbm' => 'image/x-xbitmap',
					'xht' => 'application/xhtml+xml',
					'xhtml' => 'application/xhtml+xml',
					'xls' => 'application/vnd.ms-excel',
					'xml xsl' => 'application/xml',
					'xpm' => 'image/x-xpixmap',
					'xslt' => 'application/xslt+xml',
					'xul' => 'application/vnd.mozilla.xul+xml',
					'xwd' => 'image/x-xwindowdump',
					'xyz' => 'chemical/x-xyz',
					'flv' => 'video/x-flv',
					'mp4' => 'video/mp4',
					'3gp' => 'video/3gpp',
					'mov' => 'video/quicktime',
					'avi' => 'video/x-msvideo',
					'wmv' => 'video/x-ms-wmv',
					'zip' => 'application/zip'
			);
				
			if (isset ( $mimet [$idx] )) {
				return $mimet [$idx];
			} else {
				return 'application/octet-stream';
			}
			return $mimetype;
		}
		return false;
	}
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
			$ffmpeg = 'ffmpeg';
			$interval = 1;
			$size = '80x80';
			$cmd = "$ffmpeg -i \"$videoPath\" -deinterlace -an -ss $interval -f mjpeg -t 1 -s $size -r 1 -y -vcodec mjpeg -f mjpeg \"$thumbPath\" 2>&1";
			exec ( $cmd, $output, $retval );
		}
	}
	protected function getImage($image) {
		$html = array ();
		if (file_exists ( $image->src )) {
			$mime = $this->getMimeType ( $image->src );
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
			$galleryType = 'pgwSlideshow';//$params->get ( 'gallery_type', 'pgwSlideshow' );
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