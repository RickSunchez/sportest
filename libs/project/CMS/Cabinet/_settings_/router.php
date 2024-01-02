<?php
defined('DELORIUS') or die('access denied');

$router['cabinet'] = array(
    '/cabinet/',
    array('_controller' => 'CMS:Cabinet:User:index')
);

$router['cabinet_user_data'] = array(
    '/cabinet/user/{action}.data',
    array('_controller' => 'CMS:Cabinet:User:{action}Data'),
    array('action' => '\w+')
);

$router['cabinet_help_desk_im_data'] = array(
    '/cabinet/help/{action}.data',
    array('_controller' => 'CMS:Cabinet:Message:{action}Data'),
    array('action' => '\w+')
);

$router['cabinet_help_desk_list'] = array(
    '/cabinet/help/',
    array('_controller' => 'CMS:Cabinet:Message:list'),
    array('action' => '\w+')
);

$router['cabinet_help_desk_show'] = array(
    '/cabinet/help/im',
    array('_controller' => 'CMS:Cabinet:Message:show')
);

$router['cabinet_balance'] = array(
    '/cabinet/balance',
    array('_controller' => 'CMS:Cabinet:Balance:index')
);


$router['cabinet_account_data'] = array(
    '/cabinet/account.data',
    array('_controller' => 'CMS:Cabinet:Balance:accountData')
);

$router['cabinet_account'] = array(
    '/cabinet/account',
    array('_controller' => 'CMS:Cabinet:Balance:account')
);

return $router;