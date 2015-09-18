<?php
defined ( '_JEXEC' ) or die ();

use Joomla\Registry\Registry;
class TalentModelTalent extends JModelAdmin {
	public function getTalentType() {
		return SiteTalentHelper::getTalentType ( JFactory::getApplication ()->input->get ( 'cid', 0 ) );
	}
	public function getItem($pk = null) {
		$item = SiteTalentHelper::getTalent ( JFactory::getApplication ()->input->get ( 'id', 0 ) );
		return $item;
	}
	public function getForm($data = array(), $loadData = true) {
		// Get the form.
		return $this->loadForm ( 'com_talent.talent', 'talent', array (
				'control' => 'jform',
				'load_data' => $loadData 
		) );
	}
	public function getCss() {
		return 'components/com_talent/src/css/talent.css';
	}
}
