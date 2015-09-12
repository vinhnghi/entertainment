<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ();
class TalentController extends JControllerLegacy {
	protected $default_view = 'types';
	public function display($cachable = false, $urlparams = array()) {
		return parent::display ();
	}
}
