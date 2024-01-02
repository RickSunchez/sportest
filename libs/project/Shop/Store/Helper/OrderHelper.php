<?php
namespace Shop\Store\Helper;

use Delorius\Core\Environment;

class OrderHelper
{

    /**
     * @return array|mixed
     * @throws \Delorius\Exception\Error
     */
    public static function getStatus()
    {
        $config = Environment::getContext()->getParameters('shop.store.order.status');
        $config = Environment::countedConfig($config);
        return $config;
    }

    /**
     * @return array
     */
    public static function getStatusId()
    {
        $status = self::getStatus();
        $result = array();
        foreach ($status as $item) {
            $result[$item['id']] = $item['name'];
        }

        return $result;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function getStatusById($id)
    {
        $config = self::getStatus();
        return $config[$id];
    }
}