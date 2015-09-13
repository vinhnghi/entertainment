<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentViewTalents extends JViewLegacy {
	function display($tpl = null) {
		$this->type = $this->get ( 'TalentType' );
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->params = JFactory::getApplication ()->getParams ();
		$this->heading = $this->type->title;
		parent::display ( $tpl );
		$this->setDocument ();
	}
	protected function setDocument() {
		$document = JFactory::getDocument ();
		
		$title = $this->params->get ( 'page_title', '' );
		if (! $title) {
			$title = $this->type->title;
		} else {
			$title = "{$title} - {$this->type->title}";
		}
		$document->setTitle ( $title );
		$document->addStyleSheet ( JURI::root () . $this->get ( 'Css' ) );
	}
}