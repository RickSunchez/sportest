<?php
namespace Delorius\Forms\Rendering;

use Delorius\View\Html;

class BootstrapFormRenderer extends DefaultFormRenderer
{
	 /* @var array of HTML tags */
    public $wrappers = array(
        'form' => array(
            'container' =>'form class=form-horizontal',
            'errors' => TRUE,
            'success' => TRUE,
        ),

        'success' => array(
            'container' => 'div class="info-success test"',
            'item' => 'div',
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
            'container' => 'div',
        ),

        'pair' => array(
            'container' => 'div class=control-group',
            '.required' => 'required',
            '.optional' => NULL,
            '.odd' => NULL,
        ),

        'control' => array(
            'container' => 'div class=controls',
            '.odd' => NULL,

            'errors' => FALSE,
            'description' => 'small',
            'requiredsuffix' => '',

            '.required' => 'required',
            '.text' => 'text',
            '.password' => 'text',
            '.file' => 'text',
            '.submit' => 'btn',
            '.image' => 'imagebutton',
            '.button' => 'btn',
        ),

        'label' => array(
            'container' => '',
            'suffix' => NULL,
            'requiredsuffix' => '',
        ),

        'hidden' => array(
            'container' => 'div',
        ),
    );

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
                $label->addClass('control-label');
                $suffix = '';
            }
            return $head->setHtml((string) $label . $suffix);
        }
    }

}
