<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentControllerAgents extends JControllerAdmin {
	protected $default_view = 'agents';
	public function getModel($name = 'Agent', $prefix = 'TalentModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
}