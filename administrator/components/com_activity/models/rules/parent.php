<?php
// No direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class JFormRuleParent extends JFormRule 
{
	protected $regex = '^[\w\W]+$';

	public function test(SimpleXMLElement $element, $value, $group = null, Registry $input = null, JForm $form = null)
	{
		if (empty($this->regex))
		{
			throw new UnexpectedValueException(sprintf('%s has invalid regex.', get_class($this)));
		}

		// Add unicode property support if available.
		if (JCOMPAT_UNICODE_PROPERTIES)
		{
			$this->modifiers = (strpos($this->modifiers, 'u') !== false) ? $this->modifiers : $this->modifiers . 'u';
		}

		// Test the value against the regular expression.
		if (preg_match(chr(1) . $this->regex . chr(1) . $this->modifiers, $value))
		{
			return true;
		}
	}
}