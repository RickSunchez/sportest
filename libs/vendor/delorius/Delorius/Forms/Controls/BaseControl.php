<?php
namespace Delorius\Forms\Controls;

use Delorius\ComponentModel\Component;
use Delorius\Forms\Form;
use Delorius\Forms\IControl;
use Delorius\Forms\Rule;
use Delorius\Forms\Rules;
use Delorius\Utils\Arrays;
use Delorius\Utils\Callback;
use Delorius\View\Html;

/**
 * Base class that implements the basic functionality common to form controls.
 *
 *
 *
 * @property-read \Delorius\Forms\Form $form
 * @property-read string $htmlName
 * @property   string $htmlId
 * @property-read array $options
 * @property   ITranslator|NULL $translator
 * @property   mixed $value
 * @property-read bool $filled
 * @property-write $defaultValue
 * @property   bool $disabled
 * @property-read \Delorius\View\Html $control
 * @property-read \Delorius\View\Html $label
 * @property-read \Delorius\View\Html $controlPrototype
 * @property-read \Delorius\View\Html $labelPrototype
 * @property-read \Delorius\Forms\Rules $rules
 * @property   bool $required
 * @property-read array $errors
 */
abstract class BaseControl extends Component implements IControl
{
	/** @var string */
	public static $idMask = 'df-frm-%s-%s';

	/** @var string textual caption or label */
	public $caption;

	/** @var mixed unfiltered control value */
	protected $value;

	/** @var \Delorius\View\Html  control element template */
	protected $control;

	/** @var \Delorius\View\Html  label element template */
	protected $label;

	/** @var array */
	private $errors = array();

	/** @var bool */
	private $disabled = FALSE;

	/** @var string */
	private $htmlId;

	/** @var string */
	private $htmlName;

	/** @var \Delorius\Forms\Rules */
	private $rules;

	/** @var ITranslator */
	private $translator = TRUE; // means autodetect

	/** @var array user options */
	private $options = array();
    /** @var bool name-name2 = name[name2] */
    private $_is_separator = true;

	/**
	 * @param  string  caption
	 */
	public function __construct($caption = NULL)
	{
		$this->monitor('Delorius\Forms\Form');
		parent::__construct();
		$this->control = Html::el('input');
		$this->label = Html::el('label');
		$this->caption = $caption;
		$this->rules = new Rules($this);
	}



	/**
	 * This method will be called when the component becomes attached to Form.
	 * @param  \Delorius\ComponentModel\IComponent
	 * @return void
	 */
	protected function attached($form)
	{
		if (!$this->disabled && $form instanceof Form && $form->isAnchored() && $form->isSubmitted()) {
			$this->htmlName = NULL;
			$this->loadHttpData();
		}
	}



	/**
	 * Returns form.
	 * @param  bool   throw exception if form doesn't exist?
	 * @return \Delorius\Forms\Form
	 */
	public function getForm($need = TRUE)
	{
		return $this->lookup('Delorius\Forms\Form', $need);
	}


    /**
     * @return $this
     */
    public function no_separator(){
        $this->_is_separator = false;
        return $this;
    }

	/**
	 * Returns HTML name of control.
	 * @return string
	 */
	public function getHtmlName()
	{
		if ($this->htmlName === NULL) {
            if($this->_is_separator){
                $name = str_replace(self::NAME_SEPARATOR, '][', $this->lookupPath('Delorius\Forms\Form'), $count);
                if ($count) {
                    $name = substr_replace($name, '', strpos($name, ']'), 1) . ']';
                }
            }else{
                $name = $this->lookupPath('Delorius\Forms\Form');
            }
			if (is_numeric($name) || in_array($name, array('attributes','children','elements','focus','length','reset','style','submit','onsubmit'))) {
			    $name .= '_';
			}
			$this->htmlName = $name;
		}
		return $this->htmlName;
	}



	/**
	 * Changes control's HTML id.
	 * @param  string new ID, or FALSE or NULL
	 * @return BaseControl  provides a fluent interface
	 */
	public function setHtmlId($id)
	{
		$this->htmlId = $id;
		return $this;
	}



	/**
	 * Returns control's HTML id.
	 * @return string
	 */
	public function getHtmlId()
	{
		if ($this->htmlId === FALSE) {
			return NULL;

		} elseif ($this->htmlId === NULL) {
			$this->htmlId = sprintf(self::$idMask, $this->getForm()->getName(), $this->lookupPath('Delorius\Forms\Form'));
		}
		return $this->htmlId;
	}



