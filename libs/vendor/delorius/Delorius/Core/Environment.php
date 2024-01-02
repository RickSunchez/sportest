<?php
namespace Delorius\Core;

use Delorius\DI\Container;
use Delorius\Exception\Error;

class Environment
{
    /**
     * @var Container
     */
    protected static $context;

    /**
     * @return void
     */
    public static function setContext(Container $context)
    {
        self::$context = $context;
    }


    /**
     * @return Container
     */
    public static function getContext()
    {
        if (self::$context === NULL) {
            throw new Error('Dont init Container');
        }
        return self::$context;
    }


    /**
     * @param array $config
     * @param string $field
     * @return array
     */
    public static function countedConfig(array $config, $field = 'id')
    {
        $result = array();
        foreach ($config as $item) {
            $result[$item[$field]] = $item;
        }
        return $result;
    }
}