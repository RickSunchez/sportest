<?php
defined('DELORIUS') or die('access denied');

$router['banner_go'] = array(
    '/go-to/{id}',
    array('_controller'=>'CMS:Banners:Redirect:go'),
    array('id'=>'\d+'),
);

return $router;