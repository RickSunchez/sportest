<?php
namespace Delorius\Behaviors;


class Helpers
{

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    public static function mergeBehaviors(array $old, array $new)
    {
        foreach ($new as $name => $cnf) {
            if (!$cnf) {
                unset($old[$name]);
            } elseif (is_string($cnf)) {
                $old[$name] = $cnf;
            } elseif (is_array($cnf) && count($cnf)) {
                foreach ($cnf as $field => $value) {
                    if (!$value) {
                        unset($old[$name][$field]);
                    } else {
                        $old[$name][$field] = $value;
                    }
                }
            }
        }
        return $old;
    }

    /**
     * @param array $old
     * @param array $new
     * @return array
     */
    public static function mergeColumns(array $old, array $new)
    {
        foreach ($new as $field => $cnf) {
            if (!count($cnf)) {
                unset($old[$field]);
            } else {
                foreach ($cnf as $name => $value) {
                    $old[$field][$name] = $value;
                }
            }
        }
        return $old;
    }

}