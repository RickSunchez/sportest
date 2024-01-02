<?php

namespace Delorius\DI;

use Delorius\Exception\Error;
use Delorius\Exception\InvalidArgument;
use Delorius\PhpGenerator\PhpLiteral;
use Delorius\Utils\Arrays;

/**
 * The DI helpers.
 * @internal
 */
class Helpers
{

    /**
     * Expands %placeholders%.
     * @param  mixed
     * @param  array
     * @param  bool|array
     * @return mixed
     * @throws InvalidArgument
     */
    public static function expand($var, array $params, $recursive = FALSE)
    {
        if (is_array($var)) {
            $res = array();
            foreach ($var as $key => $val) {
                $res[$key] = self::expand($val, $params, $recursive);
            }
            return $res;

        } elseif ($var instanceof Statement) {
            return new Statement(self::expand($var->getEntity(), $params, $recursive), self::expand($var->arguments, $params, $recursive));

        } elseif (!is_string($var)) {
            return $var;
        }

        $parts = preg_split('#%([\w.-]*)%#i', $var, -1, PREG_SPLIT_DELIM_CAPTURE);
        $res = array();
        $php = false;
        foreach ($parts as $n => $part) {
            if ($n % 2 === 0) {
                $res[] = $part;

            } elseif ($part === '') {
                $res[] = '%';

            } elseif (isset($recursive[$part])) {
                throw new InvalidArgument(sprintf('Circular reference detected for variables: %s.', implode(', ', array_keys($recursive))));

            } else {
                try {
                    $val = Arrays::get($params, explode('.', $part));
                } catch (InvalidArgument $e) {
                    throw new InvalidArgument("Missing parameter '$part'.", 0, $e);
                }
                if ($recursive) {
                    $val = self::expand($val, $params, (is_array($recursive) ? $recursive : array()) + array($part => 1));
                }
                if (strlen($part) + 2 === strlen($var)) {
                    return $val;
                }
                if ($val instanceof PhpLiteral) {
                    $php = true;
                } elseif (!is_scalar($val)) {
                    throw new InvalidArgument("Unable to concatenate non-scalar parameter '$part' into '$var'.");
                }
                $res[] = $val;
            }
        }
        if ($php) {
            $res = array_filter($res, function ($val) {
                return $val !== '';
            });
            $res = array_map(function ($val) {
                return $val instanceof PhpLiteral ? "($val)" : var_export((string)$val, true);
            }, $res);
            return new PhpLiteral(implode(' . ', $res));
        }
        return implode('', $res);
    }


    /**
     * Generates list of arguments using autowiring.
     * @return array
     */
    public static function autowireArguments(\ReflectionFunctionAbstract $method, array $arguments, $container)
    {
        $optCount = 0;
        $num = -1;
        $res = array();
        $methodName = ($method instanceof \ReflectionMethod ? $method->getDeclaringClass()->getName() . '::' : '')
            . $method->getName() . '()';

        foreach ($method->getParameters() as $num => $parameter) {
            if (array_key_exists($num, $arguments)) {
                $res[$num] = $arguments[$num];
                unset($arguments[$num]);
                $optCount = 0;

            } elseif (array_key_exists($parameter->getName(), $arguments)) {
                $res[$num] = $arguments[$parameter->getName()];
                unset($arguments[$parameter->getName()]);
                $optCount = 0;

            } elseif (($class = PhpReflection::getParameterType($parameter)) && !PhpReflection::isBuiltinType($class)) {
                $res[$num] = $container->getByType($class, FALSE);
                if ($res[$num] === NULL) {
                    if ($parameter->allowsNull()) {
                        $optCount++;
                    } elseif (class_exists($class) || interface_exists($class)) {
                        throw new Error("Service of type {$class} needed by $methodName not found. Did you register it in configuration file?");
                    } else {
                        throw new Error("Class {$class} needed by $methodName not found. Check type hint and 'use' statements.");
                    }
                } else {
                    if ($container instanceof ContainerBuilder) {
                        $res[$num] = '@' . $res[$num];
                    }
                    $optCount = 0;
                }

            } elseif ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
                // !optional + defaultAvailable = func($a = NULL, $b) since 5.3.17 & 5.4.7
                // optional + !defaultAvailable = i.e. Exception::__construct, mysqli::mysqli, ...
                $res[$num] = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : NULL;
                $optCount++;

            } else {
                throw new Error("Parameter \${$parameter->getName()} in $methodName has no class type hint or default value, so its value must be specified.");
            }
        }

        // extra parameters
        while (array_key_exists(++$num, $arguments)) {
            $res[$num] = $arguments[$num];
            unset($arguments[$num]);
            $optCount = 0;
        }
        if ($arguments) {
            throw new Error("Unable to pass specified arguments to $methodName.");
        }

        return $optCount ? array_slice($res, 0, -$optCount) : $res;
    }

}
