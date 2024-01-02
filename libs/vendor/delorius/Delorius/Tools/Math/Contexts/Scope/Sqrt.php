<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Sqrt extends Scope
{
    public function evaluate()
    {
        return sqrt(parent::evaluate());
    }
}
