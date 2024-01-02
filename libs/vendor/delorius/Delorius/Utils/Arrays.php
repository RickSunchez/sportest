<?php

namespace Delorius\Utils;

use Delorius\Exception\Error;
use Closure;

/**
 * Array tools library.
 *
 */
final class Arrays extends Collection
{

    /**
     * Static class - cannot be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Returns reference to array item or $default if item is not set.
     * @param  mixed  array
     * @param  mixed  key
     * @return mixed
     */
    public static function & getRef(& $arr, $key)
    {
        foreach (is_array($key) ? $key : array($key) as $k) {
            if (is_array($arr) || $arr === NULL) {
                $arr = &$arr[$k];
            } else {
                throw new Error ('Traversed item is not an array.');
            }
        }
        return $arr;
    }


    /**
     * Recursively appends elements of remaining keys from the second array to the first.
     * @param  array
     * @param  array
     * @return array
     */
    public static function mergeTree($arr1, $arr2)
    {
        $res = $arr1 + $arr2;
        foreach (array_intersect_key($arr1, $arr2) as $k => $v) {
            if (is_array($v) && is_array($arr2[$k])) {
                $res[$k] = self::mergeTree($v, $arr2[$k]);
            }
        }
        return $res;
    }

    /**
     * Return all the keys of an array
     * @param array $arrays
     * @return array
     */
    public static function keys($arrays)
    {
        return array_keys($arrays);
    }

    /**
     * Searches the array for a given key and returns the offset if successful.
     * @param  array  input array
     * @param  mixed  key
     * @return int    offset if it is found, FALSE otherwise
     */
    public static function searchKey($arr, $key)
    {
        $foo = array($key => NULL);
        return array_search(key($foo), array_keys($arr), TRUE);
    }

    /**
     * Searches to array all keys
     * @param string $keys key|key2|key3
     * @param array $search
     * @return bool
     */
    public static function keysExists($keys, $search)
    {
        $keys_r = explode('|', $keys);
        foreach ($keys_r as $key)
            if (!array_key_exists($key, $search))
                return false;
        return true;
    }

    /**
     * Searches to array anyone key
     * @param string $keys key|key2|key3
     * @param array $search
     * @return bool
     */
    public static function keysAnyoneExists($keys, $search)
    {
        $keys_r = explode('|', $keys);
        foreach ($keys_r as $key)
            if (array_key_exists($key, $search))
                return true;
        return false;
    }


    /**
     * Inserts new array before item specified by key.
     * @param  array  input array
     * @param  mixed  key
     * @param  array  inserted array
     * @return void
     */
    public static function insertBefore(array &$arr, $key, array $inserted)
    {
        $offset = self::searchKey($arr, $key);
        $arr = array_slice($arr, 0, $offset, TRUE) + $inserted + array_slice($arr, $offset, count($arr), TRUE);
    }


    /**
     * Inserts new array after item specified by key.
     * @param  array  input array
     * @param  mixed  key
     * @param  array  inserted array
     * @return void
     */
    public static function insertAfter(array &$arr, $key, array $inserted)
    {
        $offset = self::searchKey($arr, $key);
        $offset = $offset === FALSE ? count($arr) : $offset + 1;
        $arr = array_slice($arr, 0, $offset, TRUE) + $inserted + array_slice($arr, $offset, count($arr), TRUE);
    }

    /**
     * Renames key in array.
     * @param  array $arr
     * @param  mixed $oldKey
     * @param  mixed $newKey
     * @return void
     */
    public static function renameKey(array &$arr, $oldKey, $newKey)
    {
        $offset = self::searchKey($arr, $oldKey);
        if ($offset !== FALSE) {
            $keys = array_keys($arr);
            $keys[$offset] = $newKey;
            $arr = array_combine($keys, $arr);
        }
    }

    /**
     * Normalizes to associative array.
     * @return array
     */
    public static function normalize(array $arr, $filling = NULL)
    {
        $res = array();
        foreach ($arr as $k => $v) {
            $res[is_int($k) ? $v : $k] = is_int($k) ? $filling : $v;
        }
        return $res;
    }

