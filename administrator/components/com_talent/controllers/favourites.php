<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentControllerFavourites extends JControllerAdmin {
	protected $default_view = 'favourites';
	public function getModel($name = 'Favourite', $prefix = 'TalentModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
	public function publish() {
	}
	public function unpublish() {
	}
}