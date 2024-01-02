<?php
namespace Delorius\Bootstrap;

use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Exception\InvalidState;
use Delorius\Exception\NotSupported;
use Delorius\Loaders\RobotLoader;
use Delorius\Utils\FileSystem;

/**
 * Initial system DI container generator.
 *
 * @property   bool $debugMode
 * @property-write $tempDirectory
 */
class Configurator extends Object
{

    const DEVELOPMENT = 'development';
    const PRODUCTION = 'production';

    const AUTO = TRUE,
        NONE = FALSE;

    const COOKIE_SECRET = 'df-debug';

    /** @var callable[]  function (Configurator $sender, DI\Compiler $compiler); Occurs after the compiler is created */
    public $onCompile;

    /** @var array */
    public $defaultExtensions = array(
        'php' => 'Delorius\DI\Extensions\PhpExtension',
        'routing' => array('Delorius\Routing\Bridges\RouterExtension', array('%debugMode%')),      #!!!
        'constants' => 'Delorius\DI\Extensions\ConstantsExtension',
        'extensions' => 'Delorius\DI\Extensions\ExtensionsExtension',
        'application' => array('Delorius\Application\Bridges\ApplicationExtension', array('%debugMode%', array('%libDir%'), '%tempDir%/cache')),
        'decorator' => 'Delorius\DI\Extensions\DecoratorExtension',
        'cache' => array('Delorius\Caching\Bridges\CacheExtension', array('%tempDir%')),
        'database' => array('Delorius\DataBase\Bridges\DatabaseExtension', array('%debugMode%')),
        'di' => array('Delorius\DI\Extensions\DIExtension', array('%debugMode%')),
        'http' => 'Delorius\Http\Bridges\HttpExtension',
        'reflection' => array('Delorius\Reflection\Bridges\ReflectionExtension', array('%debugMode%')),
        'session' => array('Delorius\Http\Bridges\SessionExtension', array('%debugMode%')),
        'security' => array('Delorius\Security\Bridges\SecurityExtension', array('%debugMode%')),
        'inject' => 'Delorius\DI\Extensions\InjectExtension',
        'attribute' => 'Delorius\Attribute\Bridges\AttributeExtension',
        'migration' => 'Delorius\Migration\Bridges\MigrationExtension',
    );

    /** @var string[] of classes which shouldn't be autowired */
    public $autowireExcludedClasses = array(
        'stdClass',
    );

    /** @var array */
    protected $parameters;

    /** @var array */
    protected $services = array();

    /** @var array [file|array, section] */
    protected $files = array();


    public function __construct()
    {
        $this->parameters = $this->getDefaultParameters();
    }


    /**
     * Set parameter %debugMode%.
     * @param  bool|string|array
     * @return self
     */
    public function setDebugMode($value)
    {
        if (is_string($value) || is_array($value)) {
            $value = static::detectDebugMode($value);
        } elseif (!is_bool($value)) {
            throw new Error(sprintf('Value must be either a string, array, or boolean, %s given.', gettype($value)));
        }
        $this->parameters['debugMode'] = $value;
        $this->parameters['productionMode'] = !$this->parameters['debugMode']; // compatibility
        $this->parameters['environment'] = $this->parameters['debugMode'] ? 'development' : 'production';
        return $this;
    }


    /**
     * @return bool
     */
    public function isDebugMode()
    {
        return $this->parameters['debugMode'];
    }


    /**
     * Sets path to temporary directory.
     * @return self
     */
    public function setTempDirectory($path)
    {
        $this->parameters['tempDir'] = $path;
        return $this;
    }


    /**
     * @param $path
     * @return self
     */
    public function setWwwDirectory($path)
    {
        $this->parameters['wwwDir'] = $path;
        return $this;
    }


    /**
     * @param $path
     * @return self
     */
    public function setLibsDirectory($path)
    {
        $this->parameters['libDir'] = $path;
        return $this;
    }


    /**
     * Adds new parameters. The %params% will be expanded.
     * @return self
     */
    public function addParameters(array $params)
    {
        $this->parameters = \Delorius\DI\Config\Helpers::merge($params, $this->parameters);
        return $this;
    }


    /**
     * Add instances of services.
     * @return self
     */
    public function addServices(array $services)
    {
        $this->services = $services + $this->services;
        return $this;
    }


    /**
     * @return array
     */
    protected function getDefaultParameters()
    {
        $debugMode = static::detectDebugMode();

        $basic_host = $_SERVER['HTTP_HOST'];
        $domains = explode('.', $_SERVER['HTTP_HOST']);
        if (count($domains) >= 3) {
            $d1 = array_pop($domains);
            $d2 = array_pop($domains);
            $basic_host = $d2 . '.' . $d1;
        }

        return array(
            'host' => $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : PHP_SAPI,
            'basicHost' => $basic_host,
            'debugMode' => $debugMode,
            'productionMode' => !$debugMode,
            'environment' => $debugMode ? 'development' : 'production',
            'consoleMode' => PHP_SAPI === 'cli',
            'container' => array(
                'class' => NULL,
                'parent' => NULL,
            ),
            'security' => array(
                'namespace' => 'user',
                'guestRole' => 'guest',
                'authenticatedRole' => 'registered',
                'rootRole' => 'root'
            ),
        );
    }


