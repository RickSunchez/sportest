<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Exception\ForbiddenAccess;

/**
 * Class PostAttribute
 * @package Delorius\Attribute\Common
 * Example: @Post(false|true,parser=true|false)   // false = layout = null
 */
class PostAttribute extends Attribute implements IAttributeOnStartup, IAttributeOnShutdown
{

    protected $template = true;
    protected $parser = true;

    function setParams(array $params = null)
    {
        $this->template = $params[0] ? true : false;
        if (isset($params['parser'])) {
            $this->parser = $params['parser'] ? true : false;
        }
    }

    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        if (!$controller->httpRequest->isMethod('POST')) {
            throw new ForbiddenAccess('Not POST request');
        }
    }


    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        if (!$this->template)
            $controller->layout(null);

        $controller->setSite('isParser', $this->parser);
    }
}
