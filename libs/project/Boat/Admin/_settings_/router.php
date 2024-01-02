<?php
defined('DELORIUS') or die('access denied');

$router['admin_goods_data'] = array(
    '/shop/goods/{action}.data',
    array('_controller' => 'Boat:Admin:Goods:{action}Data'),
    array('action' => '\w+')
);

$router['admin_goods'] = array(
    '/shop/goods/{action}',
    array('_controller' => 'Boat:Admin:Goods:{action}', array('action' => 'list')),
    array('action' => '\w+')
);


$router['admin_schema_data'] = array(
    '/schema/{action}.data',
    array('_controller' => 'Boat:Admin:Schema:{action}Data'),
    array('action' => '\w+')
);

$router['admin_schema'] = array(
    '/schema/{action}',
    array('_controller' => 'Boat:Admin:Schema:{action}', array('action' => 'list')),
    array('action' => '\w+')
);


return $router;