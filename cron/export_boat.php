<?php
define('DIR_INDEX',__DIR__.'/..');
include_once DIR_INDEX . '/libs/bootstrap.php';

define('EXPORT_PATH', __DIR__ . '/../export');
define('EXPORT_TMP', __DIR__ . '/../export_tmp');
define('ARCHIVE_LIMIT', 50);
define('ATTEMPTS_LIMIT', 5);
define('LOCK_FILE', '.lock');
define('ATTEMPT_FILE', '.attempts');
define('ERROR_FILE', '.error');
define('IMPORT_XML', 'import.xml');
define('OFFERS_XML', 'offers.xml');

if (isLocked(__DIR__)) {
    exit();
}

lock(__DIR__);

$batches = listPath(EXPORT_TMP);
$archivedCount = 0;
foreach ($batches as $name => $batchPath) {
    if (isLocked($batchPath)) {
        $archivedCount++;
        continue;
    }

    $importTmp = implode('/', [$batchPath, IMPORT_XML]);
    $offersTmp = implode('/', [$batchPath, OFFERS_XML]);
    if (!is_file($importTmp) || !is_file($offersTmp)) {
        if (attempt($batchPath)) {
            break;
        } else {
            lock($batchPath);
            error($batchPath);
            continue;
        }
    }

    $importXml = implode('/', [EXPORT_PATH, 'import.xml']);
    $offersXml = implode('/', [EXPORT_PATH, 'offers.xml']);

    copy($importTmp, $importXml);
    copy($offersTmp, $offersXml);

    try {
        $cron = new \Boat\Store\Cron\Export1C\Export1C('Export1c');
        echo $cron->Exec();
    } catch (\Delorius\Exception\Error $e) {
        logger('[import][script] error: ' . $e->getMessage());

        lock($batchPath);
        error($batchPath, $e->getMessage());

        echo $e->getMessage();
        return false;
    }

    unlink($importXml);
    unlink($offersXml);

    lock($batchPath);
    $archivedCount++;
}

if ($archivedCount > ARCHIVE_LIMIT) {
    foreach ($batches as $name => $batchPath) {
        if (!isLocked($batchPath)) {
            continue;
        }

        rmrf($batchPath);
        $archivedCount--;
        if ($archivedCount <= ARCHIVE_LIMIT) {
            break;
        }
    }
}

unlock(__DIR__);

/* Helpers */
function isLocked($path) {
    $lock = implode('/', [$path, LOCK_FILE]);
    return file_exists($lock);
}

function lock($path) {
    if (isLocked($path)) {
        return;
    }

    $lock = implode('/', [$path, LOCK_FILE]);
    file_put_contents($lock, date('Y-m-d H:i:s'));
}

function unlock($path) {
    if (!isLocked($path)) {
        return;
    }

    $lock = implode('/', [$path, LOCK_FILE]);
    unlink($lock);
}

function attempt($path) {
    $attempts = implode('/', [$path, ATTEMPT_FILE]);
    $num = 0;
    if (is_file($attempts)) {
        $num = (integer)file_get_contents($attempts);
    }

    $num++;
    file_put_contents($attempts, $num);

    return $num < ATTEMPTS_LIMIT;
}

function error($path, $error = '1') {
    $err = implode('/', [$path, ERROR_FILE]);
    file_put_contents($err, $error);
}

function listPath($path) {
    $files = array_filter(scandir($path), function ($name) {
        return !in_array($name, ['.', '..', '.gitignore', 'clear_lock.php']);
    });

    $list = array();
    foreach ($files as $filename) {
        $list[$filename] = realpath(implode('/', [$path, $filename]));
    }

    ksort($list);
    return $list;
}

function rmrf($path) {
    $cmd = [
        'rm -rf',
        $path
    ];

    exec(implode(' ', $cmd));
}

exit();

#/usr/bin/php /home/u13359/site.com/www/cron/migration.php 1> /dev/null  2>&1
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$running = implode('/', [__DIR__, '.running']);
if (is_file($running)) {
    return;
}
file_put_contents($running, date('Y-m-d H:i:s'));

$exportPath = __DIR__ . '/../export';
$exportTmp = __DIR__ . '/../export_tmp';

$tmpFiles = array_filter(scandir($exportTmp), function ($name) {
    return !in_array($name, ['.', '..']);
});

sort($tmpFiles);
foreach ($tmpFiles as $exportFolder) {
    $exportBatch = implode('/', [$exportTmp, $exportFolder]);

    $importFile = implode('/', [$exportBatch, 'import.xml']);
    $offersFile = implode('/', [$exportBatch, 'offers.xml']);
    if (!is_file($importFile) || !is_file($offersFile)) {
        // @note если мы не нашли файлы хотя бы в одной папке, считаем количество попыток
        $attempts = implode('/', [$exportBatch, '.attempts']);
        $num = 0;
        if (is_file($attempts)) {
            $num = (integer)file_get_contents($attempts);
        }

        // @note если больше пяти попыток чтения - удаляем папку и продолжаем
        if ($num + 1 > 5) {
            rmrf($exportBatch);
            continue;
        }

        file_put_contents($attempts, $num + 1);
        break;
    }

    // @note иначе копируем файлы в папку экспорта и запускаем
    $importXml = implode('/', [$exportPath, 'import.xml']);
    $offersXml = implode('/', [$exportPath, 'offers.xml']);
    $dotLock = implode('/', [$exportPath, '.lock']);
    copy($importFile, $importXml);
    copy($offersFile, $offersXml);
    file_put_contents($dotLock, 1);

    try {
        $cron = new \Boat\Store\Cron\Export1cCron('Export1c');
        echo $cron->Exec();
    } catch (\Delorius\Exception\Error $e) {
        logger('[import][script] error: ' . $e->getMessage());
        echo $e->getMessage();
        return false;
    }

    rmrf($exportBatch);
    unlink($importXml);
    unlink($offersXml);
    unlink($dotLock);
}

unlink($running);
