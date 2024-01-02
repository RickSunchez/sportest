<?php
namespace Delorius\Behaviors\Common;

use Delorius\Behaviors\ControllerBehavior;
use Delorius\Http\Session;

class FlashControllerBehavior extends ControllerBehavior
{
    /**
     * @var Session
     * @service session
     * @inject
     */
    public $session;


    /**
     * Мгновения сообщения, сохранятеся в течении 1 минуты или при первом вызове
     * Flash
     */
    protected function _getFlashHandler()
    {
        return $this->session->getSection('flash');
    }

    /**
     * Установить мгновения сообщения
     * @param string $name
     * @param mixed $value
     */
    public function setFlash($name, $value)
    {
        $flash = $this->_getFlashHandler();
        $flash[$name] = $value;
        $flash->setExpiration('+ 1 day', $name);
    }

    /**
     * Проверить наличия мгновения сообщения
     * @param string $name
     * @return bool
     */
    public function hasFlash($name)
    {
        $flash = $this->_getFlashHandler();
        return isset($flash[$name]) ? true : false;
    }

    /**
     * Получить мгновения сообщения
     * @param string $name
     * @return mixed
     */
    public function getFlash($name)
    {
        $flash = $this->_getFlashHandler();
        $value = $flash[$name];
        unset($flash[$name]);
        return $value;
    }


}