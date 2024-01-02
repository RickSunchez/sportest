<?php
namespace Delorius\Exception;

use Delorius\Http\IResponse;

class BadRequest extends Error
{
    protected $defaultCode = 400;

    public function __construct($message = "", $code = IResponse::S400_BAD_REQUEST, \Exception $previous = null) {
        parent::__construct($message,$code,$previous);
    }
}