    /**
     * Returns array entries that match the pattern.
     * @param  array
     * @param  string
     * @param  int
     * @return array
     */
    public static function grep(array $arr, $pattern, $flags = 0)
    {
        $res = preg_grep($pattern, $arr, $flags);
        return $res;
    }

    public static function iconv($var, $in_charset = false, $out_charset = 'UTF-8')
    {
        if (!$in_charset || !is_string($in_charset))
            return $var;

        if (is_array($var)) {
            $new = array();
            foreach ($var as $k => $v) {
                $new[Arrays::iconv($k, $in_charset, $out_charset)] = Arrays::iconv($v, $in_charset, $out_charset);
            }
            $var = $new;
        } elseif (is_object($var)) {
            $vars = get_object_vars($var);
            foreach ($vars as $m => $v) {
                $var->$m = Arrays::iconv($v, $in_charset, $out_charset);
            }
        } elseif (is_string($var)) {
            $var = iconv($in_charset, $out_charset, $var);
        }
        return $var;
    }

    /** @return array(columnId=>id,columnName=>name) <= array(id=>name,id=>name,id=>name) */
    public static function dataKeyValue($array, $columnId = 'id', $columnName = 'name', $assoc = true)
    {
        return self::each($array, function ($value, $key) use ($columnId, $columnName) {
            return array(
                $columnId => $key,
                $columnName => $value
            );
        }, $assoc);
    }

    /**
     * @param \Delorius\DataBase\Result $result
     * @return array
     */
    public static function resultAsArray(\Delorius\DataBase\Result $result, $isArray = true)
    {
        return self::each($result, function ($value, $key) use ($isArray) {
            return $isArray ? is_array($value) ? $value : $value->as_array() : $value;
        });
    }

    /**
     * @param \Delorius\DataBase\Result $result
     * @return array
     */
    public static function resultAsArrayKey(\Delorius\DataBase\Result $result, $colname, $isArray = false)
    {
        $arr = array();
        foreach ($result as $item) {

            if (is_array($item)) {
                $arr[$item[$colname]] = $item;
            } else {
                $arr[$item->{$colname}] = $isArray ? $item->as_array() : $item;
            }

        }
        $result = null;
        return $arr;
    }

    /**
     * Очистить массив от пустых значений
     * @param array $arr
     * @return array
     */
    public static function cleatOfNull($arr)
    {
        foreach ($arr as $key => $value) {
            if (!is_scalar($value) || $value == null) {
                unset($arr[$key]);
            }
            if (is_array($value)) {
                $arr[$key] = self::cleatOfNull($value);
            }
        }
        return count($arr) ? $arr : array();
    }


    /**************************************************************
     * http://anahkiasen.github.io/underscore-php/#Arrays-initial *
     * ************************************************************/

    /**
     * Iterate over an array and modify the array's value
     */
    public static function each($array, \Closure $closure, $assoc = true)
    {
        $arr = array();
        foreach ($array as $key => $value) {
            if ($assoc)
                $arr[$key] = $closure($value, $key);
            else
                $arr[] = $closure($value, $key);
        }
        $array = null;
        return $arr;
    }


    /**
     * Generate an array from a range
     *
     * @param integer $_base The base number
     * @param integer $stop The stopping point
     * @param integer $step How many to increment of
     *
     * @return array
     */
    public static function range($_base, $stop = null, $step = 1)
    {
        // Dynamic arguments
        if (!is_null($stop)) {
            $start = $_base;
        } else {
            $start = 1;
            $stop = $_base;
        }
        return range($start, $stop, $step);
    }

