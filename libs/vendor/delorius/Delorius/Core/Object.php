<?php

namespace Delorius\Core;

use Delorius\Behaviors\IBehavior;
use Delorius\Exception\Error;
use Delorius\Reflection\ClassType;

/**
 * Properties is a syntactic sugar which allows access public getter and setter
 * methods as normal object variables. A property is defined by a getter method
 * or setter method (no setter method means read-only property).
 * <code>
 * $val = $obj->label;     // equivalent to $val = $obj->getLabel();
 * $obj->label = 'Deloris';  // equivalent to $obj->setLabel('Deloris');
 * </code>
 * Property names are case-sensitive, and they are written in the camelCaps
 * or PascalCaps.
 *
 * Event functionality is provided by declaration of property named 'on{Something}'
 * Multiple handlers are allowed.
 * <code>
 * public $onClick;                // declaration in class
 * $this->onClick[] = 'callback';  // attaching event handler
 * if (!empty($this->onClick)) ... // are there any handlers?
 * $this->onClick($sender, $arg);  // raises the event with arguments
 * </code>
 *
 * Adding method to class (i.e. to all instances) works similar to JavaScript
 * prototype property. The syntax for adding a new method is:
 * <code>
 * MyClass::extensionMethod('newMethod', function(MyClass $obj, $arg, ...) { ... });
 * $obj = new MyClass;
 * $obj->newMethod($x);
 * </code>
 *
 *
 * @method: methods that we hate to write
 * <code>
 * [@]method setName(string)
 * [@]method string getName()
 * [@]method setEnabled(bool)
 * [@]method bool isEnabled()
 * class MyClass extends \Delorius\Core\Object{
 *   / @var string /
 * public $name;
 *
 * / @var bool /
 * public $enabled;
 * }
 * </code>
 *
 *
 * @property-read \Delorius\Reflection\ClassType $reflection
 */
abstract class Object
{

    /** @var  array(\Delorius\Behaviors\IBehavior) */
    private $_m;

    /**
     * Access to reflection.
     * @return \Delorius\Reflection\ClassType
     */
    public static function getReflection()
    {
        return new ClassType(get_called_class());
    }


    /**
     * Call to undefined method.
     * @param  string $name method
     * @param  array $args arguments
     * @return mixed
     * @throws Error
     */
    public function __call($name, $args)
    {
        // behavior->methodName()
        if ($this->_m !== null) {
            foreach ($this->_m as $object) {
                if ($object->getEnabled() && method_exists($object, $name))
                    return call_user_func_array(array($object, $name), $args);
            }
        }

        return ObjectMixin::call($this, $name, $args);
    }


    /**
     * Call to undefined static method.
     * @param  string $name method  (in lower case!)
     * @param  array $args arguments
     * @return mixed
     * @throws Error
     */
    public static function __callStatic($name, $args)
    {
        ObjectMixin::callStatic(get_called_class(), $name, $args);
    }


    /**
     * Adding method to class.
     * @param  string $name method
     * @param  callable
     * @return mixed
     */
    public static function extensionMethod($name, $callback = NULL)
    {
        if (strpos($name, '::') === FALSE) {
            $class = get_called_class();
        } else {
            list($class, $name) = explode('::', $name);
            $rc = new \ReflectionClass($class);
            $class = $rc->getName();
        }
        if ($callback === NULL) {
            return ObjectMixin::getExtensionMethod($class, $name);
        } else {
            ObjectMixin::setExtensionMethod($class, $name, $callback);
        }
    }


    /**
     * Returns property value. Do not call directly.
     * @param  string $name property
     * @return mixed   property value
     * @throws Error if the property is not defined.
     */
    public function &__get($name)
    {
        // behavior->propertyName;
        if (is_array($this->_m)) {
            foreach ($this->_m as $object) {
                if (
                    $object->getEnabled() &&
                    (
                        property_exists($object, $name)
                        ||
                        method_exists($object, $name)
                    )
                )
                    return $object->$name;
            }
        }

        return ObjectMixin::get($this, $name);
    }


    /**
     * Sets value of a property. Do not call directly.
     * @param  string $name property
     * @param  mixed $value property
     * @return void
     * @throws Error if the property is not defined or is read-only
     */
    public function __set($name, $value)
    {
        // behavior->propertyName=value or behavior->methodName(value) ;
        if (is_array($this->_m)) {
            foreach ($this->_m as $object) {
                if ($object->getEnabled()) {
                    if (property_exists($object, $name)) {
                        return $object->$name = $value;
                    } else if (method_exists($object, $name)) {
                        return $object->$name($value);
                    }
                }
            }
        }


        ObjectMixin::set($this, $name, $value);
    }


    /**
     * Is property defined?
     * @param  string $name property name
     * @return bool
     */
    public function __isset($name)
    {
        // isset(behavior->propertyName)
        if (is_array($this->_m)) {
            if (isset($this->_m[$name]))
                return true;
            foreach ($this->_m as $object) {
                if ($object->getEnabled() && (property_exists($object, $name) || method_exists($object, $name)))
                    return $object->$name !== null;
            }
        }


        return ObjectMixin::has($this, $name);
    }


