<?php
namespace Delorius\Behaviors;

use Delorius\Core\Object;

class Behavior extends Object implements IBehavior
{
    private $_enabled = false;
    private $_owner;

    public function events()
    {
        return array();
    }

    /**
     * Attaches the behavior object to the component.
     * @param Object $object the component that this behavior is to be attached to.
     */
    public function attach(Object $object)
    {
        $this->_enabled = true;
        $this->_owner = $object;
        $this->_attachEventHandlers();
    }

    /**
     * Detaches the behavior object from the component.
     * @param Object $object the component that this behavior is to be detached from.
     */
    public function detach(Object $object)
    {
        $this->_owner = null;
        $this->_enabled = false;
    }

    /**
     * @return boolean whether this behavior is enabled
     */
    public function getEnabled()
    {
        return $this->_enabled;
    }

    /**
     * @param boolean $value whether this behavior is enabled
     */
    public function setEnabled($value)
    {
        $this->_enabled = $value;
    }

    /**
     * @return Object the owner component that this behavior is attached to.
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    private function _attachEventHandlers()
    {
        $class=new \ReflectionClass($this);
        foreach($this->events() as $event=>$handler)
        {
            if($class->getMethod($handler)->isPublic())
                $this->_owner->{$event}[] = callback($this,$handler);
        }
    }
}