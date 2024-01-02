<?php
namespace Delorius\DI\Extensions;

use Delorius\DI\CompilerExtension;
use Delorius\DI\Container;
use Delorius\DI\PhpReflection;
use Delorius\Exception\InvalidArgument;
use Delorius\Exception\InvalidState;


/**
 * Calls inject methods and fills @inject properties.
 */
class InjectExtension extends CompilerExtension
{
    const TAG_INJECT = 'inject';


    public function beforeCompile()
    {
        foreach ($this->getContainerBuilder()->getDefinitions() as $def) {
            if ($def->getTag(self::TAG_INJECT) && $def->getClass()) {
                $this->updateDefinition($def);
            }
        }
    }


    private function updateDefinition($def)
    {
        $class = $def->getClass();
        $builder = $this->getContainerBuilder();
        $injects = array();
        foreach (self::getInjectProperties($class) as $property => $type) {
            self::checkType($class, $property, $type, $builder);
            $injects[] = new \Delorius\DI\Statement('$' . $property, array('@\\' . ltrim($type, '\\')));
        }

        foreach (self::getInjectMethods($def->getClass()) as $method) {
            $injects[] = new \Delorius\DI\Statement($method);
        }

        $setups = $def->getSetup();
        foreach ($injects as $inject) {
            foreach ($setups as $key => $setup) {
                if ($setup->getEntity() === $inject->getEntity()) {
                    $inject = $setup;
                    unset($setups[$key]);
                }
            }
            array_unshift($setups, $inject);
        }
        $def->setSetup($setups);
    }


    /**
     * Generates list of inject methods.
     * @return array
     * @internal
     */
    public static function getInjectMethods($class)
    {
        return array_values(array_filter(get_class_methods($class), function ($name) {
            return substr($name, 0, 6) === 'inject';
        }));
    }


    /**
     * Generates list of properties with annotation @inject.
     * @return array
     * @internal
     */
    public static function getInjectProperties($class)
    {
        $res = array();
        foreach (get_class_vars($class) as $name => $foo) {
            $rp = new \ReflectionProperty($class, $name);
            if (PhpReflection::parseAnnotation($rp, 'inject') !== NULL) {
                if ($type = PhpReflection::parseAnnotation($rp, 'var')) {
                    $type = PhpReflection::expandClassName($type, PhpReflection::getDeclaringClass($rp));
                } else if ($service = PhpReflection::parseAnnotation($rp, 'service')) {
                    $type = $service;
                }
                $res[$name] = $type;

            }
        }
        return $res;
    }


    /**
     * Calls all methods starting with with "inject" using autowiring.
     * @return void
     */
    public static function callInjects(\Delorius\DI\Container $container, $service)
    {
        if (!is_object($service)) {
            throw new InvalidArgument(sprintf('Service must be object, %s given.', gettype($service)));
        }

        foreach (array_reverse(self::getInjectMethods($service)) as $method) {
            $container->callMethod(array($service, $method));
        }

        foreach (self::getInjectProperties(get_class($service)) as $property => $type) {
            self::checkType($service, $property, $type, $container);
            if (class_exists($type) || interface_exists($type)) {
                $service->$property = $container->getByType($type);
            } else {
                $service->$property = $container->getService($type);
            }
        }
    }


    /** @internal */
    private static function checkType($class, $name, $type, Container $container)
    {
        $rc = PhpReflection::getDeclaringClass(new \ReflectionProperty($class, $name));
        $fullname = $rc->getName() . '::$' . $name;
        if (!$type) {
            throw new InvalidState("Property $fullname has no @var annotation.");
        } elseif ($container->hasService($type)) {
            return;
        } elseif (!class_exists($type) && !interface_exists($type)) {
            throw new InvalidState("Class or interface '$type' used in @var annotation at $fullname not found. Check annotation and 'use' statements.");
        } elseif (!$container->getByType($type, FALSE)) {
            throw new InvalidState("Service of type {$type} used in @var annotation at $fullname not found. Did you register it in configuration file?");
        }
    }

}
