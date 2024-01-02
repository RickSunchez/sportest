<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Tangent extends Scope
{
    public function evaluate()
    {
        return tan(deg2rad(parent::evaluate()));
    }
}
