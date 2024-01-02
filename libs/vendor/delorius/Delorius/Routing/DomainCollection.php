<?php

namespace Delorius\Routing;


class DomainCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Domain[]
     */
    private $routes = array();

    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    public function count()
    {
        return count($this->routes);
    }

    public function add($name, Domain $route)
    {
        unset($this->routes[$name]);
        $this->routes[$name] = $route;
        return $this;
    }

    public function all()
    {
        return $this->routes;
    }

    public function get($name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    public function remove($name)
    {
        foreach ((array) $name as $n) {
            unset($this->routes[$n]);
        }
        return $this;
    }

    public function addCollection(DomainCollection $collection)
    {
        foreach ($collection->all() as $name => $domain) {
            unset($this->routes[$name]);
            $this->routes[$name] = $domain;
        }
        return $this;
    }

    public function addDefaults(array $defaults)
    {
        if ($defaults) {
            foreach ($this->routes as $route) {
                $route->addDefaults($defaults);
            }
        }
        return $this;
    }

    public function addRequirements(array $requirements)
    {
        if ($requirements) {
            foreach ($this->routes as $domain) {
                $domain->addRequirements($requirements);
            }
        }
        return $this;
    }

    public function addOptions(array $options)
    {
        if ($options) {
            foreach ($this->routes as $domain) {
                $domain->addOptions($options);
            }
        }
        return $this;
    }

    public function setSchemes($schemes)
    {
        foreach ($this->routes as $domain) {
            $domain->setSchemes($schemes);
        }
        return $this;
    }
}
