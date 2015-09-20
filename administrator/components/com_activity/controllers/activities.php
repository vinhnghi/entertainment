<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityControllerActivities extends JControllerAdmin {
	protected $default_view = 'activitiess';
	public function getModel($name = 'Activity', $prefix = 'ActivityModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
}