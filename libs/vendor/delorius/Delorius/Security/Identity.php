<?php
namespace Delorius\Security;

use Delorius\Core\FreezableObject;
use Delorius\Core\ObjectMixin;

Class Identity extends FreezableObject implements IIdentity
{

    /** @var mixed */
    private $id;

    /** @var array */
    private $roles;

    /** @var array */
    private $data;


    /**
     * @param  mixed   identity ID
     * @param  mixed   roles
     * @param  array   user data
     */
    public function __construct($id, $roles = NULL, $data = NULL)
    {
        $this->setId($id);
        $this->setRoles((array)$roles);
        $this->data = $data instanceof \Traversable ? iterator_to_array($data) : (array)$data;
    }


    /**
     * Sets the ID of user.
     * @param  mixed
     * @return Identity  provides a fluent interface
     */
    public function setId($id)
    {
        $this->updating();
        $this->id = is_numeric($id) ? 1 * $id : $id;
        return $this;
    }


    /**
     * Returns the ID of user.
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Sets a list of roles that the user is a member of.
     * @param  array
     * @return Identity  provides a fluent interface
     */
    public function setRoles(array $roles)
    {
        $this->updating();
        $this->roles = $roles;
        return $this;
    }


    /**
     * Returns a list of roles that the user is a member of.
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * Returns a user data.
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Sets user data value.
     * @param  string  property name
     * @param  mixed   property value
     * @return void
     */
    public function __set($key, $value)
    {
        if (parent::__isset($key)) {
            parent::__set($key, $value);
        } else {
            $this->data[$key] = $value;
        }
    }


    /**
     * Returns user data value.
     * @param  string  property name
     * @return mixed
     */
    public function &__get($key)
    {
        if (parent::__isset($key)) {
            return parent::__get($key);
        } else {
            return $this->data[$key];
        }
    }


    /**
     * Is property defined?
     * @param  string  property name
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]) || parent::__isset($key);
    }


    /**
     * Removes property.
     * @param  string  property name
     * @return void
     */
    public function __unset($name)
    {
        ObjectMixin::remove($this, $name);
    }

}
