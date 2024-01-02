<?php
namespace Delorius\Utils;

use Delorius\Core\Object;
use Closure;
use Delorius\Exception\Error;

abstract class Collection extends Object
{

    /**
     * Check if an array has a given key
     */
    public static function has($array, $key)
    {
        // Generate unique string to use as marker
        $unfound = Strings::random(5);
        return self::get($array, $key, $unfound) !== $unfound;
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////// FETCH FROM ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Get a value from an collection using dot-notation
     *
     * @param array $collection The collection to get from
     * @param string $key The key to look for
     * @param mixed $default Default value to fallback to
     *
     * @return mixed
     */
    public static function get($collection, $key, $default = null)
    {
        if (is_null($key)) {
            return $collection;
        }

        if (is_array($key)) {
            foreach ($key as $k) {
                if (is_array($collection) && array_key_exists($k, $collection)) {
                    $collection = $collection[$k];
                } else {
                    if (func_num_args() < 3) {
                        throw new Error ("Missing item '$k'.");
                    }
                    return $default instanceof Closure ? $default() : $default;
                }
            }
            return $collection;
        }

        if (!is_object($collection) && isset($collection[$key])) {
            return $collection[$key];
        }

        // Crawl through collection, get key according to object or not
        foreach (explode('.', $key) as $segment) {
            // If object
            if (is_object($collection)) {
                if (!isset($collection->{$segment})) {
                    return $default instanceof Closure ? $default() : $default;
                } else {
                    $collection = $collection->$segment;
                }
                // If array
            } else {
                if (!isset($collection[$segment])) {
                    return $default instanceof Closure ? $default() : $default;
                } else {
                    $collection = $collection[$segment];
                }
            }
        }

        return $collection;
    }

    /**
     * Set a value in a collection using dot notation
     *
     * @param mixed $collection The collection
     * @param string $key The key to set
     * @param mixed $value Its value
     *
     * @return mixed
     */
    public static function set($collection, $key, $value)
    {
        self::internalSet($collection, $key, $value);
        return $collection;
    }

    /**
     * Remove a value from an array using dot notation
     */
    public static function remove($collection, $key)
    {
        // Recursive call
        if (is_array($key)) {
            foreach ($key as $k) {
                self::internalRemove($collection, $k);
            }

            return $collection;
        }

        self::internalRemove($collection, $key);
        return $collection;
    }

    /**
     * Fetches all columns $property from a multidimensional array
     */
    public static function pluck($collection, $property)
    {
        $plucked = array_map(function ($value) use ($property) {
            return Arrays::get($value, $property);
        }, (array)$collection);

        // Convert back to object if necessary
        if (is_object($collection)) {
            $plucked = (object)$plucked;
        }

        return $plucked;
    }


    /**
     * Sort a collection by value, by a closure or by a property
     * If the sorter is null, the collection is sorted naturally
     */
    public static function sort($collection, $sorter = null, $direction = 'asc')
    {
        $collection = (array)$collection;

        // Get correct PHP constant for direction
        $direction = (strtolower($direction) == 'desc') ? SORT_DESC : SORT_ASC;

        // Transform all values into their results
        if ($sorter) {
            $results = Arrays::each($collection, function ($value) use ($sorter) {
                return is_callable($sorter) ? $sorter($value) : Arrays::get($value, $sorter);
            });
        } else {
            $results = $collection;
        }

        // Sort by the results and replace by original values
        array_multisort($results, $direction, SORT_REGULAR, $collection);

        return $collection;
    }

    /**
     * Group values from a collection according to the results of a closure
     */
    public static function group($collection, $grouper, Closure $closure = null)
    {
        if (!($collection instanceof \Traversable) && !is_array($collection)) {
            throw new Error('Traversed item is not an array');
        }
        $result = array();
        // Iterate over values, group by property/results from closure
        foreach ($collection as $key => $value) {
            $key = is_callable($grouper) ? $grouper($value, $key) : Arrays::get($value, $grouper);
            if (!isset($result[$key])) {
                $result[$key] = array();
            }
            // Add to results
            $result[$key][] = is_callable($closure) && $closure != null ? $closure($value, $key) : $value;
        }

        return $result;
    }

    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// HELPERS /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Internal mechanic of set method
     */
    protected static function internalSet(&$collection, $key, $value)
    {
        if (is_null($key)) {
            return $collection = $value;
        }

        // Explode the keys
        $keys = explode('.', $key);

        // Crawl through the keys
        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If we're dealing with an object
            if (is_object($collection)) {
                if (!isset($collection->$key) or !is_array($collection->$key)) {
                    $collection->$key = array();
                }
                $collection = & $collection->$key;
                // If we're dealing with an array
            } else {
                if (!isset($collection[$key]) or !is_array($collection[$key])) {
                    $collection[$key] = array();
                }
                $collection = & $collection[$key];
            }
        }

        // Bind final tree on the collection
        $key = array_shift($keys);
        if (is_array($collection)) {
            $collection[$key] = $value;
        } else {
            $collection->$key = $value;
        }
    }

