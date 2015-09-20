<?php
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentViewTalents extends JViewLegacy {
	function display($tpl = null) {
		$this->type = $this->get ( 'TalentType' );
		$this->items = $this->get ( 'Items' );
		$this->pagination = $this->get ( 'Pagination' );
		JHtml::stylesheet ( JURI::root () . $this->get ( 'Css' ) );
		$this->setDocument ();
		parent::display ( $tpl );
	}
	protected function setDocument() {
		$this->params = JFactory::getApplication ( 'site' )->getParams ( 'com_talent' );
		$params = $this->params;
		$this->heading = $this->type ? $this->type->title : $params->get ( 'page_title', '' );
		$document = JFactory::getDocument ();
		$title = $params->get ( 'page_title', '' );
		$document->setTitle ( $title );
		$pathway = JFactory::getApplication ()->getPathWay ();
		if ($this->type) {
			if (! $title) {
				$title = "{$this->type->title}";
			} else {
				$title = "{$title} - {$this->type->title}";
			}
			$pathway->addItem ( $this->type->title, '' );
		}
		$document->setTitle ( $title );
		if ($params->get ( 'menu-meta_description' )) {
			$document->setDescription ( $params->get ( 'menu-meta_description' ) );
		}
		if ($params->get ( 'menu-meta_keywords' )) {
			$document->setMetadata ( 'keywords', $params->get ( 'menu-meta_keywords' ) );
		}
		if ($params->get ( 'robots' )) {
			$document->setMetadata ( 'robots', $params->get ( 'robots' ) );
		}
	}
}