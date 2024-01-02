<?php
defined('DELORIUS') or die('access denied');

// todo: fix - толлько для каталогов
//$router['category_list'] = array(
//    '/catalog/{url}~{cid}/',
//    array('_controller' => 'Shop:Catalog:Category:list'),
//    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
//    array(),
//    'www'
//);
//
//$router['category'] = array(
//    '/catalog/',
//    array('_controller' => 'Shop:Catalog:Category:index'),
//    array(),
//    array(),
//    'www'
//);
//
//$router['category_search_data'] = array(
//    '/catalog/search.data',
//    array('_controller' => 'Shop:Catalog:Category:searchData'),
//    array(),
//    array(),
//    'www'
//);

$router['shop_filters_data'] = array(
    '/filters.data',
    array('_controller' => 'Shop:Catalog:Shop:filtersData'),
    array(),
    array(),
    'www'
);

$router['shop_category_filter'] = array(
    '/shop/{url}~{cid}/{url_filter}/',
    array('_controller' => 'Shop:Catalog:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)', 'url_filter' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_category_list'] = array(
    '/shop/{url}~{cid}/',
    array('_controller' => 'Shop:Catalog:Shop:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_category'] = array(
    '/shop/',
    array('_controller' => 'Shop:Catalog:Shop:index'),
    array(),
    array(),
    'www'
);

$router['shop_category_collection'] = array(
    '/shop/{url}~l{id}/',
    array('_controller' => 'Shop:Catalog:Shop:collection'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_type'] = array(
    '/product-type~{type_id}/',
    array('_controller' => 'Shop:Catalog:Shop:type'),
    array('type_id' => '\d+'),
    array(),
    'www'
);


$router['shop_brand'] = array(
    '/brands/{url}/',
    array('_controller' => 'Shop:Catalog:Brand:showBrand'),
    array('url' => '([0-9a-zA-Z\-]+)'),
    array(),
    'www'
);

$router['shop_brand_list'] = array(
    '/brands/',
    array('_controller' => 'Shop:Catalog:Brand:listBrand'),
    array(),
    array(),
    'www'
);

$router['shop_search_data'] = array(
    '/shop/search.data',
    array('_controller' => 'Shop:Catalog:Shop:searchData'),
    array(),
    array(),
    'www'
);

return $router;