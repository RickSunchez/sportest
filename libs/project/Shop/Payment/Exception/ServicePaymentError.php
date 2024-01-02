<?php
namespace Shop\Payment\Exception;

use Delorius\Exception\Error;

class ServicePaymentError extends Error
{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message,$code,$previous);
    }
}