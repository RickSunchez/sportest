<?php
namespace Delorius\Tools\Math\Contexts\Scope;

use Delorius\Tools\Math\Contexts\Scope;
use Delorius\Tools\Math\Exceptions\ParsingException;

class Exp extends Scope
{
    public function evaluate()
    {
        if (is_array($result = parent::evaluate())) {
            throw new ParsingException('exp accept only one argument');
        }

        return (float) exp($result);
    }
}
