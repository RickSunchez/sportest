<?php
namespace Delorius\Utils;

use Delorius\Core\Environment;

class Json
{
    const FORCE_ARRAY = 1;

    protected static $temp_array;

    public static function encode(array $value, $in_charset = false, $out_charset = 'UTF-8')
    {
        self::$temp_array = $value;
        $json = json_encode(Arrays::iconv($value, $in_charset, $out_charset));
        self::register_last_error();
        return $json;
    }

    public static function decode($json, $options = true)
    {
        return json_decode($json, (bool)($options & self::FORCE_ARRAY));
    }

    public static final function register_last_error()
    {
        if (JSON_ERROR_NONE != ($error = json_last_error_msg())) {
            $logger = Environment::getContext()->getService('logger');
            $logger->error($error, 'json');
            $logger->error(self::$temp_array, 'json');
        }
        self::$temp_array = array();
    }

}
