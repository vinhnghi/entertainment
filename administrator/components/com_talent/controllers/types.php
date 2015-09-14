<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentControllerTypes extends JControllerAdmin {
	protected $default_view = 'types';
	public function getModel($name = 'Type', $prefix = 'TalentModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
}