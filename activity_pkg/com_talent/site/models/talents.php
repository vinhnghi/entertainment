<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class TalentModelTalents extends JModelList {
	public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] = array (
					'id',
					'title',
					'published' 
			);
		}
		parent::__construct ( $config );
	}
	public function getTalent() {
		$table = $this->getTable ( 'Type', 'ActivityTable' );
		$jinput = JFactory::getApplication ()->input;
		$cid = $jinput->get ( 'cid', 0 );
		$table->load ( $cid );
		return $table;
	}
	protected function getListQuery() {
		$jinput = JFactory::getApplication ()->input;
		$cid = $jinput->get ( 'cid', 0 );
		if (! $cid) {
			throw new Exception ( JText::_ ( 'Activity type not found' ) );
			return;
		}
		
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array (
				'a.*',
				'c.id AS typeid',
				'c.title AS typetitle' 
		)
		// 'd.name AS author',
		;
		$query->select ( implode ( ",", $fields ) )->from ( '#__activity AS a' );
		$query->leftJoin ( '#__activity_activity_type AS b ON a.id=b.activity_id' );
		$query->leftJoin ( '#__activity_type AS c ON c.id=b.activity_type_id' );
		// $query->leftJoin ( '#__users AS d ON d.id=a.created_by' );
		$query->where ( 'b.activity_type_id = ' . ( int ) $cid );
		$query->where ( 'a.published = 1' );
		$query->where ( 'c.published = 1' );
		
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		
		return $query;
	}
	public function getCss() {
		return 'components/com_activity/models/forms/activity.css';
	}
}