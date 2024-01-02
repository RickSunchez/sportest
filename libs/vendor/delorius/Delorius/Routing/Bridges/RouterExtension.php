<?php
namespace Delorius\Routing\Bridges;

use Delorius\Core\Common;
use Delorius\DI\CompilerExtension;


class RouterExtension extends CompilerExtension
{
    public $defaults = array(
        'libDir' => '%libDir%',
        'router' => array(),
        'domain' => array(),
        'debugger' => true  # debugger bar panel
    );

    /**
     * @var array
     */
    protected $collections = array();

    /** @var bool */
    private $debugMode;

    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }

    public function beforeCompile()
    {
        $config = $this->validateConfig($this->defaults);
        #router init
        foreach ($config['router'] as $project => $bundles) {
            if (count($bundles)) {
                foreach ($bundles as $bundle => $opts) {
                    $collection = Common::getRouter($project . ':' . $bundle);
                    if (count($collection)) {
                        foreach ($collection as $name => $cnf) {
                            if (count($opts['options'])) {
                                $cnf[3] = $opts['options'];
                            }
                            $this->collections[$name] = $cnf;
                        }
                    }
                }
            }
        }
    }


    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);
        $container->parameters['domain'] = $config['domain'];
        $container->addDefinition($this->prefix('routeCollection'))
            ->setClass('Delorius\Routing\RouteCollection');

        $container->addDefinition($this->prefix('domainCollection'))
            ->setClass('Delorius\Routing\DomainCollection');

        $container->addDefinition($this->prefix('domainRouter'))
            ->setClass('Delorius\Routing\DomainRouter');

        $container->addDefinition($this->prefix('routing'))
            ->setClass('Delorius\Routing\Router')
            ->addSetup('setContext', array('@httpRequest'));



        if ($this->name === 'routing') {
            $container->addAlias('router', $this->prefix('routing'));
            $container->addAlias('routeCollection', $this->prefix('routeCollection'));
            $container->addAlias('domainRouter', $this->prefix('domainRouter'));
            $container->addAlias('domainCollection', $this->prefix('domainCollection'));
        }

    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        #domainRouter
        $service = $this->prefix('domainCollection');
        $initialize->addBody('$domainRouter = $this->getService(?);', array($service));
        $config = $this->validateConfig($this->defaults);
        if (count($config['domain'])) {
            foreach ($config['domain'] as $name => $cnf) {
                $initialize->addBody('$domainRouter->add(?, new \Delorius\Routing\Domain(?,?,?,?,?,?) );',
                    array($name, $cnf[0], (array)$cnf[1], (array)$cnf[2], (array)$cnf[3], $cnf[4], (array)$cnf[5]));
            }
        }

        #routeCollection
        $service = $this->prefix('routeCollection');
        $initialize->addBody('$collectionRouter = $this->getService(?);', array($service));
        if (count($this->collections))
            foreach ($this->collections as $name => $cnf) {
                $initialize->addBody('$collectionRouter->add(?, new \Delorius\Routing\Route(?,?,?,?,?,?) );',
                    array($name, $cnf[0], (array)$cnf[1], (array)$cnf[2], (array)$cnf[3], $cnf[4], (array)$cnf[5]));
            }

    }


}
