<?php
namespace Delorius\Configure;

use Delorius\Core\ArrayObject;
use Delorius\Utils\Arrays;

/*
 * для сбора временных динамически изменяемы данных
 * шаблон, слой и другие промежуточные данные
 */

Class Site extends ArrayObject
{
    public function add($key, $value)
    {
        if (!is_array($this->__vars[$key])) {
            $this->__vars[$key] = array();
        }
        $this->__vars[$key][] = $value;
    }


    public function getParameters($key = null, $default = null)
    {
        if ($key == null) {
            return $this->__vars;
        } else {
            return Arrays::get($this->__vars, $key, $default);
        }
    }

}