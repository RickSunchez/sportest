<?php
defined('DELORIUS') or die('access denied');

$router['cabinet_order'] = array(
    '/cabinet/orders/{action}',
    array('_controller' => 'Shop:Cabinet:Orders:{action}','action'=>'list'),
    array('action'=>'\w+')
);

return $router;