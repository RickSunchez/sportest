<?php
namespace Delorius\DI\Config;

use Delorius\Utils\Arrays;

/**
 * Configuration helpers.
 */
class Helpers
{
    const EXTENDS_KEY = '_extends',
        OVERWRITE = TRUE;


    /**
     * Merges configurations. Left has higher priority than right one.
     * @return array|string
     */
    public static function merge($left, $right)
    {
        if (is_array($left) && is_array($right) && count($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (is_array($val) && isset($val[self::EXTENDS_KEY])) {
                        if ($val[self::EXTENDS_KEY] === self::OVERWRITE) {
                            unset($val[self::EXTENDS_KEY]);
                        }
                    } elseif (isset($right[$key])) {
                        $val = static::merge($val, $right[$key]);
                    }
                    $right[$key] = $val;
                }
            }
            return $right;

        } elseif ($left === NULL && is_array($right)) {
            return $right;

        } else {
            return $left;
        }
    }


    /**
     * Finds out and removes information about the parent.
     * @return mixed
     */
    public static function takeParent(& $data)
    {
        if (is_array($data) && isset($data[self::EXTENDS_KEY])) {
            $parent = $data[self::EXTENDS_KEY];
            unset($data[self::EXTENDS_KEY]);
            return $parent;
        }
    }


    /**
     * @return bool
     */
    public static function isOverwriting(& $data)
    {
        return is_array($data) && isset($data[self::EXTENDS_KEY]) && $data[self::EXTENDS_KEY] === self::OVERWRITE;
    }


    /**
     * @return bool
     */
    public static function isInheriting(& $data)
    {
        return is_array($data) && isset($data[self::EXTENDS_KEY]) && $data[self::EXTENDS_KEY] !== self::OVERWRITE;
    }


    public static function extend($data, array &$params, $keys = null)
    {
        if (is_array($data)) {

            if ($parent = self::takeParent($data)) {
                $right = Arrays::get($params, $parent, null);
                $data = self::merge($data, $right);
            }

            foreach ($data as $key => $val) {
                $data[$key] = self::extend($val, $params, $keys . '.' . $key);
            }
        }
        return $data;

    }

}