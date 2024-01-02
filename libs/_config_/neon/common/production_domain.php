<?php

$domain['go'] = array(
    $_SERVER['HTTP_HOST'] . '/link',
    array(),
    array(),
    array(),
    '/link',
    'http'
);

$domain['admin'] = array(
    $_SERVER['HTTP_HOST'] . '/admin',
    array(),
    array(),
    array(),
    '/admin'
);

$domain['www'] = array(
    $_SERVER['HTTP_HOST'],
    array(),
    array(),
    array(),
    null,
    'http'
);

return array(
    'routing' => array(
        'domain' => $domain
    )
);