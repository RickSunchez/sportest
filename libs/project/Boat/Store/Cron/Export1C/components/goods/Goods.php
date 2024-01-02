<?php namespace Boat\Store\Cron\Export1C\components\goods;

use Boat\Store\Cron\Export1C\components\goods\helpers\ImportFileHelper;
use Boat\Store\Cron\Export1C\components\goods\helpers\UpdateHelper;
use Delorius\Core\Environment;

class Goods
{
    protected $importHelper;
    protected $updateHelper;

    protected $container; 

    protected $goods = array();
    protected $categories = array();
    protected $properties = array();
    protected $successInit = false;

    public function __construct() {
        $this->container = Environment::getContext();
        $this->updateHelper = new UpdateHelper;
    }

    public function init($importData, $offersData, $categories, $properties)
    {
        $goodsExists = (bool)count($importData->Каталог->Товары->Товар);
        if (!$goodsExists) {
            return $this->goods;
        }

        $offersExists = (bool)count($offersData->ПакетПредложений->Предложения->Предложение);
        if (!$offersExists) {
            return $this->goods;
        }

        $this->importHelper = new ImportFileHelper($categories, $properties);

        $this->categories = $categories;
        $this->properties = $properties;

        foreach ($importData->Каталог->Товары->Товар as $goods) {
            $this->importGoods($goods);
        }

        foreach ($offersData->ПакетПредложений->Предложения->Предложение as $offer) {
            $this->offersGoods($offer);
        }

        $successInit = (bool)count($this->goods);
        return $this->goods; 
    }

    public function updateGoods()
    {
        $this->log('export1c | updateGoods');
        $this->log('export1c | updateGoods | count = ' . count($this->goods));

        foreach ($this->goods as $externalId => $item) {
            $result = null;
            if ($this->isMono($item)) {
                $result = $this->updateHelper->updateMonoItem($item, $externalId);
            } else {
                $result = $this->updateHelper->updateCollectionItem($item, $externalId);
            }
        }

        // foreach ($this->goods as $hash => $item) {
        //     if ($this->check_goods_mono($item)) {
        //         $this->inset_mono_goods($item, $hash);
        //     } else {
        //         $this->inset_collection_goods($item, $hash);
        //     }
        // }
    }

    /* Helpers */
    protected function importGoods($elm)
    {
        $categoryExternalId = 0;
        foreach ($elm->Группы->Ид as $id) {
            $categoryExternalId = $id->__toString();
        }

        $cid = 0;
        $categoryName = null;
        if (key_exists($categoryExternalId, $this->categories)) {
            $cid = key_exists('cid', $this->categories[$categoryExternalId])
                ? $this->categories[$categoryExternalId]['cid']
                : $cid;
            $categoryName = key_exists('name', $this->categories[$categoryExternalId])
                ? $this->categories[$categoryExternalId]['name']
                : $categoryName;
        }

        $id = $elm->Ид->__toString();
        $this->goods[$id]['id'] = $id;
        $this->goods[$id]['cid'] = $cid;
        $this->goods[$id]['category'] = $categoryName;
        $this->goods[$id]['vendor'] = $elm->Изготовитель ? $elm->Изготовитель->Наименование->__toString() : '';
        $this->goods[$id]['name'] = trim($elm->Наименование->__toString());
        $this->goods[$id]['unit'] = $elm->БазоваяЕдиница->__toString();
        // $this->goods[$id]['brief'] = $elm->Описание->__toString();
        $this->goods[$id]['article'] = $this->importHelper->extractArticle($elm);
        $this->goods[$id]['t_article'] = trim($elm->Артикул->__toString());
        $this->goods[$id]['a_articles'] = $this->importHelper->extractAdditionalArticles($elm);
        $this->goods[$id]['delete'] = $this->importHelper->extractDeleteFlag($elm);
    }

    protected function offersGoods($elm)
    {
        $id = $elm->Ид->__toString();

        $name = trim($elm->Наименование->__toString());
        $article = trim($elm->Артикул->__toString());
        $amount = (int)$elm->Количество->__toString();
        $price = trim($elm->Цены->Цена->ЦенаЗаЕдиницу->__toString());

        if (strpos($id, '#') != false) {
            $ids = explode('#', $id);
            $id = $ids[0];
            $sub = $ids[1];

            $this->goods[$id]['products'][$sub]['id'] = $sub;
            $this->goods[$id]['products'][$sub]['name'] = $name;
            $this->goods[$id]['products'][$sub]['article'] = $article;
            $this->goods[$id]['products'][$sub]['parent_external_id'] = $id;
            $this->goods[$id]['products'][$sub]['amount'] = $amount;
            $this->goods[$id]['products'][$sub]['price'] = $price;
                
        } else {
            $this->goods[$id]['id'] = $id;
            $this->goods[$id]['name'] = $name;
            $this->goods[$id]['article'] = $article;
            $this->goods[$id]['amount'] = $amount;
            $this->goods[$id]['price'] = $price;
        }
    }

    protected function isMono($item)
    {
        if (!key_exists('products', $item)) {
            return true;
        }
        return count($item['products']) == 0
            ? true
            : false;
    }

    protected function log($sMsg)
    {
        $this->container->getService('logger')->info($sMsg, 'cron');
    }
}
