<?php
namespace Delorius\DataBase\Bridges;

use Delorius\DI\CompilerExtension;
use Delorius\Exception\Error;

/**
 * Delorius Framework Database services.
 */
class DatabaseExtension extends CompilerExtension
{
    public $databaseDefaults = array(
        'type' => 'MySQL',
        'connection' => array(),
        'table_prefix' => NULL,
        'charset' => 'UTF-8',
        'debugger' => FALSE,
        'caching' => TRUE,
        'profiling' => NULL, // BC
        'autowired' => NULL,
    );

    /** @var bool */
    private $debugMode;


    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }


    public function loadConfiguration()
    {
        $configs = $this->getConfig();
        $defaults = $this->databaseDefaults;
        $defaults['autowired'] = TRUE;

        foreach ((array)$configs as $name => $config) {
            if (!is_array($config)) {
                continue;
            }
            $config = $this->validateConfig($defaults, $config, $this->prefix($name));
            $defaults['autowired'] = FALSE;
            $this->setupDatabase($config, $name);
        }
    }

    private function setupDatabase($config, $name)
    {
        $container = $this->getContainerBuilder();

        if (!isset($config['type'])) {
            throw new Error("DataBase type not defined in $name configuration");
        }

        // Set the driver class name
        $driver = '\\Delorius\\DataBase\\' . ucfirst($config['type']);

        $connection = $container->addDefinition($this->prefix("$name.connection"))
            ->setClass('Delorius\DataBase\DataBase')
            ->setFactory($driver, array($name, $config));

        if ($this->name === 'database') {
            $container->addAlias("database.$name", $this->prefix("$name.connection"));
            $container->addAlias("database.$name.context", $this->prefix("$name.context"));
        }

    }

}
