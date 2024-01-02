<?php

namespace CMS\Core\Helper;

use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;

/**
 * Class ParserString
 * @package CMS\Core\Helper
 *
 * {name} или {product.name}
 *
 */
class ParserString
{

    const PATTERN = '#\{([0-9a-z\_\.]+)\}#';

    protected $_values = array();

    public function __construct($data = array())
    {
        $this->_values = $data;
    }

    public function setKey($name, $value)
    {
        $this->_values[$name] = $value;
        return $this;
    }

    public function render($subject)
    {
        $result = Strings::matchAll($subject, self::PATTERN);

        if (count($result) == 0) {
            return $subject;
        }

        foreach ($result as $item) {
            $patterns['#\{' . $item[1] . '\}#'] = Arrays::get($this->_values, $item[1]);
        }

        $subject = Strings::replace($subject, $patterns);
        return $subject;
    }
} 