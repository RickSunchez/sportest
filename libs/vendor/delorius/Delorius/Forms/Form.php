<?php
namespace Delorius\Forms;

use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Delorius\Forms\Controls\HiddenField;
use Delorius\Forms\Rendering\DefaultFormRenderer;

/**
 * Creates, validates and renders HTML forms.
 *
 *
 *
 * @property   mixed $action
 * @property   string $method
 * @property-read array $groups
 * @property   ITranslator|NULL $translator
 * @property-read bool $anchored
 * @property-read ISubmitterControl|FALSE $submitted
 * @property-read bool $success
 * @property-read array $httpData
 * @property-read array $errors
 * @property-read \Delorius\View\Html $elementPrototype
 * @property   IFormRenderer $renderer
 */
class Form extends Container
{
    /** validator */
    const EQUAL = ':equal',
        IS_IN = ':equal',
        FILLED = ':filled',
        VALID = ':valid';

    // CSRF protection
    const PROTECTION = 'Delorius\Forms\Controls\HiddenField::validateEqual';

    // button
    const SUBMITTED = ':submitted';

    // text
    const MIN_LENGTH = ':minLength',
        MAX_LENGTH = ':maxLength',
        LENGTH = ':length',
        EMAIL = ':email',
        URL = ':url',
        REGEXP = ':regexp',
        PATTERN = ':pattern',
        INTEGER = ':integer',
        NUMERIC = ':integer',
        FLOAT = ':float',
        RANGE = ':range';

    // multiselect
    const COUNT = ':length';

    // file upload
    const MAX_FILE_SIZE = ':fileSize',
        MIME_TYPE = ':mimeType',
        IMAGE = ':image';

    /** method */
    const GET = 'get',
        POST = 'post';

    /** @internal tracker ID */
    const TRACKER_ID = '_form_';

    /** @internal protection token ID */
    const PROTECTOR_ID = '_token_';

    /** @var array of function(Form $sender); Occurs when the form is submitted and successfully validated */
    public $onSuccess;

    /** @var array of function(Form $sender); Occurs when the form is submitted and is not valid */
    public $onError;

    /** @var array of function(Form $sender); Occurs when the form is submitted */
    public $onSubmit;

    /** @deprecated */
    public $onInvalidSubmit;

    /** @var mixed or NULL meaning: not detected yet */
    private $submittedBy;

    /** @var array */
    private $httpData;

    /** @var Html  <form> element */
    private $element;

    /** @var IFormRenderer */
    private $renderer;

    /** @var ITranslator */
    private $translator;

    /** @var ControlGroup[] */
    private $groups = array();

    /** @var array */
    private $errors = array();

    /** @var string */
    protected $success;

    /**
     * Form constructor.
     * @param  string
     */
    public function __construct($name = NULL)
    {
        $this->element = Html::el('form');
        $this->element->action = ''; // RFC 1808 -> empty uri means 'this'
        $this->element->method = self::POST;
        $this->element->id = $name === NULL ? NULL : 'df-frm-' . $name;

        $this->monitor(__CLASS__);
        if ($name !== NULL) {
            $tracker = new HiddenField($name);
            $tracker->unmonitor(__CLASS__);
            $this[self::TRACKER_ID] = $tracker;
        }
        parent::__construct(NULL, $name);
    }


    /**
     * This method will be called when the component (or component's parent)
     * becomes attached to a monitored object. Do not call this method yourself.
     * @param  \Delorius\ComponentModel\IComponent
     * @return void
     */
    protected function attached($obj)
    {
        if ($obj instanceof self) {
            throw new Error('Nested forms are forbidden.');
        }
    }


    /**
     * Returns self.
     * @return Form
     */
    final public function getForm($need = TRUE)
    {
        return $this;
    }


    /**
     * Sets form's action.
     * @param  mixed URI
     * @return Form  provides a fluent interface
     */
    public function setAction($url)
    {
        $this->element->action = $url;
        return $this;
    }


    /**
     * Returns form's action.
     * @return mixed URI
     */
    public function getAction()
    {
        return $this->element->action;
    }


