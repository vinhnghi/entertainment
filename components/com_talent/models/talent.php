<?php
defined ( '_JEXEC' ) or die ();

use Joomla\Registry\Registry;
class TalentModelTalent extends JModelAdmin {
	public function getTalentType() {
		return TalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	public function getItem($pk = null) {
		return TalentHelper::getTalent ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
	}
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		return $this->loadForm ( 'com_talent.talent', 'talent', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
	}
}
