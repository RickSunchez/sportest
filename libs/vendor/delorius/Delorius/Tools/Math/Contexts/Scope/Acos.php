<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Acos extends Scope
{
    public function evaluate()
    {
        return acos(deg2rad(parent::evaluate()));
    }
}
