<?php
defined('DELORIUS') or die('access denied');


$router['help_desk_im_data'] = array(
    '/help/{action}.data',
    array('_controller' => 'CMS:HelpDesk:Message:{action}Data'),
    array('action' => '\w+')
);

$router['help_desk_list'] = array(
    '/help/',
    array('_controller' => 'CMS:HelpDesk:Message:list'),
    array('action' => '\w+')
);

$router['help_desk_show'] = array(
    '/help/im/{id}',
    array('_controller' => 'CMS:HelpDesk:Message:show'),
    array('id' => '\d+')
);

return $router;