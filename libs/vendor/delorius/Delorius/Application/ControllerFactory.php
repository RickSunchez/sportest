<?php
namespace Delorius\Application;

use Delorius\Application\UI\Controller;
use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnRequest;
use Delorius\Attribute\IAttributeOnResponse;
use Delorius\Attribute\IAttributeOnShutdown;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Core\Common;
use Delorius\DI\Container;

class ControllerFactory implements IControllerFactory
{
    /**
     * @var string Name action method{Suffix}()
     */
    protected $suffix = IController::SUFFIX_ACTION;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var array
     */
    protected $behaviors = array();

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    function setSuffixAction($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    function createController($name)
    {
        /** @var Controller $controller */
        $controller = $this->container->createInstance($this->getControllerClass($name));
        $controller->attachBehaviors($this->behaviors);
        # attribute class and method
        $arAttribute = $this->getAnnotations($controller, $this->getControllerMethod($name));
        $collectionAttribute = $this->container->getService('collectionAttribute');
        foreach ($arAttribute as $nameAttribute => $params) {
            $attributeClass = $collectionAttribute->get($nameAttribute);
            if ($attributeClass instanceof Attribute) {
                $attributeClass->setParams((array)$params[0]);
            } else
                continue;

            if ($attributeClass instanceof IAttributeOnStartup) {
                $controller->onStartup[] = callback($attributeClass, 'onStartup');
            }

            if ($attributeClass instanceof IAttributeOnShutdown) {
                $controller->onShutdown[] = callback($attributeClass, 'onShutdown');
            }

            if ($attributeClass instanceof IAttributeOnRequest) {
                $controller->onRequest[] = callback($attributeClass, 'onRequest');
            }

            if ($attributeClass instanceof IAttributeOnResponse) {
                $controller->onResponse[] = callback($attributeClass, 'onResponse');
            }
        }
        return $controller;
    }

    function getControllerClass($name)
    {
        $paramClass = Common::getController($name);
        return $paramClass['class'];
    }

    function getControllerMethod($name)
    {
        $paramClass = Common::getController($name);
        return $paramClass['action'] . $this->suffix;
    }

    function getAnnotations(Controller $controller, $method)
    {
        $arAnnotationClass = $controller->getReflection()->getAnnotations();
        $arAnnotationMethod = $controller->getReflection()->getMethod($method)->getAnnotations();
        return array_merge($arAnnotationClass, $arAnnotationMethod);
    }

    function addBehavior($name, $behavior)
    {
        $this->behaviors[$name] = $behavior;
    }


}
