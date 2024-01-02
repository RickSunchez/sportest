<?php
namespace Delorius\Exception;

use Delorius\Http\IResponse;

class ForbiddenAccess  extends Error
{
    protected $defaultCode = 403;

    public function __construct($message = "", $code = IResponse::S403_FORBIDDEN, \Exception $previous = null) {
        parent::__construct($message,$code,$previous);
    }
}