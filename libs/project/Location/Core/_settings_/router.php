<?php
defined('DELORIUS') or die('access denied');

$router['homepage'] = array(
    '/',
    array('_controller' => 'Location:Core:Home:index')
);

return $router;