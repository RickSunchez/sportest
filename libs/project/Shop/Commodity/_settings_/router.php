<?php
defined('DELORIUS') or die('access denied');


$router['shop_goods'] = array(
    '/product/{url}~{id}/',
    array('_controller' => 'Shop:Commodity:Goods:show'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['goods_review_data'] = array(
    '/product/review/{action}.data',
    array('_controller' => 'Shop:Commodity:Review:{action}Data'),
    array(),
    array(),
    'www'
);

$router['shop_collection'] = array(
    '/collection/{url}~{id}/',
    array('_controller' => 'Shop:Commodity:Goods:collection'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);


$router['shop_vendor_show'] = array(
    '/vendors/{name}~{id}/',
    array('_controller' => 'Shop:Commodity:Vendor:show'),
    array('id' => '\d+'),
    array(),
    'www'
);


$router['goods_option_data'] = array(
    '/goods/option.{action}.data',
    array('_controller' => 'Shop:Commodity:Option:{action}Data'),
    array('action' => '\w+'),
    array(),
    'www'
);

$router['goods_search'] = array(
    '/search/',
    array('_controller' => 'Shop:Commodity:Goods:search'),
    array(),
    array(),
    'www'
);

return $router;
