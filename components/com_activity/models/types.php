<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityModelTypes extends JModelList 
{
	public function __construct($config = array()) 
	{
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] = array (
					'id',
					'title',
					'published' 
			);
		}
		parent::__construct ( $config );
	}
	
	protected function getListQuery() 
	{
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$query->select ( '*' )->from ( $db->quoteName ( '#__activity_type' ) );
		$query->where ( 'id <> 1 AND published = 1');
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		
		return $query;
	}
	
	public function getCss()
	{
		return 'components/com_activity/models/forms/activity.css';
	}
	
}