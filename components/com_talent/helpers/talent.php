<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
//
use Joomla\Registry\Registry;
// Require helper file
JLoader::register ( 'TalentHelper', JPATH_ADMINISTRATOR . '/components/com_talent/helpers/talent.php' );
JLoader::register ( 'JToolBarHelper', JPATH_ADMINISTRATOR . '/includes/toolbar.php' );
JLoader::register ( 'JSubMenuHelper', JPATH_ADMINISTRATOR . '/includes/subtoolbar.php' );
JLoader::register ( 'TalentRouter', JPATH_SITE . '/components/com_talent/router.php' );
//
class SiteTalentHelper extends TalentHelper {
	//
	public static function getTalentDetailsHtml($obj) {
		if (is_string ( $obj )) {
			$talent = static::getTalent ( $obj );
			$talent->index = 0;
		} else {
			$talent = static::getTalent ( $obj->id );
			$talent->index = $obj->index;
		}
		
		if ($talent) {
			self::normaliseTalentDetails ( $talent->user_details );
			$layout = new JLayoutFile ( 'talent_details', JPATH_SITE . '/components/com_talent/layouts' );
			return $layout->render ( $talent );
		}
		return '';
	}
	//
	public static function normaliseTalentDetails(&$details) {
		$lang = & JFactory::getLanguage ();
		$details ['gender'] = $details ['gender'] ? JText::_ ( 'COM_TALENT_MALE' ) : JText::_ ( 'COM_TALENT_FEMALE' );
	}
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
	//
	public static function getTalentDetailLink($talent, $type) {
		$base_url = 'index.php?option=com_talent&view=talent&cid=';
		return JRoute::_ ( "{$base_url}{$type->id}&id={$talent->id}" );
	}
}
