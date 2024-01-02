<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnShutdown;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Utils\Json;

/**
 * Class AjaxAttribute
 * @package Delorius\Attribute\Common
 * Example: @Ajax(modeAjax=true,source=data,total=count)
 * Result:  json|throw
 */
class AjaxAttribute extends Attribute implements IAttributeOnStartup,IAttributeOnShutdown {


    const JSON_CONTAINER = 'html';
    const JSON_AJAX = true;


    /**
     * Определяет в каком режиме отдовать в json формате
     * необезательный
     * @var string
     */
    protected $modeAjax;

    public function setParams(array $params = null){
        $this->modeAjax =   isset($params['modeAjax']) ? $params['modeAjax'] : self::JSON_AJAX ;
    }

    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        if($this->modeAjax && !$controller->httpRequest->isAjax()){
            throw new ForbiddenAccess('Not AJAX request');
        }
    }

    function onShutdown(\Delorius\Application\UI\Controller $controller, \Exception $exception = null)
    {
        $controller->httpResponse->setContentType('application/json','UTF-8');
        $controller->layout(null);
        $response = $controller->getResponse();
        $result = array();
        if(!is_array($response)){
            $result[self::JSON_CONTAINER] = (string)$response;
            $controller->response($result);
        }
    }



}
