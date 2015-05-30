<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityControllerActivities extends JControllerLegacy 
{
	
	protected $default_view = 'types';
	
	public function getModel($name = 'ActivityForm', $prefix = 'ActivityModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}

}