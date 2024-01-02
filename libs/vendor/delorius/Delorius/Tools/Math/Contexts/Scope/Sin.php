<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Sin extends Scope
{
    public function evaluate()
    {
        return sin(deg2rad(parent::evaluate()));
    }
}
