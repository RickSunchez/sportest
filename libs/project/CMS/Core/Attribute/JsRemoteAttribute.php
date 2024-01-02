<?php
namespace CMS\Core\Attribute;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;


class JsRemoteAttribute extends Attribute implements IAttributeOnShutdown {

    protected $js_remote = array();

    public function setParams(array $params = null){
        $this->js_remote = $params;

    }

    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        $jsFiles = $controller->container->getService('jsFiles');
        foreach($this->js_remote as $key=>$file){
            $jsFiles->addRemoteFile($file);
        }
    }
}
