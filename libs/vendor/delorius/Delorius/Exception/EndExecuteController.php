<?php
namespace Delorius\Exception;

class EndExecuteController extends Error {
    protected $_response;

    public function __construct($message, $response , $code = 0, \Exception $previous = NULL)
    {
        $this->_response = $response;
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(){
        return $this->_response;
    }

}