    /**
     * Internal mechanics of remove method
     */
    protected static function internalRemove(&$collection, $key)
    {
        // Explode keys
        $keys = explode('.', $key);

        // Crawl though the keys
        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If we're dealing with an object
            if (is_object($collection)) {
                if (!isset($collection->$key)) {
                    return false;
                }
                $collection = & $collection->$key;
                // If we're dealing with an array
            } else {
                if (!isset($collection[$key])) {
                    return false;
                }
                $collection = & $collection[$key];
            }
        }

        $key = array_shift($keys);
        if (is_object($collection)) {
            unset($collection->$key);
        } else {
            unset($collection[$key]);
        }
    }


    /**
     * Filters an array of objects (or a numeric array of associative arrays) based on the value of a particular property within that
     *
     * @param string $property
     * @param string $value
     * @param string $comparisonOp
     */
    public static function filterBy($collection, $property, $value, $comparisonOp = null)
    {
        if (!$comparisonOp) {
            $comparisonOp = is_array($value) ? 'contains' : 'eq';
        }
        $ops = array(
            'eq' => function ($item, $prop, $value) {
                    return $item[$prop] === $value;
                },
            'gt' => function ($item, $prop, $value) {
                    return $item[$prop] > $value;
                },
            'gte' => function ($item, $prop, $value) {
                    return $item[$prop] >= $value;
                },
            'lt' => function ($item, $prop, $value) {
                    return $item[$prop] < $value;
                },
            'lte' => function ($item, $prop, $value) {
                    return $item[$prop] <= $value;
                },
            'ne' => function ($item, $prop, $value) {
                    return $item[$prop] !== $value;
                },
            'contains' => function ($item, $prop, $value) {
                    return in_array($item[$prop], (array)$value);
                },
            'notContains' => function ($item, $prop, $value) {
                    return !in_array($item[$prop], (array)$value);
                },
            'newer' => function ($item, $prop, $value) {
                    return strtotime($item[$prop]) > strtotime($value);
                },
            'older' => function ($item, $prop, $value) {
                    return strtotime($item[$prop]) < strtotime($value);
                },
        );
        $result = array_values(array_filter((array)$collection, function ($item) use (
            $property,
            $value,
            $ops,
            $comparisonOp
        ) {
            $item = (array)$item;
            if (!isset($item[$property])) {
                $item[$property] = null;
            }

            return $ops[$comparisonOp]($item, $property, $value);
        }));
        if (is_object($collection)) {
            $result = (object)$result;
        }

        return $result;
    }

    public static function findBy($collection, $property, $value, $comparisonOp = "eq")
    {
        $filtered = self::filterBy($collection, $property, $value, $comparisonOp);
        return Arrays::first($filtered);
    }


} 