    /**
     * Sets form's method.
     * @param  string get | post
     * @return Form  provides a fluent interface
     */
    public function setMethod($method)
    {
        if ($this->httpData !== NULL) {
            throw new Error(__METHOD__ . '() must be called until the form is empty.');
        }
        $this->element->method = strtolower($method);
        return $this;
    }


    /**
     * Returns form's method.
     * @return string get | post
     */
    public function getMethod()
    {
        return $this->element->method;
    }


    /**
     * Cross-Site Request Forgery (CSRF) form protection.
     * @param  string
     * @param  int
     * @return void
     */
    public function addProtection($message = NULL, $timeout = NULL)
    {
        $session = $this->getSession()->getSection('Delorius.Forms.Form/CSRF');
        $key = "key$timeout";
        if (isset($session->$key)) {
            $token = $session->$key;
        } else {
            $session->$key = $token = Strings::random();
        }
        $session->setExpiration($timeout, $key);
        $this[self::PROTECTOR_ID] = new HiddenField($token);
        $this[self::PROTECTOR_ID]->addRule(self::PROTECTION, $message, $token);
    }


    /**
     * Adds fieldset group to the form.
     * @param  string  caption
     * @param  bool    set this group as current
     * @return ControlGroup
     */
    public function addGroup($caption = NULL, $setAsCurrent = TRUE)
    {
        $group = new ControlGroup;
        $group->setOption('label', $caption);
        $group->setOption('visual', TRUE);

        if ($setAsCurrent) {
            $this->setCurrentGroup($group);
        }

        if (isset($this->groups[$caption])) {
            return $this->groups[] = $group;
        } else {
            return $this->groups[$caption] = $group;
        }
    }


    /**
     * Removes fieldset group from form.
     * @param  string|FormGroup
     * @return void
     */
    public function removeGroup($name)
    {
        if (is_string($name) && isset($this->groups[$name])) {
            $group = $this->groups[$name];

        } elseif ($name instanceof ControlGroup && in_array($name, $this->groups, TRUE)) {
            $group = $name;
            $name = array_search($group, $this->groups, TRUE);

        } else {
            throw new Error("Group not found in form '$this->name'");
        }

        foreach ($group->getControls() as $control) {
            $control->getParent()->removeComponent($control);
        }

        unset($this->groups[$name]);
    }


    /**
     * Returns all defined groups.
     * @return FormGroup[]
     */
    public function getGroups()
    {
        return $this->groups;
    }


    /**
     * Returns the specified group.
     * @param  string  name
     * @return ControlGroup
     */
    public function getGroup($name)
    {
        return isset($this->groups[$name]) ? $this->groups[$name] : NULL;
    }



    /********************* translator *****************/


