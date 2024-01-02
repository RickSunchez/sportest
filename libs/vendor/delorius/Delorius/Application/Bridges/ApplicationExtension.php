<?php
namespace Delorius\Application\Bridges;

use Delorius\DI\CompilerExtension;

/**
 * Application extension for Delorius DI.
 */
class ApplicationExtension extends CompilerExtension
{
    public $defaults = array(
        'behaviors' => array(),
        'catchExceptions' => true,
        'profiler' => false,
        'toolbar' => NULL,
    );

    /** @var bool */
    private $debugMode;


    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }


    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('site'))
            ->setClass('\Delorius\Configure\Site')
            ->addSetup('$layout', array($container->parameters['site']['layout']))
            ->addSetup('$template', array($container->parameters['site']['template']))
            ->addSetup('$mobile', array($container->parameters['site']['mobile']))
            ->addSetup('$template_path', array($container->parameters['path']['libs'] . '/templates'));

        $container->addDefinition($this->prefix('signalReceiver'))
            ->setClass('Delorius\Application\SignalReceiver');

        $container->addDefinition($this->prefix('application'))
            ->setClass('Delorius\Application\Front');

        $container->addDefinition($this->prefix('controllerFactory'))
            ->setClass('Delorius\Application\IControllerFactory')
            ->setFactory('Delorius\Application\ControllerFactory');


        if ($this->name === 'application') {
            $container->addAlias('application', $this->prefix('application'));
            $container->addAlias('front', $this->prefix('application'));
            $container->addAlias('site', $this->prefix('site'));
            $container->addAlias('controllerFactory', $this->prefix('controllerFactory'));
        }

    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->validateConfig($this->defaults);
        $front = 'front';


        $profile = $config['profiler'] ? 'enable()' : 'disable()';
        $toolbar = $config['toolbar']['enabled'] ? 'enable()' : 'disable()';
        if ($config['toolbar']['render']) {
            $print = $config['toolbar']['print'] ? true : false;
            $render = _sf('\Delorius\Tools\Debug\Toolbar::render({0});', $print);
        }
        $initialize->addBody('
             \Delorius\Tools\Debug\Profiler::' . $profile . ';
             \Delorius\Tools\Debug\Toolbar::' . $toolbar . ';
             \Delorius\Tools\Debug\Toolbar::setSecretKey(?);
        ', array($config['toolbar']['secret_key']));

        $initialize->addBody('$this->getService(?)->onShutdown[] = function ($front)  {
                if(!$front->ajaxMode && !$front->httpRequest->isPost()){
                    ' . $render . '
                }
        };', array($front));

        #behaviors
        $initialize->addBody('$controllerFactory = $this->getService(?);', array('controllerFactory'));
        if (count($config['behaviors'])) {
            foreach ($config['behaviors'] as $name => $behavior) {
                $initialize->addBody('$controllerFactory->addBehavior(?,?);', array($name, $behavior));
            }
        }

    }


}
