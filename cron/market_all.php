<?php
define('DIR_INDEX',__DIR__.'/../');
include_once DIR_INDEX . '/libs/bootstrap.php';

#/usr/bin/php /home/u13359/site.com/www/cron/migration.php 1> /dev/null  2>&1

try {
    $cron = new \Shop\Commodity\Cron\MarketAllCron('MarketAll');
    echo $cron->Exec();
} catch (\Delorius\Exception\Error $e) {
    echo $e->getMessage();
}