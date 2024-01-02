<?php
namespace Delorius\Forms\Rendering;

use Delorius\Exception\Error;
use Delorius\Core\Object;
use Delorius\Forms\IFormRenderer;
use Delorius\View\Html;


/**
 * Converts a Form into the HTML output.
 *
 *
 */
class DefaultFormRenderer extends Object implements IFormRenderer
{
	/**
	 *  /--- form.container
	 *
	 *    /--- if (form.errors) error.container
	 *      .... error.item [.class]
	 *    \---
     *
     *      /--- if (form.success) success.container
     *      .... success.item [.class]
     *    \---
	 *
	 *    /--- hidden.container
	 *      .... HIDDEN CONTROLS
	 *    \---
	 *
	 *    /--- group.container
	 *      .... group.label
	 *      .... group.description
	 *
	 *      /--- controls.container
	 *
	 *        /--- pair.container [.required .optional .odd]
	 *
	 *          /--- label.container
	 *            .... LABEL
	 *            .... label.suffix
	 *            .... label.requiredsuffix
	 *          \---
	 *
	 *          /--- control.container [.odd]
	 *            .... CONTROL [.required .text .password .file .submit .button]
	 *            .... control.requiredsuffix
	 *            .... control.description
	 *            .... if (control.errors) error.container
	 *          \---
	 *        \---
	 *      \---
	 *    \---
	 *  \--
	 *
	 * @var array of HTML tags */
	public $wrappers = array(
		'form' => array(
			'container' => NULL,
			'errors' => TRUE,
            'success' => TRUE,
        ),

        'success' => array(
            'container' => 'div class=info-success',
            'item' => '',
        ),

		'error' => array(
			'container' => 'ul class=error',
			'item' => 'li',
		),

		'group' => array(
			'container' => 'fieldset',
			'label' => 'legend',
			'description' => 'p',
		),

		'controls' => array(
			'container' => 'table',
		),

		'pair' => array(
			'container' => 'tr',
			'.required' => 'required',
			'.optional' => NULL,
			'.odd' => NULL,
		),

		'control' => array(
			'container' => 'td',
			'.odd' => NULL,

			'errors' => FALSE,
			'description' => 'small',
			'requiredsuffix' => '',

			'.required' => 'required',
			'.text' => 'text',
			'.password' => 'text',
			'.file' => 'text',
			'.submit' => 'button',
			'.image' => 'imagebutton',
			'.button' => 'button',
		),

		'label' => array(
			'container' => 'th',
			'suffix' => NULL,
			'requiredsuffix' => '',
		),

		'hidden' => array(
			'container' => 'div',
		),
	);

	/** @var \Delorius\Forms\Form */
	protected $form;

	/** @var int */
	protected $counter;



	/**
	 * Provides complete form rendering.
	 * @param  \Delorius\Forms\Form
	 * @param  string 'begin', 'errors', 'body', 'end' or empty to render all
	 * @return string
	 */
	public function render(\Delorius\Forms\Form $form, $mode = NULL)
	{
		if ($this->form !== $form) {
			$this->form = $form;
			$this->init();
		}

		$s = '';
		if (!$mode || $mode === 'begin') {
			$s .= $this->renderBegin();
		}
		if ((!$mode && $this->getValue('form errors')) || $mode === 'errors') {
			$s .= $this->renderErrors();
		}

        if ((!$mode && $this->getValue('form success')) || $mode === 'success') {
            $s .= $this->renderSuccess();
        }


		if (!$mode || $mode === 'body') {
			$s .= $this->renderBody();
		}
		if (!$mode || $mode === 'end') {
			$s .= $this->renderEnd();
		}
		return $s;
	}



	/** @deprecated */
	public function setClientScript()
	{
		trigger_error(__METHOD__ . '() is deprecated; use unobstructive JavaScript instead.', E_USER_WARNING);
		return $this;
	}



	/**
	 * Initializes form.
	 * @return void
	 */
	protected function init()
	{
		// TODO: only for back compatiblity - remove?
		$wrapper = & $this->wrappers['control'];
		foreach ($this->form->getControls() as $control) {
			if ($control->isRequired() && isset($wrapper['.required'])) {
				$control->getLabelPrototype()->class($wrapper['.required'], TRUE);
			}

			$el = $control->getControlPrototype();
			if ($el->getName() === 'input' && isset($wrapper['.' . $el->type])) {
				$el->class($wrapper['.' . $el->type], TRUE);
			}
		}
	}



