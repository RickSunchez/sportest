<?php
defined('DELORIUS') or die('access denied');

$router['mail_unsub'] = array(
    '/unsub/{group_id}/{hash}/',
    array('_controller'=>'CMS:Mail:Subscriber:unsub'),
    array('group_id'=>'\d+','hash'=>'\w{45}'),
    array(),
    'www'
);

$router['form_show'] = array(
    '/form/{url}/',
    array('_controller'=>'CMS:Mail:Forms:show'),
    array('url'=>'\w+'),
    array(),
    'www'
);

$router['form_show_send'] = array(
    '/form/send.data',
    array('_controller'=>'CMS:Mail:Forms:send'),
    array(),
    array(),
    'www'
);

return $router;