<?php
namespace CMS\Core\Component\Sitemaps\Bridges;

use Delorius\DI\CompilerExtension;


class SitemapsExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();

        $container->addDefinition($this->prefix('sitemaps'))
            ->setClass('\CMS\Core\Component\Sitemaps\Collection')
            ->setFactory('\CMS\Core\Component\Sitemaps\Collection')
            ->addSetup('build', array($config));

        if ($this->name === 'sitemaps') {
            $container->addAlias('sitemaps', $this->prefix('sitemaps'));
        }
    }

}