    /**
     * @return \Delorius\Loaders\RobotLoader
     * @throws NotSupported if RobotLoader is not available
     */
    public function createRobotLoader()
    {
        if (!class_exists('Delorius\Loaders\RobotLoader')) {
            throw new NotSupported('RobotLoader not found');
        }

        $loader = new RobotLoader();
        $loader->setCacheStorage(new \Delorius\Caching\Storage\FileStorage($this->getCacheDirectory()));
        $loader->autoRebuild = $this->parameters['debugMode'];
        return $loader;
    }


    /**
     * Adds configuration file.
     * @return self
     */
    public function addConfig($file, $section = NULL)
    {
        if ($section === NULL && is_string($file) && $this->parameters['debugMode']) { // back compatibility
            try {
                $loader = new \Delorius\DI\Config\Loader;
                $loader->load($file, $this->parameters['environment']);
                trigger_error("Config file '$file' has sections, call addConfig() with second parameter Configurator::AUTO.", E_USER_WARNING);
                $section = $this->parameters['environment'];
            } catch (\Exception $e) {
            }
        }
        $this->files[] = array($file, $section === self::AUTO ? $this->parameters['environment'] : $section);
        return $this;
    }


    /**
     * Returns system DI container.
     * @return \Delorius\DI\Container
     */
    public function createContainer()
    {
        $loader = new \Delorius\DI\ContainerLoader(
            $this->getCacheDirectory() . '/Delorius.Configurator',
            $this->parameters['debugMode']
        );
        $class = $loader->load(
            array($this->parameters, $this->files),
            array($this, 'generateContainer')
        );
        $container = new $class;
        foreach ($this->services as $name => $service) {
            $container->addService($name, $service);
        }
        $container->initialize();
        return $container;
    }


    /**
     * @return string
     * @internal
     */
    public function generateContainer(\Delorius\DI\Compiler $compiler)
    {
        $loader = $this->createLoader();
        $compiler->addConfig(array('parameters' => $this->parameters));
        $fileInfo = array();
        foreach ($this->files as $info) {
            if (is_scalar($info[0])) {
                $fileInfo[] = "// source: $info[0] $info[1]";
                $info[0] = $loader->load($info[0], $info[1]);
            }
            $compiler->addConfig($this->fixCompatibility($info[0]));
        }
        $compiler->addDependencies($loader->getDependencies());

        $builder = $compiler->getContainerBuilder();
        $builder->addExcludedClasses($this->autowireExcludedClasses);

        foreach ($this->defaultExtensions as $name => $extension) {
            list($class, $args) = is_string($extension) ? array($extension, array()) : $extension;
            if (class_exists($class)) {
                $rc = new \ReflectionClass($class);
                $args = \Delorius\DI\Helpers::expand($args, $this->parameters, TRUE);
                $compiler->addExtension($name, $args ? $rc->newInstanceArgs($args) : $rc->newInstance());
            }
        }

        $this->onCompile($this, $compiler);

        $classes = $compiler->compile();

        if (!empty($builder->parameters['container']['parent'])) {
            $classes[0]->setExtends($builder->parameters['container']['parent']);
        }

        return implode("\n", $fileInfo) . "\n\n" . implode("\n\n\n", $classes)
        . (($tmp = $builder->parameters['container']['class']) ? "\nclass $tmp extends {$builder->getClassName()} {}\n" : '');
    }


    /**
     * @return \Delorius\DI\Config\Loader
     */
    protected function createLoader()
    {
        return new \Delorius\DI\Config\Loader;
    }


    protected function getCacheDirectory()
    {
        if (empty($this->parameters['tempDir'])) {
            throw new InvalidState('Set path to temporary directory using setTempDirectory().');
        }
        $dir = $this->parameters['tempDir'] . '/cache';
        if (!is_dir($dir)) {
            FileSystem::createDir($dir); // @ - directory may already exist
        }
        return $dir;
    }


    /**
     * Back compatibility with
     * @return array
     */
    protected function fixCompatibility($config)
    {
        //code code edit config
        return $config;
    }


    /********************* tools ****************d*g**/


    /**
     * Detects debug mode by IP address.
     * @param  string|array IP addresses or computer names whitelist detection
     * @return bool
     */
    public static function detectDebugMode($list = NULL)
    {
        $addr = isset($_SERVER['REMOTE_ADDR'])
            ? $_SERVER['REMOTE_ADDR']
            : php_uname('n');
        $secret = isset($_COOKIE[self::COOKIE_SECRET]) && is_string($_COOKIE[self::COOKIE_SECRET])
            ? $_COOKIE[self::COOKIE_SECRET]
            : null;
        $list = is_string($list)
            ? preg_split('#[,\s]+#', $list)
            : (array) $list;
        if (!isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !isset($_SERVER['HTTP_FORWARDED'])) {
            $list[] = '127.0.0.1';
            $list[] = '::1';
        }

        return in_array($addr, $list, true) || in_array("$secret@$addr", $list, true);
    }

}
