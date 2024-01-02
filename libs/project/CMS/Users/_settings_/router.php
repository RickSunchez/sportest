<?php
defined('DELORIUS') or die('access denied');

$router['user_login_data'] = array(
    '/login/{action}.data',
    array('_controller'=>'CMS:Users:Authorized:{action}Data'),
    array('action'=>'(reg|auth|forgot)')
);

$router['user_login'] = array(
    '/login/{action}',
    array('_controller'=>'CMS:Users:Authorized:{action}'),
    array('action'=>'(forgot|reg|auth|logout|remind)')
);

$router['im'] = array(
    '/im',
    array('_controller'=>'CMS:Users:Message:list')
);

$router['im_to'] = array(
    '/im/{user_id}',
    array('_controller'=>'CMS:Users:Message:to'),
    array('user_id'=>'\d+')
);

$router['im_data'] = array(
    '/im/{action}.data',
    array('_controller'=>'CMS:Users:Message:{action}Data')
);

$router['im_private_message'] = array(
    '/im/private_message/{id}',
    array('_controller'=>'CMS:Users:Message:privateMessage'),
    array('id'=>'\d+')
);

$router['im_private_message_list'] = array(
    '/im/list_dialogs/',
    array('_controller'=>'CMS:Users:Message:listDialogs')
);


$router['im_clear_dialog'] = array(
    '/im/clear_dialog/{id}',
    array('_controller'=>'CMS:Users:Message:clearDialogData'),
    array('id'=>'\d+')
);

return $router;