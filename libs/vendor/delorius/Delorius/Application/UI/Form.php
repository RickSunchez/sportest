<?php
namespace Delorius\Application\UI;

use Delorius\Application\SignalReceiver;
use Delorius\Exception\Error;
use Delorius\Forms\Rendering\BootstrapFormRenderer;
use Delorius\Utils\Arrays;

class Form extends \Delorius\Forms\Form implements ISignalReceiver {

    function  __construct(\Delorius\ComponentModel\IContainer $parent = NULL, $name = NULL){
        parent::__construct();
        $this->monitor('Delorius\Application\UI\Controller');
        if ($parent !== NULL) {
            $parent->addComponent($this, $name);
        }
    }

    /**
     * @param string $form_id
     * @param \Delorius\Forms\IFormRenderer|null $FormRenderer
     * @return Form
     */
    public static function create($form_id = 'form', \Delorius\Forms\IFormRenderer $FormRenderer = null ){

        $form = new Form(null,$form_id);
        if($FormRenderer == null){
            $FormRenderer = new BootstrapFormRenderer();
        }
        if($FormRenderer instanceof \Delorius\Forms\IFormRenderer)
            $form->setRenderer($FormRenderer);

        return $form;
    }

    /**
     * This method will be called when the component (or component's parent)
     * becomes attached to a monitored object. Do not call this method yourself.
     * @param  \Delorius\ComponentModel\IComponent
     * @return void
     */
    protected function attached($controller)
    {
        if ($controller instanceof Controller) {
            $name = $this->lookupPath('Delorius\Application\UI\Controller');
            if (!isset($this->getElementPrototype()->id)) {
                $this->getElementPrototype()->id = 'df-frm-' . $name;
            }
            $this->addHidden(SignalReceiver::SIGNAL_KEY,$name.'-submit');
            if (iterator_count($this->getControls()) && $this->isSubmitted()) {
                foreach ($this->getControls() as $control) {
                    if (!$control->isDisabled()) {
                        $control->loadHttpData();
                    }
                }
            }
        }
        parent::attached($controller);
    }

    /********************* interface ISignalReceiver ****************d*g**/

    /**
     * This method is called by presenter.
     * @param  string
     * @return void
     */
    public function signalReceived($signal)
    {
        if ($signal === 'submit') {
                $this->fireEvents();
        } else {
            $class = get_class($this);
            throw new Error("Missing handler for signal '$signal' in $class.");
        }
    }
}