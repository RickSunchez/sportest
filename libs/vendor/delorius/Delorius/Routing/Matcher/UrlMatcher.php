<?php

namespace Delorius\Routing\Matcher;

use Delorius\Http\IRequest;
use Delorius\Routing\Route;
use Delorius\Routing\RouteCollection;

class UrlMatcher implements UrlMatcherInterface
{
    const REQUIREMENT_MATCH = 0;
    const REQUIREMENT_MISMATCH = 1;
    const ROUTE_MATCH = 2;

    /**
     * @var IRequest
     */
    protected $context;
    /**
     * @var array
     */
    protected $optDomain;

    /**
     * @var array
     */
    protected $allow = array();

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * Constructor.
     *
     * @param RouteCollection $routes A RouteCollection instance
     * @param IRequest $context The context
     *
     * @api
     */
    public function __construct(RouteCollection $routes, IRequest $context, array $optDomain)
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->optDomain = $optDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(IRequest $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $this->allow = array();
        if ($ret = $this->matchCollection(rawurldecode($pathinfo), $this->routes)) {
            return $ret;
        }
        return null;
    }

    /**
     * Tries to match a URL with a set of routes.
     *
     * @param string $pathinfo The path info to be parsed
     * @param RouteCollection $routes The set of routes
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
    protected function matchCollection($pathinfo, RouteCollection $routes)
    {
        $list_host_first = $routes->getByFirst();
        if (count($list_host_first)) {
            $result = $this->_matchCurrentList($pathinfo, $list_host_first);

            if ($result !== false) {
                return $result;
            }
        }

        $list_host = $routes->getByHost($this->optDomain['_route']);
        if (count($list_host)) {
            $result = $this->_matchCurrentList($pathinfo, $list_host);

            if ($result !== false) {
                return $result;
            }
        }

        $list_host_all = $routes->getByHost();
        if (count($list_host_all)) {
            $result = $this->_matchCurrentList($pathinfo, $list_host_all);

            if ($result !== false) {
                return $result;
            }
        }

    }

    /**
     * @param string $pathinfo
     * @param array $routes
     * @return array
     */
    private function _matchCurrentList($pathinfo, $routes)
    {
        foreach ($routes as $name => $route) {
            $compiledRoute = $route->compile();

            // check the static prefix of the URL first. Only use the more expensive preg_match when it matches
            if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($pathinfo, $compiledRoute->getStaticPrefix())) {
                continue;
            }
            if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
                continue;
            }
            // check HTTP method requirement
            if ($req = $route->getRequirement('_method')) {
                // HEAD and GET are equivalent as per RFC
                if ('HEAD' === $method = $this->context->getMethod()) {
                    $method = 'GET';
                }

                if (!in_array($method, $req = explode('|', strtoupper($req)))) {
                    $this->allow = array_merge($this->allow, $req);
                    continue;
                }
            }
            $hostParams = array();
            if (isset($this->optDomain['_params'])) {
                $hostParams['_domain'] = $this->optDomain['_params'];
            }

            if (isset($this->optDomain['_options_domain']))
                $route->addOptions(array('_domain' => $this->optDomain['_options_domain']));

            return $this->getAttributes($route, $name, array_replace($matches, $hostParams));
        }
        return false;
    }

    /**
     * Returns an array of values to use as request attributes.
     *
     * As this method requires the Route object, it is not available
     * in matchers that do not have access to the matched Route instance
     * (like the PHP and Apache matcher dumpers).
     *
     * @param Route $route The route we are matching against
     * @param string $name The name of the route
     * @param array $attributes An array of attributes from the matcher
     *
     * @return array An array of parameters
     */
    protected function getAttributes(Route $route, $name, array $attributes)
    {
        $default = $route->getDefaults();
        if (isset($attributes['action']) || $default['action']) {
            $action = $attributes['action'] ? $attributes['action'] : $default['action'];
            $default['_controller'] = str_replace("{action}", $action, $default['_controller']);
            unset($attributes['action'], $default['action']);
        }
        $attr = array();
        $attr['_params'] = $this->mergeDefaults($attributes, $attributes['_domain']);
        $attr['_route'] = $name;
        $attr['_options'] = $route->getOptions();
        $attr['_name'] = $route->getName();
        return $this->mergeDefaults($attr, $default);
    }

    /**
     * Get merged default parameters.
     *
     * @param array $params The parameters
     * @param array $defaults The defaults
     *
     * @return array Merged default parameters
     */
    protected function mergeDefaults($params, $defaults)
    {
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $defaults[$key] = $value;
            }
        }
        return $defaults;
    }
}
