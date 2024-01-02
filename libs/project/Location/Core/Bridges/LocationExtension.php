<?php

namespace Location\Core\Bridges;

use Delorius\DI\CompilerExtension;


class LocationExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        $container->parameters['location'] = $config;
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $config = $this->getConfig();
        if (!$config['is_routing']) {
            return;
        }
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);

        $initialize->addBody('$this->getService("front")->onStartup[] = function ($front) {                
                $name = ?;
                $route = new \Delorius\Routing\Route(?,?,?,?,?,?);                
                $collectionRouter = $this->getService("routing.routeCollection");
                $collectionRouter->addRoute($name,$route);
                if (\Location\Core\Helper\City::isMainPage()) {                       
                      $collectionRouter->addFirst($route );               
                }  
        };', array($config['router']['name'], $config['router']['path'], $config['router']['default'],
            $config['router']['requirements'], $config['router']['options'], $config['router']['host'],
            $config['router']['methods']));

    }
}
