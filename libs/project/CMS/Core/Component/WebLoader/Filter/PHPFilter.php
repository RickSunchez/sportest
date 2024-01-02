<?php
namespace CMS\Core\Component\WebLoader\Filter;

use Delorius\Core\Environment;

class PHPFilter
{
    /**
     * Invoke filter
     * @param string $code
     * @return string
     */
    public function __invoke($code)
    {
        $code = Environment::getContext()->getService('parser')->code($code);
        return $code;
    }

}