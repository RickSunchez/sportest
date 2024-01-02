<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Exception\ForbiddenAccess;

/**
 * Class GetAttribute
 * @package Delorius\Attribute\Common
 * Example: @Get(false|true) // false = layout = null
 */
class GetAttribute extends Attribute implements IAttributeOnStartup,IAttributeOnShutdown{

    protected $template = true;

    function setParams(array $params = null)
    {
        $this->template = $params[0]? true: false;
    }

    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        if(!$controller->httpRequest->isMethod('GET'))
        {
            throw new ForbiddenAccess('Not GET request');
        }
    }


    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        if(!$this->template)
            $controller->layout(null);
    }
}
