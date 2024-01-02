<?php

namespace Delorius\DI;

use Delorius\Core\Object;
use Delorius\Exception\InvalidArgument;

/**
 * Assignment or calling statement.
 */
class Statement extends Object
{
    /** @var string|array|ServiceDefinition|NULL  class|method|$property */
    private $entity;

    /** @var array */
    public $arguments;


    /**
     * @param  string|array|ServiceDefinition|NULL
     */
    public function __construct($entity, array $arguments = array())
    {
        $this->setEntity($entity);
        $this->arguments = $arguments;
    }


    /**
     * @param  string|array|ServiceDefinition|NULL
     * @return self
     */
    public function setEntity($entity)
    {
        if (!is_string($entity) && !(is_array($entity) && isset($entity[0], $entity[1]))
            && !$entity instanceof ServiceDefinition && $entity !== NULL
        ) {
            throw new InvalidArgument('Argument is not valid Statement entity.');
        }
        $this->entity = $entity;
        return $this;
    }


    public function getEntity($key = null)
    {
        if (is_scalar($key)) {
            return $this->entity[$key];
        }
        return $this->entity;
    }

}
