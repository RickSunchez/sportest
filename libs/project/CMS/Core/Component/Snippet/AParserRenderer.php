<?php
namespace CMS\Core\Component\Snippet;

use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\View\Html;

abstract class AParserRenderer extends Object
{
    /**
     * @var \Delorius\Tools\ILogger
     */
    private $logger;
    /**
     * @var \Delorius\Http\Url
     */
    private $url;
    /** @var  mixed */
    protected $path;
    /** @var array */
    protected $query = array();

    public function __construct($path, array $query = array())
    {
        $env = Environment::getContext();
        $this->path = $path;
        $this->query = $query;
        $this->logger = $env->getService('logger');
        $this->url = $env->getService('url');
    }

    public function error($msg)
    {
        $this->logger->error(_sf('{0} (class:{1}, url:{2})', $msg, get_class($this), $this->url), 'parser');
    }

    public function before(){
        /** some code ... */
    }

    /** @var string|Html */
    abstract function render();

}