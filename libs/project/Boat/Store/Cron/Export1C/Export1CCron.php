<?php namespace Boat\Store\Cron\Export1C;

use Boat\Store\Cron\Export1C\components\catalog\Catalog as CatalogHandler;
use Boat\Store\Cron\Export1C\components\goods\Goods as GoodsHandler;
use Boat\Store\Cron\Export1C\components\properties\Properties;
use Delorius\Core\Cron;
use Delorius\Core\Environment;
use Error;
use Shop\Catalog\Helpers\Catalog;

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');

class Export1CCron extends Cron
{
    const IMPORT_FILE = 'import.xml';
    const OFFERS_FILE = 'offers.xml';

    protected $props = array();
    protected $goods = array();

    protected $catalogHandler;
    protected $propertiesHandler;
    protected $goodsHandler;

    protected function init()
    {
        $this->log('export1c | init');

        $this->catalogHandler = new CatalogHandler;
        $this->propertiesHandler = new Properties;
        $this->goodsHandler = new GoodsHandler;
    }

    protected function client()
    {
        $this->init();
        $exportFolder = realpath(Environment::getContext()->getParameters('path.export'));

        $config = Environment::getContext()->getService('config')->deliver('import');

        $this->goods = $config->get('goods');

        $importFilepath = implode('/', array($exportFolder, self::IMPORT_FILE));
        $offersFilepath = implode('/', array($exportFolder, self::OFFERS_FILE));
        if (!file_exists($importFilepath) || !file_exists($offersFilepath)) {
            $this->log('export1c | files not exists');
            return;
        }

        $importData = @simplexml_load_file($importFilepath);
        $offersData = @simplexml_load_file($offersFilepath);
        if (!$importData) {
            throw new Error('incorrect XML file "' . $importFilepath);
        }

        if (!$offersData) {
            throw new Error('incorrect XML file "' . $offersFilepath);
        }

        $this->log('export1c | init catalog');
        $categories = $this->catalogHandler->init($importData);
        if (!empty($categories)) {
            $config->set('categories', $categories);
            $config->save();
        }
        
        $this->log('export1c | init properties');
        $properties = $this->propertiesHandler->init($importData);

        $this->log('export1c | init goods');
        $this->goods = $this->goodsHandler->init($importData, $offersData, $categories, $properties);

        $this->log('export1c | goods save');
        $config->set('goods', $this->goods);
        $config->save();

        $success = $this->goodsHandler->updateGoods();
        // $config->set('goods', null);
        // $config->save();

        $this->container->getService('sitemaps')->create();
        Catalog::counted();
        // echo var_export($this->goods, true) . PHP_EOL;
    }
}