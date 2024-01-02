<?php

namespace Delorius\Caching\Bridges;

use Delorius\DI\CompilerExtension;
use Delorius\Exception\InvalidState;
use Delorius\Utils\FileSystem;

/**
 * Cache extension for Delorius DI.
 */
class CacheExtension extends CompilerExtension
{
    public $defaults = array(
        'init' => true,
        'type' => 'files',
        'namespace' => 'www',
    );

    /** @var string */
    private $tempDir;


    public function __construct($tempDir)
    {
        $this->tempDir = $tempDir;
    }


    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container->addDefinition($this->prefix('journal'))
            ->setClass('Delorius\Caching\Storage\IJournal')
            ->setFactory('Delorius\Caching\Storage\FileJournal', array($this->tempDir . '/cache'));

        $storage = $container->addDefinition($this->prefix('storage'))
            ->setClass('Delorius\Caching\IStorage');

        if (!$config['init']) {
            $storage->setFactory('Delorius\Caching\Storage\DevNullStorage');
        } elseif ($config['type'] == 'files') {
            $storage->setFactory('Delorius\Caching\Storage\FileStorage', array($this->tempDir . '/cache', '@Delorius\Caching\Storage\IJournal'));
        } elseif ($config['type'] == 'memcached') {
            $storage->setFactory('Delorius\Caching\Storage\MemcachedStorage', array('localhost', 11211, 'mem', '@Delorius\Caching\Storage\IJournal'));
        } else {
            $storage->setFactory('Delorius\Caching\Storage\MemoryStorage');
        }

        $container->addDefinition($this->prefix('cache'))
            ->setClass('Delorius\Caching\Cache')
            ->setFactory('Delorius\Caching\Cache', array('@Delorius\Caching\IStorage', $config['namespace']));

        if ($this->name === 'cache') {
            $container->addAlias('cacheJournal', $this->prefix('journal'));
            $container->addAlias('cacheStorage', $this->prefix('storage'));
            $container->addAlias('cache', $this->prefix('cache'));
        }
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        if (!$this->checkTempDir($this->tempDir . '/cache')) {
            $class->getMethod('initialize')->addBody('Delorius\Caching\Storage\FileStorage::$useDirectories = FALSE;');
        }
    }


    private function checkTempDir($dir)
    {
        FileSystem::createDir($dir); // @ - directory may exists

        // checks whether directory is writable
        $uniq = uniqid('_', TRUE);
        if (!FileSystem::createDir("$dir/$uniq")) { // @ - is escalated to exception
            throw new InvalidState("Unable to write to directory '$dir'. Make this directory writable.");
        }

        // checks whether subdirectory is writable
        $isWritable = @file_put_contents("$dir/$uniq/_", '') !== FALSE; // @ - error is expected
        if ($isWritable) {
            unlink("$dir/$uniq/_");
        }
        FileSystem::delete("$dir/$uniq");
        return $isWritable;
    }

}