	/**
	 * Changes control's HTML attribute.
	 * @param  string name
	 * @param  mixed  value
	 * @return BaseControl  provides a fluent interface
	 */
	public function setAttribute($name, $value = TRUE)
	{
		$this->control->$name = $value;
		return $this;
	}



	/**
	 * Sets user-specific option.
	 * Options recognized by DefaultFormRenderer
	 * - 'description' - textual or Html object description
	 *
	 * @param  string key
	 * @param  mixed  value
	 * @return BaseControl  provides a fluent interface
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



	/********************* translator ****************d*g**/



	/**
	 * Sets translate adapter.
	 * @return BaseControl  provides a fluent interface
	 */
	public function setTranslator( $translator = NULL)
	{
		$this->translator = $translator;
		return $this;
	}



	/**
	 * Returns translate adapter.
	 * @return ITranslator|NULL
	 */
	final public function getTranslator()
	{
		if ($this->translator === TRUE) {
			return $this->getForm(FALSE) ? $this->getForm()->getTranslator() : NULL;
		}
		return $this->translator;
	}



	/**
	 * Returns translated string.
	 * @param  string
	 * @param  int      plural count
	 * @return string
	 */
	public function translate($s, $count = NULL)
	{
		$translator = $this->getTranslator();
		return $translator === NULL || $s == NULL ? $s : $translator->translate($s, $count); // intentionally ==
	}



	/********************* interface IFormControl ****************d*g**/



	/**
	 * Sets control's value.
	 * @return BaseControl  provides a fluent interface
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}



	/**
	 * Returns control's value.
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}



	/**
	 * Is control filled?
	 * @return bool
	 */
	public function isFilled()
	{
		return (string) $this->getValue() !== ''; // NULL, FALSE, '' ==> FALSE
	}



	/**
	 * Sets control's default value.
	 * @return BaseControl  provides a fluent interface
	 */
	public function setDefaultValue($value)
	{
		$form = $this->getForm(FALSE);
		if (!$form || !$form->isAnchored() || !$form->isSubmitted()) {
			$this->setValue($value);
		}
		return $this;
	}



	/**
	 * Loads HTTP data.
	 * @return void
	 */
	public function loadHttpData()
	{
		$path = explode('[', strtr(str_replace(array('[]', ']'), '', $this->getHtmlName()), '.', '_'));
		$this->setValue(Arrays::get($this->getForm()->getHttpData(), $path, NULL));
	}



	/**
	 * Disables or enables control.
	 * @param  bool
	 * @return BaseControl  provides a fluent interface
	 */
	public function setDisabled($value = TRUE)
	{
		$this->disabled = (bool) $value;
		return $this;
	}



	/**
	 * Is control disabled?
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}



	/********************* rendering ****************d*g**/



	/**
	 * Generates control's HTML element.
	 * @return \Delorius\View\Html
	 */
	public function getControl()
	{
		$this->setOption('rendered', TRUE);

		$control = clone $this->control;
		$control->name = $this->getHtmlName();
		$control->disabled = $this->disabled;
		$control->id = $this->getHtmlId();
		$control->required = $this->isRequired();

		$rules = self::exportRules($this->rules);
		$rules = substr(PHP_VERSION_ID >= 50400 ? json_encode($rules, JSON_UNESCAPED_UNICODE) : json_encode($rules), 1, -1);
		$rules = preg_replace('#"([a-z0-9_]+)":#i', '$1:', $rules);
		$rules = preg_replace('#(?<!\\\\)"(?!:[^a-z])([^\\\\\',]*)"#i', "'$1'", $rules);
		$control->data('delorius-rules', $rules ? $rules : NULL);

		return $control;
	}



	/**
	 * Generates label's HTML element.
	 * @param  string
	 * @return \Delorius\View\Html
	 */
	public function getLabel($caption = NULL)
	{
		$label = clone $this->label;
		$label->for = $this->getHtmlId();
		if ($caption !== NULL) {
			$label->setText($this->translate($caption));

		} elseif ($this->caption instanceof Html) {
			$label->add($this->caption);

		} else {
			$label->setText($this->translate($this->caption));
		}
		return $label;
	}



	/**
	 * Returns control's HTML element template.
	 * @return \Delorius\View\Html
	 */
	final public function getControlPrototype()
	{
		return $this->control;
	}



	/**
	 * Returns label's HTML element template.
	 * @return \Delorius\View\Html
	 */
	final public function getLabelPrototype()
	{
		return $this->label;
	}



	/********************* rules ****************d*g**/



