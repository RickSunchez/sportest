<?php
namespace Delorius\Exception;

class OrmValidationError extends Error {

    protected $_error_message = array();
    protected $_error_fields = array();
    protected $_object_name;
    /**
     * Constructs a new exception for the specified model
     *
     * @param  array     $error_message
     * @param  array     $error_fields
     * @param  string     $message     The error message
     * @param  integer    $code        The error code for the exception
     * @return void
     */
    public function __construct($error_message, $error_fields , $message = 'Error margins to changes ', $code = 0, \Exception $previous = NULL)
    {
        $this->_error_message = $error_message;
        $this->_error_fields = $error_fields;

        parent::__construct($message, $code, $previous);
    }

    public function add_object_name($object_name){
        $this->_object_name = $object_name;
    }

    public function getErrorsMessage(){
        return $this->_error_message;
    }

    public function getErrorsFields(){
        return $this->_error_fields;
    }

    public function getObjectName(){
        return $this->_object_name;
    }

}