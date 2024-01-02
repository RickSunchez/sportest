<?php

namespace CMS\Core\Component\Sitemaps;

use Delorius\ComponentModel\Container;
use Delorius\Tools\ILogger;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Finder;

class Collection extends Container
{
    /**
     * @var ILogger
     */
    protected $logger;

    public function __construct(ILogger $logger)
    {
        $this->logger = $logger;
        $this->monitor(__CLASS__);
        parent::__construct();
    }

    /**
     * @param array $list
     */
    public function build($list)
    {
        if (count($list)) {
            foreach ($list as $class => $config) {
                if (class_exists($class)) {
                    $this->addSitemaps(new $class($config));
                } else {
                    $this->logger->error('Not exist class = ' . $class, 'sitemaps');
                }
            }
        }
    }

    /**
     * @param IItemSitemaps $item
     * @return $this
     * @throws \Delorius\Exception\Error
     * @throws \Exception
     */
    public function addSitemaps(IItemSitemaps $item)
    {
        $this->addComponent($item, $item->getFullName());
        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function get()
    {
        return $this->getComponents();
    }

    /**
     * @return int
     */
    public function create()
    {
        $bits = 0;
        foreach ($this->getComponents() as $sitemaps) {
            $bits += $sitemaps->create();
            $this->logger->info($bits, 'sitemaps');
        }

        return $bits;
    }

    /**
     * @throws \Delorius\Exception\Error
     */
    public function clear()
    {
        foreach ($this->getComponents() as $sitemaps) {
            FileSystem::delete($sitemaps->getPath());
        }
    }


}