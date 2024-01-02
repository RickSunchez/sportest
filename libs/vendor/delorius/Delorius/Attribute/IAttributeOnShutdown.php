<?php
namespace Delorius\Attribute;

interface IAttributeOnShutdown {

    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null);

}