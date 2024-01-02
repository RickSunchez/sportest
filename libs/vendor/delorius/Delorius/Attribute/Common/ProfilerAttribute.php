<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Tools\Debug\Profiler;
use Delorius\Tools\Debug\Toolbar;

/**
 * Class ProfilerAttribute
 * @package Delorius\Attribute\Common
 * Example: @Profiler
 * Result:  header data
 */
class ProfilerAttribute extends Attribute implements IAttributeOnStartup,IAttributeOnShutdown {

    protected $token;

    function setParams(array $params = null)
    {
        // TODO: Implement setParams() method.
    }

    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        Profiler::stop($this->token);
        Toolbar::header($this->token);
    }

    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        $this->token = Profiler::start('Attribute',$controller->name);
    }
}
