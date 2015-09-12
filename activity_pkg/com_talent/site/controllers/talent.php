<?php
defined ( '_JEXEC' ) or die ();
class TalentControllerTalent extends JControllerForm {
	public function getModel($name = 'TalentForm', $prefix = 'TalentModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel ( $name, $prefix, $config );
		return $model;
	}
}
