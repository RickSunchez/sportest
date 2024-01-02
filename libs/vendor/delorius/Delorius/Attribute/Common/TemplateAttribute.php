<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Utils\Strings;

/**
 * Class TemplateAttribute
 * @package Delorius\Attribute\Common
 *
 * Example : @Template(
 *                      name=default,
 *                      layout=index,
 *                      mobile=false
 *          )
 * Result:
 *          $this->name = default
 *          $this->layout = index
 *          $this->mobile = false
 */
class TemplateAttribute extends Attribute implements IAttributeOnStartup
{
    /**
     * @var string шаблон для мобильника
     */
    protected $mobile;
    /**
     * @var string названия шаблона
     */
    protected $name;
    /**
     * @var string слой в которой обернуть ответ
     */
    protected $layout;

    public function setParams(array $params = null)
    {
        $this->name = isset($params['name']) ? $params['name'] : false;
        $this->layout = isset($params['layout']) ? $params['layout'] : false;
        $this->mobile = isset($params['mobile']) ? $params['mobile'] : false;
    }


    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        if (!is_bool($this->name))
            $controller->site->template = $this->name;

        if (!is_bool($this->layout))
            $controller->site->layout = $this->layout;

        if (!is_bool($this->mobile))
            $controller->site->mobile = $this->mobile;
    }

}
