<?php
namespace Delorius\Core;

class ArrayObject implements \ArrayAccess, \Iterator, \Countable
{

    protected $__vars;
    protected $__counter = 0;
    protected $__change;
    protected $__block;

    public function block($name)
    {
        if ($this->isExists($name)) {
            $this->__block[$name] = 1;
        }
    }

    public function unblock($name)
    {
        if ($this->isBlock($name)) {
            unset($this->__block[$name]);
        }
    }

    public function isBlock($name)
    {
        if (isset($this->__block[$name])) {
            return true;
        }
        return false;
    }

    protected function set($name, $value)
    {
        if ($this->isBlock($name)) {
            return;
        }
        if (empty($name)) {
            $this->__vars[] = $value;
        } else {
            $this->__vars[$name] = $value;
        }
    }


    protected function get($name)
    {
        if (isset($this->__vars[$name])) {
            return $this->__vars[$name];
        }
        return NULL;
    }

    protected function remove($name)
    {
        if ($this->isBlock($name)) {
            return;
        }
        if (isset($this->__vars[$name])) {
            unset($this->__vars[$name]);
        }
    }

    public function isExists($name)
    {
        return isset($this->__vars[$name]);
    }

    public function hasChange($name)
    {
        return $this->__change[$name];
    }

    function __set($name, $value)
    {
        $this->change($name);
        $this->set($name, $value);
    }

    protected function change($name)
    {
        $this->__change[$name] = true;
    }

    function __get($name)
    {
        return $this->get($name);
    }


    function count()
    {
        return count($this->__vars);
    }

    function offsetExists($name)
    {
        return isset($this->__vars[$name]);
    }

    function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    function offsetGet($name)
    {
        return $this->get($name);
    }

    function offsetUnset($name)
    {
        if (isset($this->__vars[$name])) {
            unset($this->__vars[$name]);
        }
    }

    function current()
    {
        $Key = $this->key();
        return $this->get($Key);
    }

    function next()
    {
        $this->__counter++;
    }

    function rewind()
    {
        $this->__counter = 0;
    }

    function key()
    {
        reset($this->__vars);
        for ($i = 0; $i < $this->__counter; $i++) {
            next($this->__vars);
        }
        return key($this->__vars);
    }

    function valid()
    {
        $key = $this->key();
        return isset($this->__vars[$key]);
    }

    public function getVar()
    {
        return $this->__vars;
    }


}