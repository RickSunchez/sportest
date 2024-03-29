<?php
namespace Delorius\Forms;

use Delorius\Exception\Error;
use Delorius\Utils\ArrayHash;
use Delorius\Forms\Controls\TextInput;
use Delorius\Forms\Controls\TextArea;
use Delorius\Forms\Controls\UploadControl;
use Delorius\Forms\Controls\HiddenField;
use Delorius\Forms\Controls\Checkbox;
use Delorius\Forms\Controls\RadioList;
use Delorius\Forms\Controls\SelectBox;
use Delorius\Forms\Controls\MultiSelectBox;
use Delorius\Forms\Controls\SubmitButton;
use Delorius\Forms\Controls\Button;
use Delorius\Forms\Controls\ImageButton;


/**
 * Container for form controls.
 *
 * @property-write $defaults
 * @property   \Delorius\Utils\ArrayHash $values
 * @property-read bool $valid
 * @property   ControlGroup $currentGroup
 * @property-read \ArrayIterator $controls
 * @property-read Form $form
 */
class Container extends \Delorius\ComponentModel\Container implements \ArrayAccess
{
	/** @var array of function(Form $sender); Occurs when the form is validated */
	public $onValidate;

	/** @var ControlGroup */
	protected $currentGroup;

	/** @var bool */
	protected $valid;



	/********************* data exchange ****************d*g**/



	/**
	 * Fill-in with default values.
	 * @param  array|Traversable  values used to fill the form
	 * @param  bool     erase other default values?
	 * @return Container  provides a fluent interface
	 */
	public function setDefaults($values, $erase = FALSE)
	{
		$form = $this->getForm(FALSE);
		if (!$form || !$form->isAnchored() || !$form->isSubmitted()) {
			$this->setValues($values, $erase);
		}
		return $this;
	}



	/**
	 * Fill-in with values.
	 * @param  array|Traversable  values used to fill the form
	 * @param  bool     erase other controls?
	 * @return Container  provides a fluent interface
	 */
	public function setValues($values, $erase = FALSE)
	{
		if ($values instanceof \Traversable) {
			$values = iterator_to_array($values);

		} elseif (!is_array($values)) {
			throw new Error("First parameter must be an array, " . gettype($values) ." given.");
		}

		foreach ($this->getComponents() as $name => $control) {
			if ($control instanceof IControl) {
				if (array_key_exists($name, $values)) {
					$control->setValue($values[$name]);

				} elseif ($erase) {
					$control->setValue(NULL);
				}

			} elseif ($control instanceof Container) {
				if (array_key_exists($name, $values)) {
					$control->setValues($values[$name], $erase);

				} elseif ($erase) {
					$control->setValues(array(), $erase);
				}
			}
		}
		return $this;
	}



	/**
	 * Returns the values submitted by the form.
	 * @param  bool  return values as an array?
	 * @return \Delorius\Utils\ArrayHash|array
	 */
	public function getValues($asArray = FALSE)
	{
		$values = $asArray ? array() : new ArrayHash();
		foreach ($this->getComponents() as $name => $control) {
			if ($control instanceof IControl && !$control->isDisabled() && !$control instanceof ISubmitterControl) {
				$values[$name] = $control->getValue();

			} elseif ($control instanceof Container) {
				$values[$name] = $control->getValues($asArray);
			}
		}
		return $values;
	}



	/********************* validation ****************d*g**/



	/**
	 * Is form valid?
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->valid === NULL) {
			$this->validate();
		}
		return $this->valid;
	}



	/**
	 * Performs the server side validation.
	 * @return void
	 */
	public function validate()
	{
		$this->valid = TRUE;
		foreach ($this->getControls() as $control) {
			if (!$control->getRules()->validate()) {
				$this->valid = FALSE;
			}
		}
		$this->onValidate($this);
	}



	/********************* form building ****************d*g**/



	/**
	 * @return Container  provides a fluent interface
	 */
	public function setCurrentGroup(ControlGroup $group = NULL)
	{
		$this->currentGroup = $group;
		return $this;
	}



	/**
	 * Returns current group.
	 * @return ControlGroup
	 */
	public function getCurrentGroup()
	{
		return $this->currentGroup;
	}



	/**
	 * Adds the specified component to the IContainer.
	 * @param  IComponent
	 * @param  string
	 * @param  string
	 * @return Container  provides a fluent interface
	 * @throws Error
	 */
	public function addComponent(\Delorius\ComponentModel\IComponent $component, $name, $insertBefore = NULL)
	{

		parent::addComponent($component, $name, $insertBefore);
		if ($this->currentGroup !== NULL && $component instanceof IControl) {
			$this->currentGroup->add($component);
		}
		return $this;
	}



	/**
	 * Iterates over all form controls.
	 * @return \ArrayIterator
	 */
	public function getControls()
	{
		return $this->getComponents(TRUE, 'Delorius\Forms\IControl');
	}



	/**
	 * Returns form.
	 * @param  bool   throw exception if form doesn't exist?
	 * @return Form
	 */
	public function getForm($need = TRUE)
	{
		return $this->lookup('Delorius\Forms\Form', $need);
	}



	/********************* control factories ****************d*g**/



	/**
	 * Adds single-line text input control to the form.
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  maximum number of characters the user may enter
	 * @return \Delorius\Forms\Controls\TextInput
	 */
	public function addText($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
	    return $this[$name] = new TextInput($label, $cols, $maxLength);
	}



