<?php
define('DELORIUS', true);
/*Check and reset PHP configuration.*/
error_reporting(E_ALL & ~E_NOTICE);


if (!defined('DELORIUS_START_TIME')) {
    define('DELORIUS_START_TIME', microtime(TRUE));
}

if (!defined('DELORIUS_START_MEMORY')) {
    define('DELORIUS_START_MEMORY', memory_get_usage());
}

/* автозагрузчик */
include_once __DIR__ . '/vendor/delorius/Delorius/Core/Autoloader.php';

/* загрузка стороних классов*/
$autoLoader = new Delorius\Core\Autoloader();
$autoLoader->registerNamespaces(array(
    'Delorius' => __DIR__ . '/vendor/delorius',
    'RicardoFiorani' => __DIR__ . '/vendor/video_parser',
));
$autoLoader->registerClasss(array(
    'Jevix' => __DIR__ . '/vendor/jevix/jevix.class.php',
    'Browser' => __DIR__ . '/vendor/cbschuld/Browser.php',
    'Upload' => __DIR__ . '/vendor/upload/class.upload.php',
    'PHPMailer' => __DIR__ . '/vendor/phpmailer/class.phpmailer.php',
    'lessc' => __DIR__ . '/vendor/lessc/lessc.inc.php',
    'PHPExcel' => __DIR__ . '/vendor/PHPExcel_1.8.0/Classes/PHPExcel.php',
    'PHPExcel_IOFactory' => __DIR__ . '/vendor/PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php',
));

//set load project default
$autoLoader->registerNamespaceFallbacks(array(
    __DIR__ . '/project',
));
$autoLoader->register();

/* функции */
include_once __DIR__ . '/_config_/php/function.php';
include_once __DIR__ . '/_config_/php/fn.location.php';
include_once __DIR__ . '/_config_/php/fn.shop.php';
include_once __DIR__ . '/_config_/php/fn.boat.php';

try {
    $configurator = new \Delorius\Bootstrap\Configurator();
    $configurator->setTempDirectory(__DIR__ . '/_temp_');
    $configurator->setWwwDirectory(realpath(__DIR__ . '/../'));
    $configurator->setLibsDirectory(__DIR__);
    $environment = \Delorius\Bootstrap\Configurator::detectDebugMode()
        ? $configurator::DEVELOPMENT
        : $configurator::PRODUCTION;

    $configurator->addConfig(__DIR__ . '/_config_/neon/config.neon', $environment);

    $container = $configurator->createContainer();
} catch (Exception $e) {
    die($e->getMessage());
}