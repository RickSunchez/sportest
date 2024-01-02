<?php

namespace CMS\Core\Component\WebLoader;

use Delorius\DI\CompilerExtension;

class WebLoaderExtension extends CompilerExtension
{

    public function getDefaultConfig()
    {
        return array(
            'temp' => null,
            'path' => null,
            'source' => null,
            'gzip' => null,
            'js' => array(),
            'css' => array(),
        );
    }

    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->getDefaultConfig());
        $container = $this->getContainerBuilder();

        $container->parameters['webloader'] = $config;

        $container->addDefinition($this->prefix('jsFiles'))
            ->setClass('\CMS\Core\Component\WebLoader\FileCollection')
            ->setFactory('\CMS\Core\Component\WebLoader\FileCollection', array($config['source']['js']));

        $container->addDefinition($this->prefix('cssFiles'))
            ->setClass('\CMS\Core\Component\WebLoader\FileCollection')
            ->setFactory('\CMS\Core\Component\WebLoader\FileCollection', array($config['source']['css']));

        if ($this->name === 'webloader') {
            $container->addAlias('jsFiles', $this->prefix('jsFiles'));
            $container->addAlias('cssFiles', $this->prefix('cssFiles'));
        }
    }

}
