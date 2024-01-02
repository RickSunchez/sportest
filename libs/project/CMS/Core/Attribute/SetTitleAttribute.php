<?php
namespace CMS\Core\Attribute;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Utils\Strings;

/**
 * Class SetTitleAttribute
 *
 * Example : @SetTitle Новый титле, который можно будет поменять в процессе работы контролера #rouret_name?param=123&para2=222
 */

class SetTitleAttribute extends Attribute implements IAttributeOnStartup {

    /** @var  string установка заголовка сайта */
    protected $title ;
    /** @var  string установка названия роутера */
    private $router;

    public function setParams(array $params = null){
        list($this->title,$this->router) = explode('#',$params[0]);
    }


    function onStartup(\Delorius\Application\UI\Controller $controller){
        if($this->title){
            $header = $controller->getHeader();
            $header->setTitle(trim($this->title));
            /** @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder $breadCrumbs */
            $breadCrumbs = $controller->container->getService('breadCrumbs');
            if($this->router != null )
                $breadCrumbs->addLink(trim($this->title),$this->router);
            else{
                $breadCrumbs->setLastItem($this->title);
            }
        }
    }

}
