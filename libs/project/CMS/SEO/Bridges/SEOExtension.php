<?php

namespace CMS\SEO\Bridges;

use Delorius\DI\CompilerExtension;


class SEOExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        $container->parameters['seo'] = $config;
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();
        if ($config['enable']) {
            $initialize->addBody('
            $collectionRouter = $this->getService("routing.routeCollection");
            $collectionRouter->add("admin_redirect_data", new \Delorius\Routing\Route("/redirect/{action}.data",array(
			    "_controller" => "CMS:Admin:Redirect:{action}Data",),
			    array("action" => "\w+"),
			    array("host" => "admin"),NULL,array()) 
			);
            $collectionRouter->add("admin_redirect", new \Delorius\Routing\Route("/redirect/{action}",array(
                "_controller" => "CMS:Admin:Redirect:{action}",
                "action" => "list",),
                array("action" => "\w+"),array("host" => "admin"),NULL,array()) 
            );            
            $this->getService("front")->onStartup[] = function ($front) {                
                $path = $front->httpRequest->getUrl()->getPath();
                \CMS\SEO\Model\RedirectChecker::check($path);
                
                $adminMenu = $this->getService("menu.adminMenu");
                $adminMenu->getHeader("config", "Настройки")
                    ->addLink("Редиректы", link_to("admin_redirect", array("action" => "list")));               
            };');
        }

    }
}
