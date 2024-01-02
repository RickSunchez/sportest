<?php
namespace Delorius\Exception;

use Delorius\Http\IResponse;

class Deprecated extends Error
{
    protected $defaultCode = 500;

    public function __construct($message = "", $code = IResponse::S500_INTERNAL_SERVER_ERROR, \Exception $previous = null) {

        parent::__construct($message,$code,$previous);
    }
}