<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentViewTalents extends JViewLegacy {
	function display($tpl = null) {
		$this->type = $this->get ( 'TalentType' );
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		$this->params = JFactory::getApplication ()->getParams ();
		$this->heading = $this->type->title;
		$this->return_page = base64_encode ( JURI::current () );
		JHtml::stylesheet ( JURI::root () . $this->get ( 'Css' ) );
		
		$this->setDocument ();
		
		parent::display ( $tpl );
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
	}
}