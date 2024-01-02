<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Sum extends Scope
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result)) {
            return $result;
        }

        return array_sum($result);
    }
}
