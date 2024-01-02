<?php
namespace CMS\Core\Behaviors;

use Delorius\Behaviors\Behavior;
use Delorius\Tools\ILogger;

class LoggerBehavior extends Behavior implements ILogger
{

    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;


    public function info($message, $status)
    {
        $this->_logger->info($message, $status);
    }

    public function warning($message, $status)
    {
        $this->_logger->warning($message, $status);
    }

    public function error($message, $status)
    {
        $this->_logger->error($message, $status);
    }

    public function alert($message, $status)
    {
        $this->_logger->alert($message, $status);
    }

    public function debug($message, $status)
    {
        $this->_logger->debug($message, $status);
    }

    public function critical($message, $status)
    {
        $this->_logger->critical($message, $status);
    }
}