	/**
	 * Adds a validation rule.
	 * @param  mixed      rule type
	 * @param  string     message to display for invalid data
	 * @param  mixed      optional rule arguments
	 * @return BaseControl  provides a fluent interface
	 */
	public function addRule($operation, $message = NULL, $arg = NULL)
	{
		$this->rules->addRule($operation, $message, $arg);
		return $this;
	}



	/**
	 * Adds a validation condition a returns new branch.
	 * @param  mixed     condition type
	 * @param  mixed     optional condition arguments
	 * @return \Delorius\Forms\Rules      new branch
	 */
	public function addCondition($operation, $value = NULL)
	{
		return $this->rules->addCondition($operation, $value);
	}



	/**
	 * Adds a validation condition based on another control a returns new branch.
	 * @param  \Delorius\Forms\IControl form control
	 * @param  mixed      condition type
	 * @param  mixed      optional condition arguments
	 * @return \Delorius\Forms\Rules      new branch
	 */
	public function addConditionOn(IControl $control, $operation, $value = NULL)
	{
		return $this->rules->addConditionOn($control, $operation, $value);
	}



	/**
	 * @return \Delorius\Forms\Rules
	 */
	final public function getRules()
	{
		return $this->rules;
	}



	/**
	 * Makes control mandatory.
	 * @param  string  error message
	 * @return BaseControl  provides a fluent interface
	 */
	final public function setRequired($message = NULL)
	{
		return $this->addRule(Form::FILLED, $message);
	}



	/**
	 * Is control mandatory?
	 * @return bool
	 */
	final public function isRequired()
	{
		foreach ($this->rules as $rule) {
			if ($rule->type === Rule::VALIDATOR && !$rule->isNegative && $rule->operation === Form::FILLED) {
				return TRUE;
			}
		}
		return FALSE;
	}



	/**
	 * @return array
	 */
	protected static function exportRules($rules)
	{
		$payload = array();
		foreach ($rules as $rule) {
			if (!is_string($op = $rule->operation)) {
				$op = new Callback($op);
				if (!$op->isStatic()) {
					continue;
				}
			}
			if ($rule->type === Rule::VALIDATOR) {
				$item = array('op' => ($rule->isNegative ? '~' : '') . $op, 'msg' => $rules->formatMessage($rule, FALSE));

			} elseif ($rule->type === Rule::CONDITION) {
				$item = array(
					'op' => ($rule->isNegative ? '~' : '') . $op,
					'rules' => self::exportRules($rule->subRules),
					'control' => $rule->control->getHtmlName()
				);
				if ($rule->subRules->getToggles()) {
					$item['toggle'] = $rule->subRules->getToggles();
				}
			}

			if (is_array($rule->arg)) {
				foreach ($rule->arg as $key => $value) {
					$item['arg'][$key] = $value instanceof IControl ? (object) array('control' => $value->getHtmlName()) : $value;
				}
			} elseif ($rule->arg !== NULL) {
				$item['arg'] = $rule->arg instanceof IControl ? (object) array('control' => $rule->arg->getHtmlName()) : $rule->arg;
			}

			$payload[] = $item;
		}
		return $payload;
	}



	/********************* validation ****************d*g**/



	/**
	 * Equal validator: are control's value and second parameter equal?
	 * @return bool
	 */
	public static function validateEqual(IControl $control, $arg)
	{
		$value = $control->getValue();
		foreach ((is_array($value) ? $value : array($value)) as $val) {
			foreach ((is_array($arg) ? $arg : array($arg)) as $item) {
				if ((string) $val === (string) ($item instanceof IControl ? $item->value : $item)) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}



	/**
	 * Filled validator: is control filled?
	 * @return bool
	 */
	public static function validateFilled(\Delorius\Forms\IControl $control)
	{
		return $control->isFilled();
	}



	/**
	 * Valid validator: is control valid?
	 * @return bool
	 */
	public static function validateValid(\Delorius\Forms\IControl $control)
	{
		return $control->rules->validate(TRUE);
	}



	/**
	 * Adds error message to the list.
	 * @param  string  error message
	 * @return void
	 */
	public function addError($message)
	{
		if (!in_array($message, $this->errors, TRUE)) {
			$this->errors[] = $message;
		}
		$this->getForm()->addError($message);
	}



	/**
	 * Returns errors corresponding to control.
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}



	/**
	 * @return bool
	 */
	public function hasErrors()
	{
		return (bool) $this->errors;
	}



	/**
	 * @return void
	 */
	public function cleanErrors()
	{
		$this->errors = array();
	}

}
