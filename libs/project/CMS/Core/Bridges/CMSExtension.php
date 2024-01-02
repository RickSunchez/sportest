<?php

namespace CMS\Core\Bridges;

use Delorius\DI\CompilerExtension;
use Delorius\Utils\FileSystem;


class CMSExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        $container->parameters['cms'] = $config;
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $paths = $this->getContainerBuilder()->parameters['path'];
        clearstatcache();

        if (!file_exists($paths['log'])) {
            FileSystem::createDir($paths['log']);
        }
        if (!file_exists($paths['cron'])) {
            FileSystem::createDir($paths['cron']);
        }
        if (!file_exists($paths['config'])) {
            FileSystem::createDir($paths['config']);
        }

        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);
        $config = $this->getConfig();


        if ($config['visitor'])
            $initialize->addBody('\CMS\Core\Helper\Visitor::register();');

        $front = 'front';

        $initialize->addBody('$this->getService(?)->onResponseAfter[] = function ($front, $response) {
               df_print_r_echo();
        };', array($front));

        $initialize->addBody('$parser = $this->getService(?);', array('parser'));
        $initialize->addBody('$site = $this->getService(?);', array('site'));
        $initialize->addBody('$this->getService(?)->onResponseAfter[] = function ($front, $response) use ($parser,$site) {
                $compress = true;
                if ($front->ajaxMode || is_array($response)) {
                    $compress = false;
                }
                if($site->isExists("isParser")){
                    if($site->isParser) {
                        $response = $parser->html($response,$compress);
                    }
				}else{
				    $response = $parser->html($response,$compress);
				}
                $front->response($response);
        };', array($front));

        $initialize->addBody('$header = $this->getService(?);', array('header'));
        $initialize->addBody('$this->getService(?)->onResponseAfter[] = function ($front, $response) use($header) {
                 if ($front->ajaxMode && !is_array($response)) {
                    $result = array();
                    $result["title"] = $header->getTitleString();
                    $result["meta"] = $header->getMetaTags();
                    $result["html"] = $response;
                    $front->response($result);
                 }
            };', array($front));

        $initialize->addBody('$site = $this->getService(?);', array('site'));
        $initialize->addBody('$this->getService(?)->onError[] =
            function ($front, $e)  use ($site) {
                debugHeader((array)$e->getMessage(), "error-front");
                DI()->getService("logger")->error((array)($e->getMessage()."  [".$site->controller."]"), "front");
            };', array($front));

        $initialize->addBody('$this->getService(?)->onRequest[] =
            function ($front, $paramsRouter) {
                \CMS\Core\Helper\Helpers::checkOptionsRouter($paramsRouter);
            };', array($front));
    }
}
