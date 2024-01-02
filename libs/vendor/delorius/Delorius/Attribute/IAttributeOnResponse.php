<?php
namespace Delorius\Attribute;

interface IAttributeOnResponse {

    function onResponse(\Delorius\Application\UI\Controller $controller, array $params = null, $response);

}