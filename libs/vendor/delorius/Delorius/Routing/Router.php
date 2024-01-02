<?php
namespace Delorius\Routing;


use Delorius\Configure\Site;
use Delorius\Core\Environment;
use Delorius\Exception\BadRequest;
use Delorius\Exception\NotFound;
use Delorius\Http\IRequest;
use Delorius\Routing\Matcher\UrlMatcher;
use Delorius\Routing\Matcher\UrlMatcherInterface;

class Router
{
    /**
     * @var UrlMatcherInterface|null
     */
    protected $matcher;

    /**
     * @var IRequest
     */
    protected $context;

    /**
     * @var RouteCollection|null
     */
    protected $collection;

    /**
     * @var
     */
    protected $site;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var DomainRouter
     */
    protected $domain;

    /**
     * @var array
     */
    protected $optDomain = array();


    /**
     * @param DomainRouter $domain
     * @param RouteCollection $collection
     * @param array $options
     */
    public function __construct(DomainRouter $domain, RouteCollection $collection, Site $site, array $options = array())
    {
        $this->domain = $domain;
        $this->collection = $collection;
        $this->site = $site;
        $this->setOptions($options);
    }

    public function getControllerParams(\Delorius\Http\IRequest $httpRequest)
    {
        $this->optDomain = $this->domain->match($httpRequest->getUrl()->getAbsoluteUrlNoQuery());
        if ($this->optDomain == null) {
            throw new NotFound('Not found domain');
        }
        $this->site->domain = $this->optDomain;
        $config = Environment::getContext()->getParameters('site.templates.' . $this->optDomain['_route']);

        if ($config['template']) {
            $this->site->template = $config['template'];
        }
        if ($config['layout']) {
            $this->site->layout = $config['layout'];
        }
        if ($config['mobile']) {
            $this->site->mobile = $config['mobile'];
        }

        $url = $httpRequest->getUrl();
        if (!empty($this->optDomain['_scriptPath'])) {
            $url->setScriptPath($this->optDomain['_scriptPath']);
        }

        return $this->match($url->getPathInfo());
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:     The cache directory (or null to disable caching)
     *   * debug:         Whether to enable debugging or not (false by default)
     *   * resource_type: Type hint for the main resource (optional)
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }
        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: "%s".', implode('\', \'', $invalid)));
        }
    }

    /**
     * Sets an option.
     *
     * @param string $key The key
     * @param mixed $value The value
     *
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }
        $this->options[$key] = $value;
    }

    /**
     * Gets an option value.
     *
     * @param string $key The key
     *
     * @return mixed The value
     *
     * @throws \InvalidArgumentException
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the "%s" option.', $key));
        }
        return $this->options[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(IRequest $context)
    {
        $this->context = $context;
        if (null !== $this->matcher) {
            $this->getMatcher()->setContext($context);
        }
        return $this;
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

    public function generate($name, $parameters = array(), $absoluteUrl = true)
    {
        if (is_string($name)) {
            if (null === $router = $this->collection->get($name)) {
                throw new BadRequest(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', $name));
            }
            $arRequirements = $router->getRequirements();
            $host = '';
            if ($arRequirements['_host']) {
                $host = $this->domain->generate($arRequirements['_host'], $parameters, $absoluteUrl);
            } else {
                if ($absoluteUrl) {
                    $host = '';
                } else {
                    if (($host = $this->context->getUrl()->getScriptPath()) == '/') $host = '';
                }
            }
            $arDefaults = $router->getDefaults();
            $path = $router->getPath();
            preg_match_all('#\{(\w+)\}#', $path, $matches);
            foreach ((array)$matches[1] as $key => $param) {
                if (isset($parameters[$param])) {
                    $value = $parameters[$param];
                    unset($parameters[$param]);
                } else {
                    $value = $arDefaults[$param];
                }
                $path = str_replace('{' . $param . '}', $value, $path);
            }
            $get = '';
            if (count($parameters)) {
                $get = '?' . http_build_query($parameters);
            }
            return $host . $path . $get;
        } elseif (is_bool($name) && $name == false) {
            $url = clone $this->context->getUrl();
            $url->setQuery($parameters);
            return $url;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $parameters = $this->getMatcher()->match($pathinfo);
        return is_array($parameters) ? new RouterParameters($parameters) : null;
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function getMatcher()
    {
        if (null === $this->matcher) {
            if(!$this->optDomain){
                $this->optDomain = array();
            }
            $this->matcher = new UrlMatcher($this->getRouteCollection(), $this->context, $this->optDomain);
        }
        return $this->matcher;
    }
}
