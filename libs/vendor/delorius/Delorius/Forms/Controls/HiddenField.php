<?php
namespace Delorius\Forms\Controls;


/**
 * Hidden form control used to store a non-displayed value.
 *
 *
 */
class HiddenField extends BaseControl
{
	/** @var string */
	private $forcedValue;



	public function __construct($forcedValue = NULL)
	{
		parent::__construct();
		$this->control->type = 'hidden';
		$this->value = (string) $forcedValue;
		$this->forcedValue = $forcedValue;
	}



	/**
	 * Bypasses label generation.
	 * @return void
	 */
	public function getLabel($caption = NULL)
	{
		return NULL;
	}



	/**
	 * Sets control's value.
	 * @param  string
	 * @return HiddenField  provides a fluent interface
	 */
	public function setValue($value)
	{
		$this->value = is_scalar($value) ? (string) $value : '';
		return $this;
	}



	/**
	 * Generates control's HTML element.
	 * @return \Delorius\View\Html
	 */
	public function getControl()
	{
		return parent::getControl()
			->value($this->forcedValue === NULL ? $this->value : $this->forcedValue)
			->data('delorius-rules', NULL);
	}

}
