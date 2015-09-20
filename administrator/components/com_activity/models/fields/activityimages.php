<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JFormFieldActivityImages extends JFormField 
{
	protected $type = 'ActivityImages';
	
	protected function getJson()
	{
		$jinput = JFactory::getApplication ()->input;
		
		$db = JFactory::getDbo ();
		$query = $db->getQuery ( true )->select ( 'a.*' );
		$query->from ( '#__activity_assets AS a' );
		$query->where ( '(a.activity_id = '.$jinput->get ( 'id', 0 ).')' );
		$db->setQuery ( $query );
		return json_encode($db->loadObjectList ());
	}
	
	protected function getInput()
	{
		$html = array();
		$id = "{$this->element ['name']}";
		$blockclass = $this->getAttribute('blockclass') ? $this->getAttribute('blockclass') : 'span5';
		$html[] = "<div class='{$this->type}' id='{$id}' blockclass='{$blockclass}'>";
		$html[] = '<script type="text/javascript">';
		$html[] = "var {$id}={$this->getJson()};";
		$html[] = '</script>';
		$html[] = '</div>';
		return implode($html, '');
	}
}