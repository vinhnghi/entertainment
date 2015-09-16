<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentControllerFavourite extends JControllerForm {
	protected $default_view = 'favourite';
	protected function allowAdd($data = array()) {
		return parent::allowAdd ( $data );
	}
	protected function allowEdit($data = array(), $key = 'id') {
		$id = isset ( $data [$key] ) ? $data [$key] : 0;
		if (! empty ( $id )) {
			return JFactory::getUser ()->authorise ( "core.edit", "com_talent.favourite." . $id );
		}
	}
	public function add() {
		die ();
	}
	public function delete() {
		die ();
	}
}