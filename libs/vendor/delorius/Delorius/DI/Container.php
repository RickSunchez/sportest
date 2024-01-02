<?php
namespace Delorius\DI;

use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Exception\InvalidArgument;
use Delorius\Exception\InvalidState;
use Delorius\Exception\MissingService;
use Delorius\Exception\ServiceCreation;
use Delorius\Utils\Arrays;

/**
 * The dependency injection container default implementation.
 */
class Container extends Object
{
    const TAGS = 'tags';
    const TYPES = 'types';
    const SERVICES = 'services';
    const ALIASES = 'aliases';

    /** @var array  user parameters */
    /*private*/
    public $parameters = array();

    /** @var object[]  storage for shared objects */
    private $registry = array();

    /** @var array[] */
    protected $meta = array();

    /** @var array circular reference detector */
    private $creating;

    public function __construct(array $params = array())
    {
        $this->parameters = $params;
    }

    /**
     * @param null $name
     * @param null $default
     * @return array|mixed
     * @throws Error
     */
    public function getParameters($name = null, $default = null)
    {
        if ($name == null) {
            return $this->parameters;
        } else {
            return Arrays::get($this->parameters, $name, $default);
        }
    }


    /**
     * Adds the service to the container.
     * @param  string
     * @param  object
     * @return self
     */
    public function addService($name, $service)
    {
        if (!is_string($name) || !$name) {
            throw new InvalidArgument(sprintf('Service name must be a non-empty string, %s given.', gettype($name)));

        }
        $name = isset($this->meta[self::ALIASES][$name]) ? $this->meta[self::ALIASES][$name] : $name;
        if (isset($this->registry[$name])) {
            throw new InvalidState("Service '$name' already exists.");

        } elseif (!is_object($service)) {
            throw new InvalidArgument(sprintf("Service '%s' must be a object, %s given.", $name, gettype($service)));

        } elseif (isset($this->meta[self::SERVICES][$name]) && !$service instanceof $this->meta[self::SERVICES][$name]) {
            throw new InvalidArgument(sprintf("Service '%s' must be instance of %s, %s given.", $name, $this->meta[self::SERVICES][$name], get_class($service)));
        }

        $this->registry[$name] = $service;
        return $this;
    }


    /**
     * Removes the service from the container.
     * @param  string
     * @return void
     */
    public function removeService($name)
    {
        $name = isset($this->meta[self::ALIASES][$name]) ? $this->meta[self::ALIASES][$name] : $name;
        unset($this->registry[$name]);
    }


    /**
     * Gets the service object by name.
     * @param  string
     * @return object
     * @throws MissingService
     */
    public function getService($name)
    {
        if (!isset($this->registry[$name])) {
            if (isset($this->meta[self::ALIASES][$name])) {
                return $this->getService($this->meta[self::ALIASES][$name]);
            }
            $this->registry[$name] = $this->createService($name);
        }
        return $this->registry[$name];
    }

    /**
     * Gets the  clone service object by name.
     * @param  string
     * @return object
     * @throws MissingService
     */
    public function getCloneService($name)
    {
        return clone $this->getService($name);
    }


    /**
     * Does the service exist?
     * @param  string service name
     * @return bool
     */
    public function hasService($name)
    {
        $name = isset($this->meta[self::ALIASES][$name]) ? $this->meta[self::ALIASES][$name] : $name;
        return isset($this->registry[$name])
        || (method_exists($this, $method = self::getMethodName($name))
            && ($rm = new \ReflectionMethod($this, $method)) && $rm->getName() === $method);
    }


    /**
     * Is the service created?
     * @param  string service name
     * @return bool
     */
    public function isCreated($name)
    {
        if (!$this->hasService($name)) {
            throw new MissingService("Service '$name' not found.");
        }
        $name = isset($this->meta[self::ALIASES][$name]) ? $this->meta[self::ALIASES][$name] : $name;
        return isset($this->registry[$name]);
    }


