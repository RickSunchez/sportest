<?php
namespace Delorius\Behaviors;

use Delorius\Application\UI\Controller;

class ControllerBehavior extends Behavior
{

    public function events()
    {
        return array(
            'onStartup' => 'startup',
            'onShutdown' => 'shutdown',
            'onResponse' => 'response',
            'onError' => 'error',
        );
    }

    protected function startup(Controller $controller)
    {
    }

    protected function shutdown(Controller $controller)
    {
    }

    protected function response(Controller $controller)
    {
    }

    protected function error(Controller $controller)
    {
    }
}