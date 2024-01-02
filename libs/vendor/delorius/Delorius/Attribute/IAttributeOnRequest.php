<?php
namespace Delorius\Attribute;

interface IAttributeOnRequest {

    function onRequest(\Delorius\Application\UI\Controller $controller,array $params = null );

}