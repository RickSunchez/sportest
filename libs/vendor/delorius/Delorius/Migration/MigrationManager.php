<?php
namespace Delorius\Migration;

use Delorius\Core\Object;
use Delorius\DataBase\DB;
use Delorius\Tools\ILogger;

class MigrationManager extends Object
{
    /**
     * @var \Delorius\Tools\ILogger
     */
    public $logger;

    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @var array
     */
    protected $items = array();

    /** @return IMigrationItem */
    public function add(IMigrationItem $item)
    {
        $this->items[$item->getName()] = $item;
        return $this->items[$item->getName()];
    }

    /**
     * @param $name
     * @return IMigrationItem
     */
    public function get($name)
    {
        return $this->items[$name];
    }

    /**
     * @return array(IMigrationItem)
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * return void;
     */
    public function start()
    {
        foreach ($this->items as $item) {
            if ($item->isChange()) {
                $arrQuery = $item->getQuery();
                foreach ($arrQuery as $sql) {
                    $query = DB::query(NULL, $sql);
                    $this->logger->info($sql, 'MigrationManager');
                    $query->execute($item->getDB());
                }
            }
            if ($item->isEmptyTable()) {
                $item->insetTable();
            }
        }
    }
} 