<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class ActivityControllerTypes extends JControllerAdmin 
{
	protected $default_view = 'types';
	
	public function getModel($name = 'Type', $prefix = 'ActivityModel', $config = array('ignore_request' => true)) 
	{
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
}