    /**
     * Access to undeclared property.
     * @param  string $name property name
     * @return void
     * @throws Error
     */
    public function __unset($name)
    {
        // unset(behavior->propertyName)
        if (is_array($this->_m)) {
            if (isset($this->_m[$name])) {
                return $this->detachBehavior($name);
            } else {
                foreach ($this->_m as $object) {
                    if ($object->getEnabled()) {
                        if (property_exists($object, $name))
                            return $object->$name = null;
                        elseif (method_exists($object, $name))
                            return $object->$name(null);
                    }
                }
            }
        }

        ObjectMixin::remove($this, $name);
    }


    /*************** Behaviors *********************/

    /**
     * Returns isset named behavior object.
     * @param $behavior
     * @return bool
     */
    public function hasBehaviors($behavior)
    {
        return isset($this->_m[$behavior]);
    }

    /**
     * Returns the named behavior object.
     * The name 'asa' stands for 'as a'.
     * @param string $behavior the behavior name
     * @return IBehavior the behavior object, or null if the behavior does not exist
     */
    public function asa($behavior)
    {
        return isset($this->_m[$behavior]) ? $this->_m[$behavior] : null;
    }

    /**
     * Attaches a list of behaviors to the object.
     * Each behavior is indexed by its name and should be an instance of
     * {@link IBehavior}, a string specifying the behavior class, or an
     * array of the following structure:
     * <pre>
     * array(
     *     'class'=>'\Path\To\BehaviorClass',
     *     'property1'=>'value1',
     *     'property2'=>'value2',
     * )
     * </pre>
     * @param array $behaviors list of behaviors to be attached to the object
     */
    public function attachBehaviors($behaviors)
    {
        foreach ($behaviors as $name => $behavior)
            $this->attachBehavior($name, $behavior);
    }

    /**
     * Detaches all behaviors from the object.
     */
    public function detachBehaviors()
    {
        if ($this->_m !== null) {
            foreach ($this->_m as $name => $behavior)
                $this->detachBehavior($name);
            $this->_m = null;
        }
    }

    /**
     * Attaches a behavior to this object.
     * This method will create the behavior object based on the given
     * configuration. After that, the behavior object will be initialized
     * by calling its {@link IBehavior::attach} method.
     * @param string $name the behavior's name. It should uniquely identify this behavior.
     * @param mixed $behavior the behavior configuration. This is passed as the first
     * parameter to {@link Container::createInstance} to create the behavior object.
     * You can also pass an already created behavior instance (the new behavior will replace an already created
     * behavior with the same name, if it exists).
     * @return IBehavior the behavior object
     */
    public function attachBehavior($name, $behavior)
    {
        if (!($behavior instanceof IBehavior)) {
            $class = '';
            $args = array();
            if (is_string($behavior)) {
                $class = $behavior;
            } elseif (isset($behavior['class'])) {
                $class = $behavior['class'];
                unset($behavior['class']);
                $args = $behavior;
            } else {
                throw new Error('Object configuration must be an array containing a "class" element.');
            }
            $behavior = Environment::getContext()->createInstance($class, $args);
        }
        $behavior->setEnabled(true);
        $behavior->attach($this);
        return $this->_m[$name] = $behavior;
    }

    /**
     * Detaches a behavior from the object.
     * The behavior's {@link IBehavior::detach} method will be invoked.
     * @param string $name the behavior's name. It uniquely identifies the behavior.
     * @return IBehavior the detached behavior. Null if the behavior does not exist.
     */
    public function detachBehavior($name)
    {
        if (isset($this->_m[$name])) {
            $this->_m[$name]->detach($this);
            $behavior = $this->_m[$name];
            unset($this->_m[$name]);
            return $behavior;
        }
    }

    /**
     * Enables all behaviors attached to this object.
     */
    public function enableBehaviors()
    {
        if ($this->_m !== null) {
            foreach ($this->_m as $behavior)
                $behavior->setEnabled(true);
        }
    }

    /**
     * Disables all behaviors attached to this object.
     */
    public function disableBehaviors()
    {
        if ($this->_m !== null) {
            foreach ($this->_m as $behavior)
                $behavior->setEnabled(false);
        }
    }

    /**
     * Enables an attached behavior.
     * A behavior is only effective when it is enabled.
     * A behavior is enabled when first attached.
     * @param string $name the behavior's name. It uniquely identifies the behavior.
     */
    public function enableBehavior($name)
    {
        if (isset($this->_m[$name]))
            $this->_m[$name]->setEnabled(true);
    }

    /**
     * Disables an attached behavior.
     * A behavior is only effective when it is enabled.
     * @param string $name the behavior's name. It uniquely identifies the behavior.
     */
    public function disableBehavior($name)
    {
        if (isset($this->_m[$name]))
            $this->_m[$name]->setEnabled(false);
    }
}
