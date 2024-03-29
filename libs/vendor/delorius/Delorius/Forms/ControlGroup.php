<?php
namespace Delorius\Forms;

use Delorius\Exception\Error;
use Delorius\Core\Object;

/**
 * A user group of form controls.
 *
 * @property-read array $controls
 * @property-read array $options
 */
class ControlGroup extends Object
{
	/** @var \SplObjectStorage */
	protected $controls;

	/** @var array user options */
	private $options = array();



	public function __construct()
	{
		$this->controls = new \SplObjectStorage;
	}



	/**
	 * @return ControlGroup  provides a fluent interface
	 */
	public function add()
	{
		foreach (func_get_args() as $num => $item) {
			if ($item instanceof IControl) {
				$this->controls->attach($item);
			} elseif ($item instanceof \Traversable || is_array($item)) {
				foreach ($item as $control) {
					$this->controls->attach($control);
				}
			} else {
				throw new Error("Only IFormControl items are allowed, the #$num parameter is invalid.");
			}
		}
		return $this;
	}



	/**
	 * @return array IFormControl
	 */
	public function getControls()
	{
		return iterator_to_array($this->controls);
	}



	/**
	 * Sets user-specific option.
	 * Options recognized by DefaultFormRenderer
	 * - 'label' - textual or Html object label
	 * - 'visual' - indicates visual group
	 * - 'container' - container as Html object
	 * - 'description' - textual or Html object description
	 * - 'embedNext' - describes how render next group
	 *
	 * @param  string key
	 * @param  mixed  value
	 * @return ControlGroup  provides a fluent interface
	 */
	public function setOption($key, $value)
	{
		if ($value === NULL) {
			unset($this->options[$key]);

		} else {
			$this->options[$key] = $value;
		}
		return $this;
	}



	/**
	 * Returns user-specific option.
	 * @param  string key
	 * @param  mixed  default value
	 * @return mixed
	 */
	final public function getOption($key, $default = NULL)
	{
		return isset($this->options[$key]) ? $this->options[$key] : $default;
	}



	/**
	 * Returns user-specific options.
	 * @return array
	 */
	final public function getOptions()
	{
		return $this->options;
	}

}
