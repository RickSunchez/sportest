<?php
defined('DELORIUS') or die('access denied');

$router['import_1c'] = array(
    '/import_1c',
    array('_controller' => 'Boat:Store:Import1c:import'),
    array(),
    array('no_sort_get_params'=>1)
);

$router['orders_1c'] = array(
    '/orders_1c',
    array('_controller' => 'Boat:Store:Import1c:orders'),
    array(),
    array('no_sort_get_params'=>1)
);

$router['shop_goods'] = array(
    '/{city_url}/{url}-p{id}/',
    array('_controller' => 'Location:Store:Goods:show'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_shop_goods'] = array(
    '/{url}-p{id}/',
    array('_controller' => 'Location:Store:Goods:show'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_category_list'] = array(
    '/{city_url}/{url}-{cid}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_shop_category_list'] = array(
    '/{url}-{cid}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_search_data'] = array(
    '/search.data',
    array('_controller' => 'Location:Store:Search:result'),
    array(),
    array(),
    'www'
);


$router['schema_index'] = array(
    '/{city_url}/{url}-s{id}/',
    array('_controller' => 'Boat:Store:Schema:index'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_schema_index'] = array(
    '/{url}-s{id}/',
    array('_controller' => 'Boat:Store:Schema:index'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['schema_note'] = array(
    '/{city_url}/{url}-n{id}/',
    array('_controller' => 'Boat:Store:Schema:note'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_schema_note'] = array(
    '/{url}-n{id}/',
    array('_controller' => 'Boat:Store:Schema:note'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);



$router['page_contact'] = array(
    '/{city_url}/contact/',
    array('_controller' => 'Boat:Store:PageCity:contact'),
    array('city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['page_delivery'] = array(
    '/{city_url}/dostavka/',
    array('_controller' => 'Boat:Store:PageCity:delivery'),
    array('city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);


return $router;