<?php
namespace Delorius\Routing;

use Delorius\Exception\Error;
use Delorius\Http\IRequest;
use Delorius\Routing\Matcher\UrlDomainMatcher;
use Delorius\Routing\Matcher\UrlMatcherInterface;


class DomainRouter
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
     * @var mixed
     */
    protected $resource;

    /**
     * @var array
     */
    protected $options = array();


    /**
     * Constructor.
     *
     * @param DomainCollection $collection
     * @param IRequest $context
     * @param array $options An array of options
     */
    public function __construct(DomainCollection $collection, IRequest $context, array $options = array())
    {
        $this->collection = $collection;
        $this->context = $context;
        $this->setOptions($options);
    }

    /**
     * Sets options.
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
    public function generate($name, & $parameters = array(), $absoluteUrl = true)
    {
        if (null === $domain = $this->collection->get($name)) {
            throw new Error(sprintf('Unable to generate a domain for the named route "%s" as such route does not exist.', $name));
        }

        if (($scriptPath = $domain->getPath()) == '/') $scriptPath = '';
        $arDefaults = $domain->getDefaults();
        $host = $domain->getHost();
        preg_match_all('#\{(\w+)\}#', $host, $matches);
        foreach ((array)$matches[1] as $key => $param) {
            if (isset($parameters[$param])) {
                $value = $parameters[$param];
                unset($parameters[$param]);
            } else
                $value = $arDefaults[$param];
            $host = str_replace('{' . $param . '}', $value, $host);
        }
        if ($domain->getSchemes()) {
            $schemes = implode('|', $domain->getSchemes());
            $host = $schemes . '://' . $host;
        } else {
            $host = '//' . $host;
        }
        if ($absoluteUrl)
            return $host;
        else
            return $scriptPath;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathInfo)
    {
        return $this->getMatcher()->match($pathInfo);
    }

    /**
     * Gets the UrlMatcher instance associated with this Router.
     *
     * @return UrlMatcherInterface A UrlMatcherInterface instance
     */
    public function getMatcher()
    {
        if (null === $this->matcher) {
            $this->matcher = new UrlDomainMatcher($this->getRouteCollection(), $this->context);
        }
        return $this->matcher;
    }

}
