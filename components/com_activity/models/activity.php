<?php

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

class ActivityModelActivity extends JModelAdmin
{
	public function getActivityType()
	{
		$table = $this->getTable('Type', 'ActivityTable');
		$jinput = JFactory::getApplication ()->input;
		$cid = $jinput->get ( 'cid', 0 );
		$table->load ( $cid );
		return $table;
	}
	
	public function getItem($pk = null)
	{
		$user	= JFactory::getUser();

		$jinput = JFactory::getApplication ()->input;
		$id = $jinput->get ( 'id', 0 );
		$cid = $jinput->get ( 'cid', 0 );
		if (!$id) {
			throw new Exception(JText::_('Activity not found'));
			return ;
		}
		
		// Initialize variables.
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true );
		
		// Create the base select statement.
		$fields = array( 
				'a.*',
				'c.id AS cid',
				'c.title AS ctitle',
				'd.name AS author', 
		);
		$query->select ( implode ( ",", $fields ) )->from ( '#__activity AS a' );
		$query->leftJoin ( '#__activity_activity_type AS b ON a.id=b.activity_id' );
		$query->leftJoin ( '#__activity_type AS c ON c.id=b.activity_type_id' );
		$query->leftJoin ( '#__users AS d ON d.id=a.created_by' );
		$query->where ( 'c.published = 1' );
		if ($cid) {
			$query->where ( 'b.activity_type_id = ' . ( int ) $cid );
			$query->where ( 'c.id = ' . ( int ) $cid );
		}
		$query->where ( 'a.published = 1 AND a.id=' . $id );

		$db->setQuery($query);
		$data = $db->loadObject();
		
		return $data;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		return $this->loadForm('com_activity.activity', 'activity', array('control' => 'jform', 'load_data' => $loadData));
	}
	
}
