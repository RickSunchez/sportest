<?php
defined('DELORIUS') or die('access denied');

$router['homepage'] = array(
    '/',
    array('_controller' => 'CMS:Core:Home:index'),
    array(),
    array(),
    'www'
);

$router['error'] = array(
    '/{action}.error',
    array('_controller' => 'CMS:Core:Error:e{action}', 'action' => '404'),
    array('action' => '(404|403|500|400|410)')
);

$router['page'] = array(
    '/{url}.html',
    array('_controller' => 'CMS:Core:Page:index'),
    array('url' => '([\/-A-Za-z0-9_]+)[^\/]')
);

$router['robots.txt'] = array(
    '/robots.txt',
    array('_controller' => 'CMS:Core:Generator:robots')
);

$router['callback'] = array(
    '/callback/',
    array('_controller' => 'CMS:Core:Callback:send')
);

$router['callback_active'] = array(
    '/callback/active',
    array('_controller' => 'CMS:Core:Callback:active'),
    array(),
    array(),
    'www'
);

$router['thumb_id'] = array(
    '/thumb/{set}/{image_id}',
    array('_controller' => 'CMS:Core:Thumb:byId'),
    array('image_id' => '\d+', 'set' => '\w+')
);

$router['thumb_path'] = array(
    '/thumb/{set}',
    array('_controller' => 'CMS:Core:Thumb:byPath'),
    array('set' => '\w+')
);

$router['doc_download'] = array(
    '/download/{id}/{hash}/',
    array('_controller' => 'CMS:Core:Document:download'),
    array('id' => '\d+')
);

return $router;