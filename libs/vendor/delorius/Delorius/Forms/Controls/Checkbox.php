<?php
namespace Delorius\Forms\Controls;

/**
 * Check box control. Allows the user to select a true or false condition.
 *
 *
 */
class Checkbox extends BaseControl
{

	/**
	 * @param  string  label
	 */
	public function __construct($label = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'checkbox';
		$this->value = FALSE;
	}



	/**
	 * Sets control's value.
	 * @param  bool
	 * @return Checkbox  provides a fluent interface
	 */
	public function setValue($value)
	{
		$this->value = is_scalar($value) ? (bool) $value : FALSE;
		return $this;
	}



	/**
	 * Generates control's HTML element.
	 * @return \Delorius\View\Html
	 */
	public function getControl()
	{
		return parent::getControl()->checked($this->value);
	}

}
