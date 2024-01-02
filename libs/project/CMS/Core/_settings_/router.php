<?php
defined('DELORIUS') or die('access denied');

$router['event_calendar'] = array(
    '/events/calendar.data',
    array('_controller' => 'CMS:Core:Event:calendarData')
);

$router['event_show'] = array(
    '/events/{url}~{id}.htm',
    array('_controller' => 'CMS:Core:Event:index'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['event_category'] = array(
    '/events/{url}~{cid}/',
    array('_controller' => 'CMS:Core:Event:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['events'] = array(
    '/events/',
    array('_controller' => 'CMS:Core:Event:list')
);

$router['news_calendar'] = array(
    '/news/calendar.data',
    array('_controller' => 'CMS:Core:News:calendarData')
);

$router['news_show'] = array(
    '/news/{url}~{id}.htm',
    array('_controller' => 'CMS:Core:News:index'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['news_category'] = array(
    '/news/{url}~{cid}/',
    array('_controller' => 'CMS:Core:News:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['news'] = array(
    '/news/',
    array('_controller' => 'CMS:Core:News:list')
);

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

$router['review_add_data'] = array(
    '/reviews/add.data',
    array('_controller' => 'CMS:Core:Review:addData'),
    array(),
    array(),
    'www'
);

$router['review_show'] = array(
    '/reviews/{id}',
    array('_controller' => 'CMS:Core:Review:show'),
    array('id' => '\d+'),
    array(),
    'www'
);

$router['reviews'] = array(
    '/reviews/',
    array('_controller' => 'CMS:Core:Review:list'),
    array(),
    array(),
    'www'
);

$router['question_add_data'] = array(
    '/question/add.data',
    array('_controller' => 'CMS:Core:Question:addData'),
    array(),
    array(),
    'www'
);

$router['question_add'] = array(
    '/question/add',
    array('_controller' => 'CMS:Core:Question:add'),
    array(),
    array(),
    'www'
);

$router['question'] = array(
    '/question/',
    array('_controller' => 'CMS:Core:Question:list'),
    array(),
    array(),
    'www'
);

$router['doc_download_file'] = array(
    '/download/file:{id}/',
    array('_controller' => 'CMS:Core:Document:downloadFile'),
    array('id' => '\d+')
);

$router['galleries'] = array(
    '/galleries/',
    array('_controller' => 'CMS:Core:Gallery:list'),
);

$router['gallery'] = array(
    '/galleries/{id}/',
    array('_controller' => 'CMS:Core:Gallery:show'),
    array('id' => '\d+')
);

$router['gallery_image'] = array(
    '/galleries/{id}/{image_id}.image',
    array('_controller' => 'CMS:Core:Gallery:image'),
    array('id' => '\d+', 'image_id' => '\d+')
);

$router['gallery_category'] = array(
    '/galleries/{url}~{cid}/',
    array('_controller' => 'CMS:Core:Gallery:list'),
    array('cid' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['videos'] = array(
    '/videos/',
    array('_controller' => 'CMS:Core:Video:list'),
);

$router['video'] = array(
    '/videos/{id}/',
    array('_controller' => 'CMS:Core:Video:show'),
    array('id' => '\d+')
);

$router['video_get'] = array(
    '/videos.get/',
    array('_controller' => 'CMS:Core:Video:get')
);

$router['video_category'] = array(
    '/videos/{url}~{cid}/',
    array('_controller' => 'CMS:Core:Video:list'),
    array('id' => '\d+', 'url' => '([0-9a-zA-Z\-]+)')
);

$router['poll_data'] = array(
    '/poll.{action}.data',
    array('_controller' => 'CMS:Core:Poll:{action}Data')
);

return $router;