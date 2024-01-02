<?php
namespace Delorius\Migration\Bridges;

use Delorius\DI\CompilerExtension;


class MigrationExtension extends CompilerExtension
{

    public $defaults = array(
        'orm' => array(),
    );

    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();

        $collectionAttribute = $container->addDefinition($this->prefix('migration'))
            ->setClass('Delorius\Migration\MigrationManager');

        if (count($config['orm']))
            foreach ($config['orm'] as $name => $value) {
                if (is_int($name)) {
                    $collectionAttribute->addSetup('add', array(new \Delorius\Migration\MigrationOrm($value)));
                }
            }

        if ($this->name === 'migration') {
            $container->addAlias('migration', $this->prefix('migration'));
        }
    }

    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->validateConfig($this->defaults);
        if (count($config['orm'])) {
            $service = $this->prefix('migration');
            $initialize->addBody('$migration = $this->getService(?);', array($service));


            foreach ($config['orm'] as $name => $values) {
                if (!is_int($name)) {
                    $initialize->addBody('$migrationItem = $migration->add(new \Delorius\Migration\MigrationOrm(?));', array($name));
                    if (count($values)) {
                        foreach ($values as $insert) {
                            if (count($insert)) {
                                $initialize->addBody('$migrationItem->insert(?);', array($insert));
                            }
                        }
                    }
                }
            }
        }


    }

}
