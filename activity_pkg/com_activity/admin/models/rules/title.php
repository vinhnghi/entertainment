<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JFormRuleTitle extends JFormRule 
{
	protected $regex = '^[\w\W]+$';
}