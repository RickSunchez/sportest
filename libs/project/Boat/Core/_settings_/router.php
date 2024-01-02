<?php
defined('DELORIUS') or die('access denied');

$router['article_category'] = array(
    '/articles/{url}~{cid}/',
    array('_controller' => 'CMS:Core:Article:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['article_show'] = array(
    '/articles/{url}~{id}.htm',
    array('_controller' => 'CMS:Core:Article:index'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['articles'] = array(
    '/articles/',
    array('_controller' => 'CMS:Core:Article:list')
);

$router['callback_realtime'] = array(
    '/callback_realtime/',
    array('_controller' => 'Boat:Core:Callback:realtime')
);

$router['doc_download'] = array(
    '/download/{id}/{hash}/',
    array('_controller' => 'CMS:Core:Document:download'),
    array('id' => '\d+')
);

return $router;