    /**
     * Sets translate adapter.
     * @return Form  provides a fluent interface
     */
    public function setTranslator($translator = NULL)
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
        return $this->translator;
    }



    /********************* submission ****************d*g**/


    /**
     * Tells if the form is anchored.
     * @return bool
     */
    public function isAnchored()
    {
        return TRUE;
    }


    /**
     * Tells if the form was submitted.
     * @return ISubmitterControl|FALSE  submittor control
     */
    final public function isSubmitted()
    {
        if ($this->submittedBy === NULL && count($this->getControls())) {
            $this->submittedBy = (bool)$this->getHttpData();
        }
        return $this->submittedBy;
    }


    /**
     * Tells if the form was submitted and successfully validated.
     * @return bool
     */
    final public function isSuccess()
    {
        return $this->isSubmitted() && $this->isValid();
    }


    /**
     * Sets the submittor control.
     * @return Form  provides a fluent interface
     */
    public function setSubmittedBy(ISubmitterControl $by = NULL)
    {
        $this->submittedBy = $by === NULL ? FALSE : $by;
        return $this;
    }


    /**
     * Returns submitted HTTP data.
     * @return array
     */
    final public function getHttpData()
    {
        if ($this->httpData === NULL) {
            if (!$this->isAnchored()) {
                throw new Error('Form is not anchored and therefore can not determine whether it was submitted.');
            }
            $this->httpData = $this->receiveHttpData();
        }
        return $this->httpData;
    }


    /**
     * Fires submit/click events.
     * @return void
     */
    public function fireEvents()
    {
        if (!$this->isSubmitted()) {
            return;

        } elseif ($this->submittedBy instanceof ISubmitterControl) {
            if (!$this->submittedBy->getValidationScope() || $this->isValid()) {
                $this->submittedBy->click();
                $valid = TRUE;
            } else {
                $this->submittedBy->onInvalidClick($this->submittedBy);
            }
        }

        if (isset($valid) || $this->isValid()) {
            $this->onSuccess($this);
        } else {
            $this->onError($this);
        }

        $this->onSubmit($this);
    }


    /**
     * Internal: receives submitted HTTP data.
     * @return array
     */
    protected function receiveHttpData()
    {
        $request = $this->getRequest();
        if (strcasecmp($this->getMethod(), $request->getMethod())) {
            return array();
        }

        if ($request->isMethod('post')) {
            $data = Arrays::mergeTree($request->getPost(), $request->getFiles());
        } else {
            $data = $request->getQuery();
        }

        if ($tracker = $this->getComponent(self::TRACKER_ID, FALSE)) {
            if (!isset($data[self::TRACKER_ID]) || $data[self::TRACKER_ID] !== $tracker->getValue()) {
                return array();
            }
        }

        return $data;
    }



    /********************* data exchange ****************d*g**/


    /**
     * Returns the values submitted by the form.
     * @return \Delorius\Utils\ArrayHash|array
     */
    public function getValues($asArray = FALSE)
    {
        $values = parent::getValues($asArray);
        unset($values[self::TRACKER_ID], $values[self::PROTECTOR_ID]);
        return $values;
    }



    /********************* validation ****************d*g**/


    /**
     * Adds error message to the list.
     * @param  string  error message
     * @return void
     */
    public function addError($message)
    {
        $this->valid = $this->success = FALSE;
        if ($message !== NULL && !in_array($message, $this->errors, TRUE)) {
            $this->errors[] = $message;
        }
    }

    public function addErrors(array $errors)
    {
        foreach ($errors as $key => $message) {
            $this->addError($message);
        }
    }


    /**
     * Returns validation errors.
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
        return (bool)$this->getErrors();
    }

    /**
     * Set success message
     * @param  string $message
     * @return void
     */
    public function setSuccess($message)
    {
        $this->valid = TRUE;
        $this->errors = array();
        if ($message !== NULL && $message !== $this->success) {
            $this->success = $message;
        }
    }


    /**
     * Returns message success.
     * @return string
     */
    public function getSuccess()
    {
        return $this->success;
    }


    /**
     * @return bool
     */
    public function hasSuccess()
    {
        return (bool)!empty($this->success);
    }


    /**
     * @return void
     */
    public function cleanErrors()
    {
        $this->errors = array();
        $this->valid = NULL;
    }



    /********************* rendering ****************d*g**/


    /**
     * Returns form's HTML element template.
     * @return \Delorius\View\Html
     */
    public function getElementPrototype()
    {
        return $this->element;
    }


    /**
     * Sets form renderer.
     * @return Form  provides a fluent interface
     */
    public function setRenderer(IFormRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }


    /**
     * Returns form renderer.
     * @return IFormRenderer
     */
    final public function getRenderer()
    {
        if ($this->renderer === NULL) {
            $this->renderer = new DefaultFormRenderer;
        }
        return $this->renderer;
    }


    /**
     * Renders form.
     * @return void
     */
    public function render()
    {
        $args = func_get_args();
        array_unshift($args, $this);
        echo call_user_func_array(array($this->getRenderer(), 'render'), $args);
    }


    /**
     * Renders form to string.
     * @return bool  can throw exceptions? (hidden parameter)
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getRenderer()->render($this);

        } catch (\Exception $e) {
            if (func_get_args() && func_get_arg(0)) {
                throw $e;
            } else {
                trigger_error("Exception in " . __METHOD__ . "(): {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}", E_USER_ERROR);
            }
        }
    }



    /********************* backend ****************d*g**/


    /**
     * @return \Delorius\Http\Session
     */
    protected function getSession()
    {
        return Environment::getContext()->getService('session');
    }

    /**
     * @return \Delorius\Http\Request
     */
    protected function getRequest()
    {
        return Environment::getContext()->getService('httpRequest');
    }

}
