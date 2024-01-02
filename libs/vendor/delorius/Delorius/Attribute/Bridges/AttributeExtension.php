<?php
namespace Delorius\Attribute\Bridges;

use Delorius\DI\CompilerExtension;


class AttributeExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $container = $this->getContainerBuilder();

        $collectionAttribute = $container->addDefinition($this->prefix('collectionAttribute'))
            ->setClass('Delorius\Attribute\CollectionAttribute');

        foreach ($config as $name => $class) {
            $collectionAttribute->addSetup('set', array($name, new $class));
        }

        if ($this->name === 'attribute') {
            $container->addAlias('collectionAttribute', $this->prefix('collectionAttribute'));
        }
    }

}
