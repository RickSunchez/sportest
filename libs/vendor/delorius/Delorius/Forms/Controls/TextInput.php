<?php
namespace Delorius\Forms\Controls;

use Delorius\Forms\Form;
use Delorius\Forms\Rule;
use Delorius\Utils\Strings;

/**
 * Single line text input control.
 *
 *
 * @property-write $type
 */
class TextInput extends TextBase
{

	/**
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL, $cols = NULL, $maxLength = NULL)
	{
		parent::__construct($label);
		$this->control->type = 'text';
		$this->control->size = $cols;
		$this->control->maxlength = $maxLength;
		$this->addFilter($this->sanitize);
		$this->value = '';
	}



	/**
	 * Filter: removes unnecessary whitespace and shortens value to control's max length.
	 * @return string
	 */
	public function sanitize($value)
	{
		if ($this->control->maxlength && Strings::length($value) > $this->control->maxlength) {
			$value =  Strings::substring($value, 0, $this->control->maxlength);
		}
		return Strings::trim(strtr($value, "\r\n", '  '));
	}



	/**
	 * Changes control's type attribute.
	 * @param  string
	 * @return BaseControl  provides a fluent interface
	 */
	public function setType($type)
	{
		$this->control->type = $type;
		return $this;
	}



	/** @deprecated */
	public function setPasswordMode($mode = TRUE)
	{
		$this->control->type = $mode ? 'password' : 'text';
		return $this;
	}



	/**
	 * Generates control's HTML element.
	 * @return \Delorius\View\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();
		foreach ($this->getRules() as $rule) {
			if ($rule->isNegative || $rule->type !== Rule::VALIDATOR) {

			} elseif ($rule->operation === Form::RANGE && $control->type !== 'text') {
				list($control->min, $control->max) = $rule->arg;

			} elseif ($rule->operation === Form::PATTERN) {
				$control->pattern = $rule->arg;
			}
		}
		if ($control->type !== 'password') {
			$control->value = $this->getValue() === '' ? $this->translate($this->emptyValue) : $this->value;
		}
		return $control;
	}

}
