<?php
namespace Delorius\Exception;

use Delorius\Core\Environment;
use Delorius\Http\IResponse;

class NotFound extends Error
{
    protected $defaultCode = 404;

    public function __construct($message = "", $code = IResponse::S404_NOT_FOUND, \Exception $previous = null) {
        if($code == IResponse::S404_NOT_FOUND)
            $message .= ' ['.Environment::getContext()->getService('url').'] ';
        parent::__construct($message,$code,$previous);
    }
}