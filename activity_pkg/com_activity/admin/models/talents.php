<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class ActivityModelTalents extends JModelList 
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
		$jinput = JFactory::getApplication ()->input;
		
		
		// Create the base select statement.
		$query->select ( '*' )->from ( $db->quoteName ( '#__talent' ) );
		
		// Filter: like / search
		$search = $this->getState ( 'filter.search' );
		
		if (! empty ( $search )) {
			$like = $db->quote ( '%' . $search . '%' );
			$query->where ( 'title LIKE ' . $like );
		}
		
		// Filter by published state
		$published = $this->getState ( 'filter.published' );
		
		if (is_numeric ( $published )) {
			$query->where ( 'published = ' . ( int ) $published );
		} elseif ($published === '') {
			$query->where ( '(published IN (0, 1))' );
		}

		$params = $jinput->getArray(array());
		if (isset($params['id'])) {
			$ids = implode(',', $params['id']);
			if ($ids)
				$query->where ( "(id NOT IN ({$ids}))" );
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get ( 'list.ordering', 'title' );
		$orderDirn = $this->state->get ( 'list.direction', 'asc' );
		
		$query->order ( $db->escape ( $orderCol ) . ' ' . $db->escape ( $orderDirn ) );
		$query->order ( 'id asc' );
		
		return $query;
	}
}