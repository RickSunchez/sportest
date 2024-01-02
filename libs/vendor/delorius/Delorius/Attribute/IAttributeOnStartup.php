<?php
namespace Delorius\Attribute;

interface IAttributeOnStartup {

    function onStartup(\Delorius\Application\UI\Controller $controller);

}