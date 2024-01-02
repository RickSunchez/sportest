<?php

$domain['go'] = array(
    'www.sportest.ru/link',
    array(),
    array(),
    array(),
    '/link',
    'https'
);

$domain['admin'] = array(
    'www.sportest.ru/admin',
    array(),
    array(),
    array(),
    '/admin',
    'https'
);

$domain['www'] = array(
    'www.sportest.ru',
    array(),
    array(),
    array(),
    null,
    'https'
);

return array(
    'routing' => array(
        'domain' => $domain
    )
);