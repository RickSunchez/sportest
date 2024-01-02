<?php
defined('DELORIUS') or die('access denied');

$router['shop_category'] = array(
    '/{city_url}/shop/',
    array('_controller' => 'Location:Store:Shop:index'),
    array('city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_shop_category'] = array(
    '/shop/',
    array('_controller' => 'Location:Store:Shop:index'),
    array('city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
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

$router['shop_filters_data'] = array(
    '/filters.data',
    array('_controller' => 'Location:Store:Shop:filtersData'),
    array(),
    array(),
    'www'
);

$router['shop_category_filter'] = array(
    '/{city_url}/{url}-c{cid}/{url_filter}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array(
        'cid' => '\d+',
        'url' => '([0-9a-zA-Z\-]+)',
        'city_url' => '([0-9a-zA-Z\-]+)',
        'url_filter' => '([0-9a-zA-Z\-]+)'
    ),
    array(),
    'www'
);

$router['default_shop_category_filter'] = array(
    '/{url}-c{cid}/{url_filter}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array(
        'cid' => '\d+',
        'url' => '([0-9a-zA-Z\-]+)',
        'url_filter' => '([0-9a-zA-Z\-]+)'
    ),
    array(),
    'www'
);

$router['shop_category_list'] = array(
    '/{city_url}/{url}-c{cid}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_shop_category_list'] = array(
    '/{url}-c{cid}/',
    array('_controller' => 'Location:Store:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_category_collection'] = array(
    '/{city_url}/{url}-t{id}/',
    array('_controller' => 'Location:Store:Shop:collection'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'city_url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['default_shop_category_collection'] = array(
    '/{url}-t{id}/',
    array('_controller' => 'Location:Store:Shop:collection'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
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

$router['shop_category'] = array(
    '/shop/',
    array(),
    array(),
    array('error' => '404'),
    'www'
);

$router['category_search_data'] = array(
    '/catalog/search.data',
    array(),
    array(),
    array('error' => '404'),
    'www'
);

return $router;
