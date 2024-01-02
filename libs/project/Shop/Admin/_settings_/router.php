<?php
defined('DELORIUS') or die('access denied');


$router['admin_yml_data'] = array(
    '/shop/yml/{action}.data',
    array('_controller' => 'Shop:Admin:Yml:{action}Data')
);

$router['admin_yml'] = array(
    '/shop/yml/{action}',
    array('_controller' => 'Shop:Admin:Yml:{action}')
);

$router['admin_category_data'] = array(
    '/shop/category/{action}.data',
    array('_controller' => 'Shop:Admin:Category:{action}Data'),
    array('action' => '\w+')
);

$router['admin_category'] = array(
    '/shop/category/{action}',
    array('_controller' => 'Shop:Admin:Category:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_category_popular_data'] = array(
    '/shop/category/popular/{action}.data',
    array('_controller' => 'Shop:Admin:CategoryPopularGoods:{action}Data'),
    array('action' => '\w+')
);

$router['admin_category_options_data'] = array(
    '/shop/category/options/{action}.data',
    array('_controller' => 'Shop:Admin:CategoryOptions:{action}Data'),
    array('action' => '\w+')
);

$router['admin_category_collection_data'] = array(
    '/shop/category/collection/{action}.data',
    array('_controller' => 'Shop:Admin:CategoryCollection:{action}Data'),
    array('action' => '\w+')
);

$router['admin_category_collection'] = array(
    '/shop/category/collection/{action}',
    array('_controller' => 'Shop:Admin:CategoryCollection:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_category_filter_data'] = array(
    '/shop/category/filter/{action}.data',
    array('_controller' => 'Shop:Admin:CategoryFilter:{action}Data'),
    array('action' => '\w+')
);

$router['admin_category_filter'] = array(
    '/shop/category/filter/{action}',
    array('_controller' => 'Shop:Admin:CategoryFilter:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_goods_type_data'] = array(
    '/shop/type/{action}.data',
    array('_controller' => 'Shop:Admin:Type:{action}Data'),
    array('action' => '\w+')
);

$router['admin_goods_type'] = array(
    '/shop/type/{action}',
    array('_controller' => 'Shop:Admin:Type:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_unit_data'] = array(
    '/shop/unit/{action}.data',
    array('_controller' => 'Shop:Admin:Unit:{action}Data'),
    array('action' => '\w+')
);

$router['admin_unit'] = array(
    '/shop/unit/{action}',
    array('_controller' => 'Shop:Admin:Unit:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_vendor_data'] = array(
    '/shop/vendor/{action}.data',
    array('_controller' => 'Shop:Admin:Vendor:{action}Data'),
    array('action' => '\w+')
);

$router['admin_vendor'] = array(
    '/shop/vendor/{action}',
    array('_controller' => 'Shop:Admin:Vendor:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_provider_data'] = array(
    '/shop/provider/{action}.data',
    array('_controller' => 'Shop:Admin:Provider:{action}Data'),
    array('action' => '\w+')
);

$router['admin_provider'] = array(
    '/shop/provider/{action}',
    array('_controller' => 'Shop:Admin:Provider:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_goods_data'] = array(
    '/shop/goods/{action}.data',
    array('_controller' => 'Shop:Admin:Goods:{action}Data'),
    array('action' => '\w+')
);

$router['admin_goods'] = array(
    '/shop/goods/{action}',
    array('_controller' => 'Shop:Admin:Goods:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_collection_data'] = array(
    '/shop/collection/{action}.data',
    array('_controller' => 'Shop:Admin:Collection:{action}Data'),
    array('action' => '\w+')
);

$router['admin_collection'] = array(
    '/shop/collection/{action}',
    array('_controller' => 'Shop:Admin:Collection:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_chara_data'] = array(
    '/shop/characteristics/{action}.data',
    array('_controller' => 'Shop:Admin:Characteristics:{action}Data'),
    array('action' => '\w+')
);

$router['admin_chara'] = array(
    '/shop/characteristics/{action}',
    array('_controller' => 'Shop:Admin:Characteristics:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_currency_data'] = array(
    '/shop/currency/{action}.data',
    array('_controller' => 'Shop:Admin:Currency:{action}Data'),
    array('action' => '\w+')
);

$router['admin_currency'] = array(
    '/shop/currency/{action}',
    array('_controller' => 'Shop:Admin:Currency:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_order'] = array(
    '/shop/order/{action}',
    array('_controller' => 'Shop:Admin:Order:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_order_data'] = array(
    '/shop/order/{action}.data',
    array('_controller' => 'Shop:Admin:Order:{action}Data'),
    array('action' => '\w+')
);

$router['admin_reviews_data'] = array(
    '/shop/reviews/{action}.data',
    array('_controller' => 'Shop:Admin:Reviews:{action}Data'),
    array('action' => '\w+')
);

$router['admin_reviews'] = array(
    '/shop/reviews/{action}',
    array('_controller' => 'Shop:Admin:Reviews:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_option_data'] = array(
    '/shop/option/{action}.data',
    array('_controller' => 'Shop:Admin:Option:{action}Data'),
    array('action' => '\w+')
);

$router['admin_option'] = array(
    '/shop/option/{action}',
    array('_controller' => 'Shop:Admin:Option:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_product_collection_data'] = array(
    '/shop/product_collection/{action}.data',
    array('_controller' => 'Shop:Admin:ProductCollection:{action}Data'),
    array('action' => '\w+')
);

$router['admin_product_collection'] = array(
    '/shop/product_collection/{action}',
    array('_controller' => 'Shop:Admin:ProductCollection:{action}', array('action' => 'list')),
    array('action' => '\w+')
);


$router['admin_line_product_data'] = array(
    '/shop/line/{action}.data',
    array('_controller' => 'Shop:Admin:LineProduct:{action}Data'),
    array('action' => '\w+')
);

$router['admin_line_product'] = array(
    '/shop/line/{action}',
    array('_controller' => 'Shop:Admin:LineProduct:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_1c'] = array(
    '/shop/1c/{action}',
    array('_controller' => 'Shop:Admin:Import1C:{action}', array('action' => 'index')),
    array('action' => '\w+')
);

return $router;
