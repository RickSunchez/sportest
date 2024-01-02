<?php
namespace Delorius\Routing;

use Delorius\Core\Common;

final class RouterParameters {

    /** @var  array  */
    private $options;

    /** @var  string */
    private $name;

    /** @var  string */
    private $controller;

    /** @var  array */
    private $params = array();

    public function __construct(array $parameters){
        $this->controller = $parameters['_controller'];
        $this->name = $parameters['_name'];
        $this->options = (array)$parameters['_options'];
        $this->params = (array)$parameters['_params'];
    }

    public function getParams(){
        return $this->params;
    }

    public function getOptions(){
        return $this->options;
    }

    public function getName(){
        return $this->name;
    }

    public function getController(){
        return $this->controller;
    }
}