<?php
namespace CMS\Core\Cron;

use Delorius\Core\Cron;

class MigrationCron extends Cron
{

    protected function client()
    {
        $migrationManager = $this->container->getService('migration');
        $migrationManager->start();
    }
}