<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class Com_ActivityInstallerScript {
	function install($parent) {
		// $parent is the class calling this method
		JFolder::create ( 'images/com_activity/' );
		$parent->getParent ()->setRedirectURL ( 'index.php?option=com_activity' );
	}
	function uninstall($parent) {
		echo '<p>' . JText::_ ( 'COM_ACTIVITY_UNINSTALL_TEXT' ) . '</p>';
	}
	function update($parent) {
		// $parent is the class calling this method
		echo '<p>' . JText::sprintf ( 'COM_ACTIVITY_UPDATE_TEXT', $parent->get ( 'manifest' )->version ) . '</p>';
	}
	function preflight($type, $parent) {
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_ ( 'COM_ACTIVITY_PREFLIGHT_' . $type . '_TEXT' ) . '</p>';
	}
	function postflight($type, $parent) {
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_ ( 'COM_ACTIVITY_POSTFLIGHT_' . $type . '_TEXT' ) . '</p>';
	}
}