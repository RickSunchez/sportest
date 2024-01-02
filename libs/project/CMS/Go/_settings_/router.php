<?php
defined('DELORIUS') or die('access denied');


$router['go_home']= array(
    '/',
    array('_controller'=>'CMS:Go:Redirect:home'),
    array('url'=>'\w+'),
    array(),
    'go'
);

$router['go']= array(
    '/{url}',
    array('_controller'=>'CMS:Go:Redirect:index'),
    array('url'=>'\w+'),
    array(),
    'go'
);

$router['go_mail']= array(
    '/{url}/m/{hash}',
    array('_controller'=>'CMS:Go:Redirect:email'),
    array('url'=>'\w+','hash'=>'\w{45}'),
    array(),
    'go'
);

return $router;