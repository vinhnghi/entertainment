<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
// Require helper file
JLoader::register ( 'ActivityHelper', JPATH_ADMINISTRATOR . '/components/com_activity/helpers/activity.php' );
JLoader::register ( 'ActivityRouter', JPATH_SITE . '/components/com_activity/router.php' );
//
abstract class SiteActivityHelper extends ActivityHelper {
	//
	public static function getActivityDetailLink($activity, $cid) {
		$cid = ( int ) $cid;
		$base_url = 'index.php?option=com_activity&view=activity&cid=';
		return JRoute::_ ( "{$base_url}{$cid}&id={$activity->id}" );
	}
	//
	public static function getImages($obj) {
		if (is_string ( $obj )) {
			$registry = new Registry ();
			$registry->loadString ( $obj );
			$obj = $registry->toArray ();
		}
		$intro = new stdClass ();
		$intro->src = $obj ['image_intro'] | $obj ['image_fulltext'];
		$intro->alt = $obj ['image_intro_alt'] | $obj ['image_fulltext_alt'];
		$intro->caption = $obj ['image_intro_caption'] | $obj ['image_fulltext_caption'];
		$fulltext = new stdClass ();
		$fulltext->src = $obj ['image_intro'] | $obj ['image_fulltext'];
		$fulltext->alt = $obj ['image_intro_alt'] | $obj ['image_fulltext_alt'];
		$fulltext->caption = $obj ['image_intro_caption'] | $obj ['image_fulltext_caption'];
		return array (
				'intro' => $intro->src ? $intro : null,
				'fulltext' => $fulltext->src ? $fulltext : null 
		);
	}
	//
	public static function getIntroImage($obj) {
		if (is_string ( $obj )) {
			$registry = new Registry ();
			$registry->loadString ( $obj );
			$obj = $registry->toArray ();
		}
		$image = new stdClass ();
		$image->src = $obj ['image_intro'] | $obj ['image_fulltext'];
		$image->alt = $obj ['image_intro_alt'] | $obj ['image_fulltext_alt'];
		$image->caption = $obj ['image_intro_caption'] | $obj ['image_fulltext_caption'];
		if ($image->src) {
			return $image;
		}
		return null;
	}
	//
	public static function getFulltextImage($obj) {
		if (is_string ( $obj )) {
			$registry = new Registry ();
			$registry->loadString ( $obj );
			$obj = $registry->toArray ();
		}
		$image = new stdClass ();
		$image->src = $obj ['image_fulltext'] | $obj ['image_intro'];
		$image->alt = $obj ['image_fulltext_alt'] | $obj ['image_intro_alt'];
		$image->caption = $obj ['image_fulltext_caption'] | $obj ['image_intro_caption'];
		if ($image->src) {
			return $image;
		}
		return null;
	}
}