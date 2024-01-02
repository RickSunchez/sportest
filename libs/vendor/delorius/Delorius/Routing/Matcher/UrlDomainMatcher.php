<?php
namespace Delorius\Routing\Matcher;

use Delorius\Http\IRequest;
use Delorius\Http\Request;
use Delorius\Routing\Domain;
use Delorius\Routing\DomainCollection;
use Delorius\Routing\RouteCollection;

class UrlDomainMatcher implements UrlMatcherInterface
{
    const REQUIREMENT_MATCH     = 0;
    const REQUIREMENT_MISMATCH  = 1;
    const ROUTE_MATCH           = 2;

    /**
     * @var IRequest
     */
    protected $context;

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
     * @param RouteCollection $routes  A RouteCollection instance
     * @param IRequest  $context The context
     *
     * @api
     */
    public function __construct(DomainCollection $routes, IRequest $context )
    {
        $this->routes = $routes;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(Request $context)
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
     * @param string          $pathinfo The path info to be parsed
     * @param RouteCollection $routes   The set of routes
     *
     * @return array An array of parameters
     *
     * @throws ResourceNotFoundException If the resource could not be found
     * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
     */
    protected function matchCollection($pathinfo, DomainCollection $routes)
    {
        foreach ($routes as $name => $domain) {
            $compiledRoute = $domain->compile();

            $hostMatches = array();
            if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $pathinfo , $hostMatches)) {
                continue;
            }

            $schemeUrl = $this->context->getUrl()->getScheme();
            if($domain->getSchemes()){
                $flag = false;
                foreach($domain->getSchemes() as $key=>$schemes)
                    if($schemes == $schemeUrl)
                        $flag = true;

                if(!$flag)
                    continue;
            }

            return $this->getAttributes($domain, $name, $schemeUrl, $hostMatches);
        }
    }

    /**
     * Returns an array of values to use as request attributes.
     *
     * As this method requires the Route object, it is not available
     * in matchers that do not have access to the matched Route instance
     * (like the PHP and Apache matcher dumpers).
     *
     * @param Route  $route      The route we are matching against
     * @param string $name       The name of the route
     * @param array  $attributes An array of attributes from the matcher
     *
     * @return array An array of parameters
     */
    protected function getAttributes(Domain $domain, $name, $scheme, array $attributes)
    {
        $attr = array();
        $attr['_params'] = $this->mergeDefaults($attributes,array());
        $attr['_route'] = $name;
        $attr['_scheme'] = $scheme;
        $attr['_scriptPath'] = $domain->getPath();
        $host = $domain->getHost();
        foreach($attr['_params'] as $name=>$value){
            $host = str_replace('{'.$name.'}',$value,$host);
        }
        $attr['_host'] = $host;
        $attr['_options_domain'] = $domain->getOptions();

        return $this->mergeDefaults($attr, $domain->getDefaults());
    }

    /**
     * Get merged default parameters.
     *
     * @param array $params   The parameters
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

        return (array)$defaults;
    }
}
