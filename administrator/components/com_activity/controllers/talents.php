<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityControllerTalents extends JControllerAdmin 
{
	
	protected $default_view = 'talents';
	
	public function getModel($name = 'Talent', $prefix = 'ActivityModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}

}