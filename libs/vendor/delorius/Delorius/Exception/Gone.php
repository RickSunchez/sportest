<?php
namespace Delorius\Exception;

use Delorius\Core\Environment;
use Delorius\Http\IResponse;

class Gone extends Error
{
    protected $defaultCode = 410;

    public function __construct($message = "", $code = IResponse::S410_GONE, \Exception $previous = null) {
        if($code == IResponse::S410_GONE)
            $message .= ' ['.Environment::getContext()->getService('url').'] ';
        parent::__construct($message,$code,$previous);
    }
}