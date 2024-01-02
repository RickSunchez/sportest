<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Cosin extends Scope
{
    public function evaluate()
    {
        return cos(deg2rad(parent::evaluate()));
    }
}
