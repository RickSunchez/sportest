<?php
namespace CMS\Core\Bridges;

use Delorius\DI\CompilerExtension;
use Delorius\Utils\FileSystem;

class ThumbExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        $container->parameters['thumb'] = $config;
        FileSystem::createDir($config['path']);
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $config = $this->getConfig();
        foreach ($config['constants'] as $name => $value) {
            $class->getMethod('initialize')->addBody('define(?, ?);', array($name, $value));
        }

    }


}
