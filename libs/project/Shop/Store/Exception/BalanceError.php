<?php
namespace Shop\Store\Exception;

use Delorius\Exception\Error;

class BalanceError extends Error
{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message,$code,$previous);
    }
}