	/**
	 * Renders form begin.
	 * @return string
	 */
	public function renderBegin()
	{
		$this->counter = 0;

		foreach ($this->form->getControls() as $control) {
			$control->setOption('rendered', FALSE);
		}

		if (strcasecmp($this->form->getMethod(), 'get') === 0) {
			$el = clone $this->form->getElementPrototype();
			$url = explode('?', (string) $el->action, 2);
			$el->action = $url[0];
			$s = '';
			if (isset($url[1])) {
				foreach (preg_split('#[;&]#', $url[1]) as $param) {
					$parts = explode('=', $param, 2);
					$name = urldecode($parts[0]);
					if (!isset($this->form[$name])) {
						$s .= Html::el('input', array('type' => 'hidden', 'name' => $name, 'value' => urldecode($parts[1])));
					}
				}
				$s = "\n\t" . $this->getWrapper('hidden container')->setHtml($s);
			}
			return $el->startTag() . $s;


		} else {
			return $this->form->getElementPrototype()->startTag();
		}
	}



	/**
	 * Renders form end.
	 * @return string
	 */
	public function renderEnd()
	{
		$s = '';
		foreach ($this->form->getControls() as $control) {
			if ($control instanceof \Delorius\Forms\Controls\HiddenField && !$control->getOption('rendered')) {
				$s .= (string) $control->getControl();
			}
		}
		if (iterator_count($this->form->getComponents(TRUE, 'Deloroius\Forms\Controls\TextInput')) < 2) {
			$s .= '<!--[if IE]><input type=IEbug disabled style="display:none"><![endif]-->';
		}
		if ($s) {
			$s = $this->getWrapper('hidden container')->setHtml($s) . "\n";
		}

		return $s . $this->form->getElementPrototype()->endTag() . "\n";
	}



	/**
	 * Renders validation errors (per form or per control).
	 * @return string
	 */
	public function renderErrors(\Delorius\Forms\IControl $control = NULL)
	{
		$errors = $control === NULL ? $this->form->getErrors() : $control->getErrors();
		if (count($errors)) {
			$ul = $this->getWrapper('error container');
			$li = $this->getWrapper('error item');

			foreach ($errors as $error) {
				$item = clone $li;
				if ($error instanceof Html) {
					$item->add($error);
				} else {
					$item->setText($error);
				}
				$ul->add($item);
			}
			return "\n" . $ul->render(0);
		}
	}

    /**
     * Renders Success message (per form or per control).
     * @return string
     */
    public function renderSuccess(\Delorius\Forms\IControl $control = NULL)
    {
        $success = ($control === NULL) ? $this->form->getSuccess() : $control->getSuccess();
        if ($success) {
            $container = $this->getWrapper('success container');
            $item = $this->getWrapper('success item');
            $inner = clone $item;
            if ($success instanceof Html) {
                $inner->add($success);
            } else {
                $inner->setText($success);
            }
            $container->add($inner);
            return "\n" . $container->render(0);
        }
    }



	/**
	 * Renders form body.
	 * @return string
	 */
	public function renderBody()
	{
		$s = $remains = '';

		$defaultContainer = $this->getWrapper('group container');
		$translator = $this->form->getTranslator();

		foreach ($this->form->getGroups() as $group) {
			if (!$group->getControls() || !$group->getOption('visual')) {
				continue;
			}

			$container = $group->getOption('container', $defaultContainer);
			$container = $container instanceof Html ? clone $container : Html::el($container);

			$s .= "\n" . $container->startTag();

			$text = $group->getOption('label');
			if ($text instanceof Html) {
				$s .= $text;

			} elseif (is_string($text)) {
				if ($translator !== NULL) {
					$text = $translator->translate($text);
				}
				$s .= "\n" . $this->getWrapper('group label')->setText($text) . "\n";
			}

			$text = $group->getOption('description');
			if ($text instanceof Html) {
				$s .= $text;

			} elseif (is_string($text)) {
				if ($translator !== NULL) {
					$text = $translator->translate($text);
				}
				$s .= $this->getWrapper('group description')->setText($text) . "\n";
			}

			$s .= $this->renderControls($group);

			$remains = $container->endTag() . "\n" . $remains;
			if (!$group->getOption('embedNext')) {
				$s .= $remains;
				$remains = '';
			}
		}

		$s .= $remains . $this->renderControls($this->form);

		$container = $this->getWrapper('form container');
		$container->setHtml($s);
		return $container->render(0);
	}



