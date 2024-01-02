<?php
namespace Delorius\Behaviors;

use Delorius\Core\Object;

interface IBehavior {

    /**
     * Attaches the behavior object to the component.
     * @param Object $object the component that this behavior is to be attached to.
     */
    public function attach(Object $object);
    /**
     * Detaches the behavior object from the component.
     * @param Object $object the component that this behavior is to be detached from.
     */
    public function detach(Object $object);
    /**
     * @return boolean whether this behavior is enabled
     */
    public function getEnabled();
    /**
     * @param boolean $value whether this behavior is enabled
     */
    public function setEnabled($value);

} 