<?php
namespace Delorius\Application;

use Delorius\Application\UI\Controller;

interface IControllerFactory
{
    /**
     * @param string $suffix
     * @return mixed
     */
    function setSuffixAction($suffix);

    /**
     * @param  string $name controller name
     * @return string  Class
     */
    function getControllerClass($name);

    /**
     * @param  string $name controller name
     * @return string Method
     */
    function getControllerMethod($name);

    /**
     * Create new controller instance.
     * @param  string $name controller name
     * @return Controller
     */
    function createController($name);

    /**
     * Расширения методов для контролера
     * @param string $name
     * @param string $behavior
     * @return mixed
     */
    function addBehavior($name, $behavior);

}