	/**
	 * Renders group of controls.
	 * @param  \Delorius\Forms\Container|FormGroup
	 * @return string
	 */
	public function renderControls($parent)
	{
		if (!($parent instanceof \Delorius\Forms\Container || $parent instanceof \Delorius\Forms\ControlGroup)) {
			throw new Error("Argument must be FormContainer or FormGroup instance.");
		}

		$container = $this->getWrapper('controls container');

		$buttons = NULL;
		foreach ($parent->getControls() as $control) {
			if ($control->getOption('rendered') || $control instanceof \Delorius\Forms\Controls\HiddenField || $control->getForm(FALSE) !== $this->form) {
				// skip

			} elseif ($control instanceof \Delorius\Forms\Controls\Button) {
				$buttons[] = $control;

			} else {
				if ($buttons) {
					$container->add($this->renderPairMulti($buttons));
					$buttons = NULL;
				}
				$container->add($this->renderPair($control));
			}
		}

		if ($buttons) {
			$container->add($this->renderPairMulti($buttons));
		}

		$s = '';
		if (count($container)) {
			$s .= "\n" . $container . "\n";
		}

		return $s;
	}



	/**
	 * Renders single visual row.
	 * @return string
	 */
	public function renderPair(\Delorius\Forms\IControl $control)
	{
		$pair = $this->getWrapper('pair container');
		$pair->add($this->renderLabel($control));
		$pair->add($this->renderControl($control));
		$pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), TRUE);
		$pair->class($control->getOption('class'), TRUE);
		if (++$this->counter % 2) {
			$pair->class($this->getValue('pair .odd'), TRUE);
		}
		$pair->id = $control->getOption('id');
		return $pair->render(0);
	}



	/**
	 * Renders single visual row of multiple controls.
	 * @param  IFormControl[]
	 * @return string
	 */
	public function renderPairMulti(array $controls)
	{
		$s = array();
		foreach ($controls as $control) {
			if (!$control instanceof \Delorius\Forms\IControl) {
				throw new Error("Argument must be array of IFormControl instances.");
			}
			$s[] = (string) $control->getControl();
		}
		$pair = $this->getWrapper('pair container');
		$pair->add($this->renderLabel($control));
		$pair->add($this->getWrapper('control container')->setHtml(implode(" ", $s)));
		return $pair->render(0);
	}



	/**
	 * Renders 'label' part of visual row of controls.
	 * @return string
	 */
	public function renderLabel(\Delorius\Forms\IControl $control)
	{
		$head = $this->getWrapper('label container');

		if ($control instanceof \Delorius\Forms\Controls\Checkbox || $control instanceof \Delorius\Forms\Controls\Button) {
			return $head->setHtml(($head->getName() === 'td' || $head->getName() === 'th') ? '&nbsp;' : '');

		} else {
			$label = $control->getLabel();
			$suffix = $this->getValue('label suffix') . ($control->isRequired() ? $this->getValue('label requiredsuffix') : '');
			if ($label instanceof Html) {
				$label->setHtml($label->getHtml() . $suffix);
				$suffix = '';
			}
			return $head->setHtml((string) $label . $suffix);
		}
	}



	/**
	 * Renders 'control' part of visual row of controls.
	 * @return string
	 */
	public function renderControl(\Delorius\Forms\IControl $control)
	{
		$body = $this->getWrapper('control container');
		if ($this->counter % 2) {
			$body->class($this->getValue('control .odd'), TRUE);
		}

		$description = $control->getOption('description');
		if ($description instanceof Html) {
			$description = ' ' . $control->getOption('description');

		} elseif (is_string($description)) {
			$description = ' ' . $this->getWrapper('control description')->setText($control->translate($description));

		} else {
			$description = '';
		}

		if ($control->isRequired()) {
			$description = $this->getValue('control requiredsuffix') . $description;
		}

		if ($this->getValue('control errors')) {
			$description .= $this->renderErrors($control);
		}

		if ($control instanceof \Delorius\Forms\Controls\Checkbox || $control instanceof \Delorius\Forms\Controls\Button) {
			return $body->setHtml((string) $control->getControl() . (string) $control->getLabel() . $description);

		} else {
			return $body->setHtml((string) $control->getControl() . $description);
		}
	}



	/**
	 * @param  string
	 * @return \Delorius\View\Html
	 */
	protected function getWrapper($name)
	{
		$data = $this->getValue($name);
		return $data instanceof Html ? clone $data : Html::el($data);
	}



	/**
	 * @param  string
	 * @return string
	 */
	protected function getValue($name)
	{
		$name = explode(' ', $name);
		$data = & $this->wrappers[$name[0]][$name[1]];
		return $data;
	}

}
