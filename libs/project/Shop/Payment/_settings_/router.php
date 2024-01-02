<?php
defined('DELORIUS') or die('access denied');


$router['payment_robokassa'] = array(
    '/payment/robokassa/{action}',
    array('_controller' => 'Shop:Payment:Robokassa:{action}'),
    array('action' => '(success|fail|result)')
);

$router['payment_yandex'] = array(
    '/payment/yandex/result',
    array('_controller' => 'Shop:Payment:Yandex:result')
);


$router['payment_sberbank'] = array(
    '/payment/sberbank/{action}',
    array('_controller' => 'Shop:Payment:Sberbank:{action}'),
    array('action' => '(payment|success|fail|result)'),
    array(),
    'www'
);

return $router;