    /**
     * Fill an array with $times times some $data
     *
     * @param mixed $data
     * @param integer $times
     *
     * @return array
     */
    public static function repeat($data, $times)
    {
        $times = abs($times);
        if ($times == 0) {
            return array();
        }
        return array_fill(0, $times, $data);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ANALYZE //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Search for the index of a value in an array
     */
    public static function search($array, $value)
    {
        return array_search($value, $array);
    }

    /**
     * Check if all items in an array match a truth test
     */
    public static function matches($array, Closure $closure)
    {
        // Reduce the array to only booleans
        $array = (array)Arrays::each($array, $closure);

        // Check the results
        if (sizeof($array) === 0) {
            return true;
        }
        $array = array_search(false, $array, false);
        return is_bool($array);
    }

    /**
     * Check if any item in an array matches a truth test
     */
    public static function matchesAny($array, Closure $closure)
    {
        // Reduce the array to only booleans
        $array = (array)Arrays::each($array, $closure);

        // Check the results
        if (sizeof($array) === 0) {
            return true;
        }
        $array = array_search(true, $array, false);
        return is_int($array);
    }

    /**
     * Check if an item is in an array
     */
    public static function contains($array, $value)
    {
        return in_array($value, $array);
    }

    /**
     * Returns the average value of an array
     *
     * @param array $array The source array
     * @param integer $decimals The number of decimals to return
     *
     * @return integer The average value
     */
    public static function average($array, $decimals = 0)
    {
        return round((array_sum($array) / sizeof($array)), $decimals);
    }

    /**
     * Get the size of an array
     */
    public static function size($array)
    {
        return sizeof($array);
    }

    /**
     * Get the max value from an array
     */
    public static function max($array, $closure = null)
    {
        // If we have a closure, apply it to the array
        if ($closure) {
            $array = Arrays::each($array, $closure);
        }
        return max($array);
    }

    /**
     * Get the min value from an array
     */
    public static function min($array, $closure = null)
    {
        // If we have a closure, apply it to the array
        if ($closure) {
            $array = Arrays::each($array, $closure);
        }
        return min($array);
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// FETCH FROM ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Find the first item in an array that passes the truth test
     */
    public static function find($array, Closure $closure)
    {
        foreach ($array as $key => $value) {
            if ($closure($value, $key)) {
                return $value;
            }
        }
    }

    /**
     * Clean all false values from an array
     */
    public static function clean($array)
    {
        return Arrays::filter($array, function ($value) {
            return (bool)$value;
        });
    }

    /**
     * Get a random string from an array
     */
    public static function random($array, $take = null)
    {
        if (!$take) {
            return $array[array_rand($array)];
        }
        shuffle($array);
        return Arrays::first($array, $take);
    }

    /**
     * Return an array without all instances of certain values
     */
    public static function without()
    {
        $arguments = func_get_args();
        $array = array_shift($arguments);
        // if singular argument and is an array treat this AS the array to run without agains
        if (is_array($arguments[0]) && count($arguments) === 1) {
            $arguments = $arguments[0];
        }
        return Arrays::filter($array, function ($value) use ($arguments) {
            return !in_array($value, $arguments);
        });
    }

    /**
     * Return an array with all elements found in both input arrays
     */
    public static function intersection($a, $b)
    {
        $a = (array)$a;
        $b = (array)$b;
        return array_values(array_intersect($a, $b));
    }

    /**
     * Return a boolean flag which indicates whether the two input arrays have any common elements
     */
    public static function intersects($a, $b)
    {
        $a = (array)$a;
        $b = (array)$b;
        return count(self::intersection($a, $b)) > 0;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// SLICERS //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Get the first value from an array
     */
    public static function first($array, $take = null)
    {
        if (!$take) {
            return array_shift($array);
        }
        return array_splice($array, 0, $take, true);
    }

    /**
     * Get the last value from an array
     */
    public static function last($array, $take = null)
    {
        if (!$take) {
            return array_pop($array);
        }
        return Arrays::rest($array, -$take);
    }

    /**
     * Get everything but the last $to items
     */
    public static function initial($array, $to = 1)
    {
        $slice = sizeof($array) - $to;
        return Arrays::first($array, $slice);
    }

    /**
     * Get the last elements from index $from
     */
    public static function rest($array, $from = 1)
    {
        return array_splice($array, $from);
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// ACT UPON /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Iterate over an array and execute a callback for each loop
     */
    public static function at($array, Closure $closure)
    {
        foreach ($array as $key => $value) {
            $closure($value, $key);
        }
        return $array;
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// ALTER ///////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Replace a value in an array
     *
     * @param array $array The array
     * @param string $replace The string to replace
     * @param string $with What to replace it with
     *
     * @return array
     */
    public static function replaceValue($array, $replace, $with)
    {
        return Arrays::each($array, function ($value) use ($replace, $with) {
            return str_replace($replace, $with, $value);
        });
    }

    /**
     * Replace the keys in an array with another set
     *
     * @param array $array The array
     * @param array $keys An array of keys matching the array's size
     *
     * @return array
     */
    public static function replaceKeys($array, $keys)
    {
        $values = array_values($array);
        return array_combine($keys, $values);
    }

    /**
     * Shuffle an array
     */
    public static function shuffle($array)
    {
        shuffle($array);
        return $array;
    }

    /**
     * Sort an array by key
     */
    public static function sortKeys($array, $direction = 'ASC')
    {
        $direction = (strtolower($direction) == 'desc') ? SORT_DESC : SORT_ASC;
        if ($direction == SORT_ASC) {
            ksort($array);
        } else {
            krsort($array);
        }
        return $array;
    }

    /**
     * Implodes an array
     *
     * @param array $array The array
     * @param string $with What to implode it with
     *
     * @return String
     */
    public static function implode($array, $with = '')
    {
        return implode($with, $array);
    }

    /**
     * Find all items in an array that pass the truth test
     * @param $array
     * @param null $closure
     * @return array
     */
    public static function filter($array, $closure = null)
    {
        if (!$closure) {
            return Arrays::clean($array);
        }
        return array_filter($array, $closure);
    }

    /**
     * Find all items in an array that pass the truth test value|key
     * @param $array
     * @param Closure $closure
     * @return array
     */
    public static function filterByValueKey($array, Closure $closure)
    {
        $filtered = array();
        foreach ($array as $key => $value) {
            if ($closure($value, $key)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Flattens an array to dot notation
     *
     * @param array $array An array
     * @param string $separator The characater to flatten with
     * @param string $parent The parent passed to the child (private)
     *
     * @return array Flattened array to one level
     */
    public static function flatten($array, $separator = '.', $parent = null)
    {
        if (!is_array($array)) {
            return $array;
        }

        $_flattened = array();

        // Rewrite keys
        foreach ($array as $key => $value) {
            if ($parent) {
                $key = $parent . $separator . $key;
            }
            $_flattened[$key] = Arrays::flatten($value, $separator, $key);
        }

        // Flatten
        $flattened = array();
        foreach ($_flattened as $key => $value) {
            if (is_array($value)) {
                $flattened = array_merge($flattened, $value);
            } else {
                $flattened[$key] = $value;
            }
        }
        return $flattened;
    }

    /**
     * Finds whether a variable is a zero-based integer indexed array.
     * @return bool
     */
    public static function isList($value)
    {
        return is_array($value) && (!$value || array_keys($value) === range(0, count($value) - 1));
    }

    /**
     * Invoke a function on all of an array's values
     */
    public static function invoke($array, $callable, $arguments = array())
    {
        // If one argument given for each iteration, create an array for it
        if (!is_array($arguments)) {
            $arguments = Arrays::repeat($arguments, sizeof($array));
        }

        // If the callable has arguments, pass them
        if ($arguments) {
            return array_map($callable, $array, $arguments);
        }

        return array_map($callable, $array);
    }

    /**
     * Return all items that fail the truth test
     */
    public static function reject($array, Closure $closure)
    {
        $filtered = array();
        foreach ($array as $key => $value) {
            if (!$closure($value, $key)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * Remove the first value from an array
     */
    public static function removeFirst($array)
    {
        array_shift($array);
        return $array;
    }

    /**
     * Remove the last value from an array
     */
    public static function removeLast($array)
    {
        array_pop($array);
        return $array;
    }

    /**
     * Removes a particular value from an array (numeric or associative)
     *
     * @param string $array
     * @param string $value
     *
     * @return array
     */
    public static function removeValue($array, $value)
    {
        $isNumericArray = true;
        foreach ($array as $key => $item) {
            if ($item === $value) {
                if (!is_integer($key)) {
                    $isNumericArray = false;
                }
                unset($array[$key]);
            }
        }
        if ($isNumericArray) {
            $array = array_values($array);
        }
        return $array;
    }

    /**
     * Prepend a value to an array
     */
    public static function prepend($array, $value)
    {
        array_unshift($array, $value);
        return $array;
    }

    /**
     * Append a value to an array
     */
    public static function append($array, $value)
    {
        array_push($array, $value);
        return $array;
    }

}
