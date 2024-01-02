<?php
define('DIR_INDEX', __DIR__);
include_once DIR_INDEX . '/libs/bootstrap.php';

$container->getService('front')->run();
