<?php
namespace Delorius\Http\Bridges;

use Delorius\DI\CompilerExtension;

/**
 * Session extension for Delorius DI.
 */
class SessionExtension extends CompilerExtension
{
    public $defaults = array(
        'debugger' => FALSE,
        'autoStart' => 'smart', // true|false|smart
        'expiration' => NULL,
        'domains' => false,
        'host' => null
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
        $config = $this->getConfig() + $this->defaults;

        $this->setConfig($config);

        $session = $container->addDefinition($this->prefix('session'))
            ->setClass('Delorius\Http\Session');

        if ($config['expiration']) {
            $session->addSetup('setExpiration', array($config['expiration']));
        }

        if ($config['domains']) {
            $session->addSetup('setCookieParameters', array('/', '.' . $config['host']));
        }

        unset(
            $config['expiration'], $config['autoStart'], $config['debugger'],
            $config['domains'], $config['host']
        );

        if (!empty($config)) {
            $session->addSetup('setOptions', array($config));
        }

        if ($this->name === 'session') {
            $container->addAlias('session', $this->prefix('session'));
        }
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();
        $name = $this->prefix('session');

        if ($config['autoStart'] === 'smart') {
            $initialize->addBody('$this->getService(?)->exists() && $this->getService(?)->start();', array($name, $name));

        } elseif ($config['autoStart']) {
            $initialize->addBody('$this->getService(?)->start();', array($name));
        }
    }

}
