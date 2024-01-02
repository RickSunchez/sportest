<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;

class Avg extends Scope
{
    public function evaluate()
    {
        $result = parent::evaluate();
        if (!is_array($result) || empty($result)) {
            return $result;
        }

        $count = count($result);
        return array_sum($result)/$count;
    }
}
