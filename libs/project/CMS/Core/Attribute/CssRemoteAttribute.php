<?php
namespace CMS\Core\Attribute;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;


class CssRemoteAttribute extends Attribute implements IAttributeOnShutdown {

    protected $css_remote = array();


    public function setParams(array $params = null){
        $this->css_remote = $params;

    }

    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        $cssFiles = $controller->container->getService('cssFiles');
        foreach($this->css_remote as $key=>$file){
            $cssFiles->addRemoteFile($file);
        }
    }
}
