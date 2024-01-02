<?php
defined('DELORIUS') or die('access denied');


$router['import_1c'] = array(
    '/import_1c',
    array('_controller' => 'Shop:Store:Import1c:import'),
    array(),
    array('no_sort_get_params'=>1)
);

$router['shop_cart_data'] = array(
    '/shop/cart/{action}.data',
    array('_controller' => 'Shop:Store:Cart:{action}Data'),
    array('action' => '\w+')
);

$router['shop_cart_goods'] = array(
    '/cart/goods.{action}',
    array('_controller' => 'Shop:Store:Cart:{action}Goods'),
    array('action' => '\w+')
);

$router['shop_cart'] = array(
    '/cart/',
    array('_controller' => 'Shop:Store:Cart:list')
);

$router['shop_order_show'] = array(
    '/order/status/{order_code}',
    array('_controller' => 'Shop:Store:Order:show'),
    array('order_code' => '\w+'),
    array(),
    'www'
);

$router['shop_order_data'] = array(
    '/order/{action}.data',
    array('_controller' => 'Shop:Store:Order:{action}Data'),
    array('action' => '\w+')
);

$router['shop_order'] = array(
    '/order/{action}',
    array('_controller' => 'Shop:Store:Order:{action}', array('action' => 'index')),
    array('action' => '\w+')
);

return $router;