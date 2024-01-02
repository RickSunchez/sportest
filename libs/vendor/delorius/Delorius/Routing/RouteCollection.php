<?php

namespace Delorius\Routing;


class RouteCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Route[]
     */
    private $routes = array();

    /**
     * @var array
     */
    private $hosts = array();


    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
        }
    }

    /**
     * Gets the current RouteCollection as an Iterator that includes all routes.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over routes
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Gets the number of Routes in this collection.
     *
     * @return int The number of routes
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Adds a route.
     *
     * @param string $name The route name
     * @param Route $route A Route instance
     *
     * @api
     */
    public function add($name, Route $route)
    {
        $this->addRoute($name, $route);
        $this->addHostRoute($route);
        return $this;
    }

    /**
     * @param $name
     * @param Route $route
     */
    public function addRoute($name, Route $route)
    {
        unset($this->routes[$name]);
        $route->setName($name);
        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * @param Route $route
     */
    public function addFirst(Route $route)
    {
        $this->hosts['_first_'][$route->getName()] = $route;
    }

    /**
     * @param Route $route
     */
    protected function addHostRoute(Route $route)
    {
        $hosts = $route->getHost();
        if ($hosts != '' && count($hosts)) {
            foreach ($hosts as $host) {
                $this->hosts[$host][$route->getName()] = $route;
            }
        } else {
            $this->hosts[''][$route->getName()] = $route;
        }

    }

    /**
     * Returns all routes in this collection.
     *
     * @return Route[] An array of routes
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * @param null|string $host
     * @return mixed
     */
    public function getByHost($host = null)
    {
        return $this->hosts[$host];
    }

    /**
     * @return mixed
     */
    public function getByFirst()
    {
        return $this->hosts['_first_'];
    }


    /**
     * Gets a route by name.
     *
     * @param string $name The route name
     *
     * @return Route|null A Route instance or null when not found
     */
    public function get($name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }


    /**
     * Removes a route or an array of routes by name from the collection
     *
     * For BC it's also removed from the root, which will not be the case in 2.3
     * as the RouteCollection won't be a tree structure.
     *
     * @param string|array $name The route name or an array of route names
     */
    public function remove($name)
    {
        foreach ((array)$name as $n) {
            unset($this->routes[$n]);
        }
        return $this;
    }

    /**
     * Adds a route collection at the end of the current set by appending all
     * routes of the added collection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     *
     * @api
     */
    public function addCollection(RouteCollection $collection)
    {
        foreach ($collection->all() as $name => $route) {
            $this->addRoute($name, $route);
            $this->addHostRoute($route);
        }
        return $this;
    }


    /**
     * Sets the host pattern on all routes.
     *
     * @param string $pattern The host_name
     */
    public function setHost($host_name)
    {
        foreach ($this->routes as $route) {
            $route->setHost($host_name);
        }
        return $this;
    }

    /**
     * Adds defaults to all routes.
     *
     * An existing default value under the same name in a route will be overridden.
     *
     * @param array $defaults An array of default values
     */
    public function addDefaults(array $defaults)
    {
        if ($defaults) {
            foreach ($this->routes as $route) {
                $route->addDefaults($defaults);
            }
        }
        return $this;
    }

    /**
     * Adds requirements to all routes.
     *
     * An existing requirement under the same name in a route will be overridden.
     *
     * @param array $requirements An array of requirements
     */
    public function addRequirements(array $requirements)
    {
        if ($requirements) {
            foreach ($this->routes as $route) {
                $route->addRequirements($requirements);
            }
        }
        return $this;
    }

    /**
     * Adds options to all routes.
     *
     * An existing option value under the same name in a route will be overridden.
     *
     * @param array $options An array of options
     */
    public function addOptions(array $options)
    {
        if ($options) {
            foreach ($this->routes as $route) {
                $route->addOptions($options);
            }
        }
        return $this;
    }

    /**
     * Sets the HTTP methods (e.g. 'POST') all child routes are restricted to.
     *
     * @param string|array $methods The method or an array of methods
     */
    public function setMethods($methods)
    {
        foreach ($this->routes as $route) {
            $route->setMethods($methods);
        }
        return $this;
    }

}
