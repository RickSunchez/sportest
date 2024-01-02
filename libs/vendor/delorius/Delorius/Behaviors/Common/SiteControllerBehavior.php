<?php
namespace Delorius\Behaviors\Common;

use Delorius\Behaviors\ControllerBehavior;
use Delorius\Configure\Site;


class SiteControllerBehavior extends ControllerBehavior
{
    /**
     * @var Site
     * @service site
     * @inject
     */
    public $site;

    /**
     * Установить шаблон для контролера
     * @param string $template
     * @return $this
     */
    public function template($template)
    {
        $this->setSite('template', $template);
        return $this;
    }

    /**
     * Установить макет для контролера
     * @param string $layout
     * @return $this
     */
    public function layout($layout)
    {
        $this->setSite('layout', $layout);
        return $this;
    }

    /**
     * Установить макет для мобильной версии
     * @param string $layout
     * @return $this
     */
    public function mobile($layout)
    {
        $this->setSite('mobile', $layout);
        return $this;
    }

    /**
     * @return string
     */
    public function getRouterName()
    {
        return $this->getSite('router');
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setSite($name, $value)
    {
        $this->site->{$name} = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return null
     */
    public function getSite($name)
    {
        return $this->site->{$name};
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasSite($name)
    {
        return $this->site->isExists($name);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setGUID($value)
    {
        $this->site->GUID = $value;
        $this->site->block('GUID');
        return $this;
    }

    /**
     * @return null
     */
    public function getGUID()
    {
        return $this->site->GUID;
    }


}