	/**
	 * Adds single-line text input control used for sensitive input such as passwords.
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  maximum number of characters the user may enter
	 * @return \Delorius\Forms\Controls\TextInput
	 */
	public function addPassword($name, $label = NULL, $cols = NULL, $maxLength = NULL)
	{
		$control = new TextInput($label, $cols, $maxLength);
		$control->setType('password');
		return $this[$name] = $control;
	}



	/**
	 * Adds multi-line text input control to the form.
	 * @param  string  control name
	 * @param  string  label
	 * @param  int  width of the control
	 * @param  int  height of the control in text lines
	 * @return \Delorius\Forms\Controls\TextArea
	 */
	public function addTextArea($name, $label = NULL, $cols = 40, $rows = 10)
	{
		return $this[$name] = new TextArea($label, $cols, $rows);
	}



	/**
	 * Adds control that allows the user to upload files.
	 * @param  string  control name
	 * @param  string  label
	 * @return \Delorius\Forms\Controls\UploadControl
	 */
	public function addUpload($name, $label = NULL)
	{
		return $this[$name] = new UploadControl($label);
	}



	/**
	 * Adds hidden form control used to store a non-displayed value.
	 * @param  string  control name
	 * @param  mixed   default value
	 * @return \Delorius\Forms\Controls\HiddenField
	 */
	public function addHidden($name, $default = NULL)
	{
		$control = new HiddenField;
		$control->setDefaultValue($default);
		return $this[$name] = $control;
	}



	/**
	 * Adds check box control to the form.
	 * @param  string  control name
	 * @param  string  caption
	 * @return \Delorius\Forms\Controls\Checkbox
	 */
	public function addCheckbox($name, $caption = NULL)
	{
		return $this[$name] = new Checkbox($caption);
	}



	/**
	 * Adds set of radio button controls to the form.
	 * @param  string  control name
	 * @param  string  label
	 * @param  array   options from which to choose
	 * @return \Delorius\Forms\Controls\RadioList
	 */
	public function addRadioList($name, $label = NULL, array $items = NULL)
	{
		return $this[$name] = new RadioList($label, $items);
	}



	/**
	 * Adds select box control that allows single item selection.
	 * @param  string  control name
	 * @param  string  label
	 * @param  array   items from which to choose
	 * @param  int     number of rows that should be visible
	 * @return \Delorius\Forms\Controls\SelectBox
	 */
	public function addSelect($name, $label = NULL, array $items = NULL, $size = NULL)
	{
		return $this[$name] = new SelectBox($label, $items, $size);
	}



	/**
	 * Adds select box control that allows multiple item selection.
	 * @param  string  control name
	 * @param  string  label
	 * @param  array   options from which to choose
	 * @param  int     number of rows that should be visible
	 * @return \Delorius\Forms\Controls\MultiSelectBox
	 */
	public function addMultiSelect($name, $label = NULL, array $items = NULL, $size = NULL)
	{
		return $this[$name] = new MultiSelectBox($label, $items, $size);
	}



	/**
	 * Adds button used to submit form.
	 * @param  string  control name
	 * @param  string  caption
	 * @return \Delorius\Forms\Controls\SubmitButton
	 */
	public function addSubmit($name, $caption = NULL)
	{
		return $this[$name] = new SubmitButton($caption);
	}



	/**
	 * Adds push buttons with no default behavior.
	 * @param  string  control name
	 * @param  string  caption
	 * @return \Delorius\Forms\Controls\Button
	 */
	public function addButton($name, $caption)
	{
		return $this[$name] = new Button($caption);
	}



	/**
	 * Adds graphical button used to submit form.
	 * @param  string  control name
	 * @param  string  URI of the image
	 * @param  string  alternate text for the image
	 * @return \Delorius\Forms\Controls\ImageButton
	 */
	public function addImage($name, $src = NULL, $alt = NULL)
	{
		return $this[$name] = new ImageButton($src, $alt);
	}



	/**
	 * Adds naming container to the form.
	 * @param  string  name
	 * @return Container
	 */
	public function addContainer($name)
	{
		$control = new Container;
		$control->currentGroup = $this->currentGroup;
		return $this[$name] = $control;
	}



	/********************* interface \ArrayAccess ****************d*g**/



	/**
	 * Adds the component to the container.
	 * @param  string  component name
	 * @param  \Delorius\ComponentModel\IComponent
	 * @return void
	 */
	final public function offsetSet($name, $component)
	{
		$this->addComponent($component, $name);
	}



	/**
	 * Returns component specified by name. Throws exception if component doesn't exist.
	 * @param  string  component name
	 * @return \Delorius\ComponentModel\IComponent
	 * @throws Error
	 */
	final public function offsetGet($name)
	{
		return $this->getComponent($name, TRUE);
	}



	/**
	 * Does component specified by name exists?
	 * @param  string  component name
	 * @return bool
	 */
	final public function offsetExists($name)
	{
		return $this->getComponent($name, FALSE) !== NULL;
	}



	/**
	 * Removes component from the container.
	 * @param  string  component name
	 * @return void
	 */
	final public function offsetUnset($name)
	{
		$component = $this->getComponent($name, FALSE);
		if ($component !== NULL) {
			$this->removeComponent($component);
		}
	}



	/**
	 * Prevents cloning.
	 */
	final public function __clone()
	{
		throw new Error('Form cloning is not supported yet.');
	}
}
