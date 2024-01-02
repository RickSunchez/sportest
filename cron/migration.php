<?php
define('DIR_INDEX',__DIR__.'/../');
include_once DIR_INDEX . '/libs/bootstrap.php';

/**
 * Миграция (костыль)
 *
 * http://site.ru/cron/migration.php
 *
 * Перед запуском закомментировать в:
 *
 *
 * ./cron/.htaccess  - все
 *
 * и
 *
 * /lib/project/Shop/Store/Model/CurrencyBuilder.php
 *
 * Это закомментировать
 * $currencies = Currency::model()
 *      ->cached()
 *      ->order_pk()
 *      ->find_all();
 *  foreach($currencies as $cur){
 *      $this->_currencies[$cur->code] = $cur;
 *  }
 *
 */

try {
    $cron = new \CMS\Core\Cron\MigrationCron('Migration');
    echo $cron->Exec();
} catch (\Delorius\Exception\Error $e) {
    echo $e->getMessage();
}