    /**
     * Creates new instance of the service.
     * @param  string service name
     * @return object
     * @throws MissingService
     */
    public function createService($name, array $args = array())
    {
        $name = isset($this->meta[self::ALIASES][$name]) ? $this->meta[self::ALIASES][$name] : $name;
        $method = self::getMethodName($name);
        if (isset($this->creating[$name])) {
            throw new InvalidState(sprintf('Circular reference detected for services: %s.', implode(', ', array_keys($this->creating))));

        } elseif (!method_exists($this, $method) || !($rm = new \ReflectionMethod($this, $method)) || $rm->getName() !== $method) {
            throw new MissingService("Service '$name' not found.");
        }

        $this->creating[$name] = TRUE;
        try {
            $service = call_user_func_array(array($this, $method), $args);
        } catch (\Exception $e) {
            unset($this->creating[$name]);
            throw $e;
        }
        unset($this->creating[$name]);

        if (!is_object($service)) {
            throw new Error("Unable to create service '$name', value returned by method $method() is not object.");
        }

        return $service;
    }


    /**
     * Resolves service by type.
     * @param  string  class or interface
     * @param  bool    throw exception if service doesn't exist?
     * @return object  service or NULL
     * @throws MissingService
     */
    public function getByType($class, $need = TRUE)
    {
        $class = ltrim($class, '\\');
        if (!empty($this->meta[self::TYPES][$class][TRUE])) {
            if (count($names = $this->meta[self::TYPES][$class][TRUE]) === 1) {
                return $this->getService($names[0]);
            }
            throw new MissingService("Multiple services of type $class found: " . implode(', ', $names) . '.');

        } elseif ($need) {
            throw new MissingService("Service of type $class not found.");
        }
    }


    /**
     * Gets the service names of the specified type.
     * @param  string
     * @return string[]
     */
    public function findByType($class)
    {
        $class = ltrim($class, '\\');
        return empty($this->meta[self::TYPES][$class])
            ? array()
            : call_user_func_array('array_merge', $this->meta[self::TYPES][$class]);
    }


    /**
     * Gets the service names of the specified tag.
     * @param  string
     * @return array of [service name => tag attributes]
     */
    public function findByTag($tag)
    {
        return isset($this->meta[self::TAGS][$tag]) ? $this->meta[self::TAGS][$tag] : array();
    }


    /********************* autowiring ****************d*g**/


    /**
     * Creates new instance using autowiring.
     * @param  string  class
     * @param  array   arguments
     * @return object
     * @throws InvalidArgument
     */
    public function createInstance($class, array $args = array())
    {
        $rc = new \ReflectionClass($class);
        if (!$rc->isInstantiable()) {
            throw new ServiceCreation("Class $class is not instantiable.");

        } elseif ($constructor = $rc->getConstructor()) {
            $object = $rc->newInstanceArgs(Helpers::autowireArguments($constructor, $args, $this));

        } elseif ($args) {
            $object = new $class;
            foreach ($args as $name => $value) {
                $object->{$name} = $value;
            }
        }

        if ($object == null) {
            $object = new $class;
        }

        $this->callInjects($object);
        return $object;
    }


    /**
     * Calls all methods starting with with "inject" using autowiring.
     * @param  object
     * @return void
     */
    public function callInjects($service)
    {
        Extensions\InjectExtension::callInjects($this, $service);
    }


    /**
     * Calls method using autowiring.
     * @return mixed
     */
    public function callMethod($function, array $args = array())
    {
        return call_user_func_array(
            $function,
            Helpers::autowireArguments(\Delorius\Utils\Callback::toReflection($function), $args, $this)
        );
    }


    /********************* shortcuts ****************d*g**/


    /**
     * Expands %placeholders%.
     * @param  mixed
     * @return mixed
     * @deprecated
     */
    public function expand($s)
    {
        return Helpers::expand($s, $this->parameters);
    }


    /** @deprecated */
    public function &__get($name)
    {
        $this->error(__METHOD__, 'getService');
        $tmp = $this->getService($name);
        return $tmp;
    }


    /** @deprecated */
    public function __set($name, $service)
    {
        $this->error(__METHOD__, 'addService');
        $this->addService($name, $service);
    }


    /** @deprecated */
    public function __isset($name)
    {
        $this->error(__METHOD__, 'hasService');
        return $this->hasService($name);
    }


    /** @deprecated */
    public function __unset($name)
    {
        $this->error(__METHOD__, 'removeService');
        $this->removeService($name);
    }


    private function error($oldName, $newName)
    {
        if (empty($this->parameters['container']['accessors'])) {
            trigger_error("$oldName() is deprecated; use $newName() or enable di.accessors in configuration.", E_USER_DEPRECATED);
        }
    }


    public static function getMethodName($name)
    {
        $uname = ucfirst($name);
        return 'createService' . ((string)$name === $uname ? '__' : '') . str_replace('.', '__', $uname);
    }

}
