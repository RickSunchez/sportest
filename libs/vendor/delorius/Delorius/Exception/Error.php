<?php
namespace Delorius\Exception;

use Delorius\Http\IResponse;

class Error extends \Exception
{
    protected $defaultCode = 500;

    public function __construct($message = "", $code = IResponse::S500_INTERNAL_SERVER_ERROR, \Exception $previous = null) {

        if ($code < 200 || $code > 504) {
            $code = $this->defaultCode;
        }
        parent::__construct($message,$code,$previous);
    }

}