<?php
defined('DELORIUS') or die('access denied');

$router['admin_country_data'] = array(
    '/country/{action}.data',
    array('_controller' => 'Location:Admin:Country:{action}Data'),
    array('action' => '\w+')
);

$router['admin_country'] = array(
    '/country/{action}',
    array('_controller' => 'Location:Admin:Country:{action}', array('action' => 'list')),
    array('action' => '\w+')
);

$router['admin_city_data'] = array(
    '/city/{action}.data',
    array('_controller' => 'Location:Admin:City:{action}Data'),
    array('action' => '\w+')
);

$router['admin_city'] = array(
    '/city/{action}',
    array('_controller' => 'Location:Admin:City:{action}', 'action' => 'list'),
    array('action' => '\w+')
);

$router['admin_metro_data'] = array(
    '/metro/{action}.data',
    array('_controller' => 'Location:Admin:Metro:{action}Data'),
    array('action' => '\w+')
);

$router['admin_metro'] = array(
    '/metro/{action}',
    array('_controller' => 'Location:Admin:Metro:{action}', array('action' => 'list')),
    array('action' => '\w+')
);


return $router;
