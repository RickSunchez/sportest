<?php
namespace CMS\Core\Bridges;

use Delorius\DI\CompilerExtension;


class MenuExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $container->addDefinition($this->prefix('adminMenu'))
            ->setClass('\Delorius\Page\Menu\MenuBuilder');

        $container->addDefinition($this->prefix('cabinetMenu'))
            ->setClass('\Delorius\Page\Menu\MenuBuilder');


        if ($this->name === 'menu') {
            $container->addAlias('adminMenu', $this->prefix('adminMenu'));
            $container->addAlias('cabinetMenu', $this->prefix('cabinetMenu'));
        }

    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();

        $adminMenu = $this->prefix('adminMenu');
        $initialize->addBody('$adminMenu = $this->getService(?);', array($adminMenu));
        if (count($config['admin'])) {
            foreach ($config['admin'] as $header => $cnf) {
                $initialize->addBody('$adminMenu->addHeader(?,?)', array($header, $cnf['name']));
                if (count($cnf['links'])) {
                    foreach ($cnf['links'] as $name => $link) {
                        $initialize->addBody('->addLink(?,link_to(?,?))', array($name, $link[0], (array)$link[1]));
                    }
                    $initialize->addBody(';');
                } else {
                    $initialize->addBody(';');
                }
            }
        }

        $cabinetMenu = $this->prefix('cabinetMenu');
        $initialize->addBody('$cabinetMenu = $this->getService(?);', array($cabinetMenu));
        if (count($config['cabinet'])) {
            foreach ($config['cabinet'] as $header => $cnf) {
                $initialize->addBody('$cabinetMenu->addHeader(?,?)', array($header, $cnf['name']));
                if (count($cnf['links'])) {
                    foreach ($cnf['links'] as $name => $link) {
                        $initialize->addBody('->addLink(?,link_to(?,?))', array($name, $link[0], (array)$link[1]));
                    }
                    $initialize->addBody(';');
                } else {
                    $initialize->addBody(';');
                }
            }
        }

    }


}
