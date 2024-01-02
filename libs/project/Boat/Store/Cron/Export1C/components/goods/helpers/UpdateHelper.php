<?php namespace Boat\Store\Cron\Export1C\components\goods\helpers;

use CMS\Core\Helper\Helpers;
use Delorius\Core\Environment;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CollectionProduct;
use Shop\Commodity\Entity\CollectionProductItem;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Unit;
use Shop\Commodity\Entity\Vendor;

class UpdateHelper
{
    protected $container; 

    public function __construct()
    {
        $this->container = Environment::getContext();
    }

    public function updateMonoItem($item, $externalId)
    {
        $existingItem = $this->existingItem($externalId);
        if (is_null($existingItem)) {
            return $this->createMonoItem($item, $externalId);
        }

        if ($this->onDelete($item)) {
            $result = null;
            if ($this->productIsEditable($existingItem)) {
                $result = $existingItem->delete();
                $this->log('export1c | updateGoods | mono | delete ' . $externalId);
            } elseif ($existingItem->status == 1) {
                $existingItem->status = 0;
                $result = $existingItem->save();
                $this->log('export1c | updateGoods | mono | disable ' . $externalId);
            }
            return $result;
        }

        return $this->updateItem($existingItem, $item, $externalId);
    }

    public function updateCollectionItem($item, $externalId)
    {
        if (!key_exists('products', $item) && !is_array($item['products'])) {
            return null;
        }

        $existingCollection = $this->existingCollection($externalId);
        if (is_null($existingCollection)) {
            $name = key_exists('name', $item) ? $item['name'] : "Не указано";
            $collection = new CollectionProduct;

            $collection->name = $name;
            $collection->label = $this->getCollectionLabel($item);
            $collection->external_id = $externalId;

            try {
                $collection->save();
            } catch (OrmValidationError $e) {
                $this->log('export1c | updateGoods | collection >> fail >> ' . $externalId);
                $this->log('export1c | updateGoods | collection >> fail >> message:' . PHP_EOL . $e->getErrorsMessage());
            }
            $this->log('export1c | updateGoods | collection >> success >> ' . $externalId);
            $existingCollection = $collection;
        }

        $existingParentItem = $this->existingItem($externalId);
        foreach ($item['products'] as $eId => $cItem) {
            $existingItem = $this->existingItem($eId);

            if ($existingParentItem && $existingItem) {
                $del = new Goods($existingItem->pk());
                $del->delete();
                $existingItem = $existingParentItem;
                $existingItem->external_id = $item['id'];
                $existingParentItem = null;
            }


            if ($existingItem) {
                $collectionItemData = array(
                    'id' => key_exists('id', $cItem) ? $cItem['id'] : null,
                    'name' => key_exists('name', $cItem) ? $cItem['name'] : null,
                    'price' => key_exists('price', $cItem) ? $cItem['price'] : null,
                    'amount' => key_exists('amount', $cItem) ? $cItem['amount'] : null,
                    'article' => key_exists('article', $item) ? $item['article'] : null,
                    't_article' => key_exists('t_article', $item) ? $item['t_article'] : null,
                    'a_articles' => key_exists('a_articles', $item) ? $item['a_articles'] : null,
                );

                $this->updateItem($existingItem, $collectionItemData, $eId, $existingCollection);
            } else {
                $this->createMonoItem($cItem, $eId, $existingCollection);
            }
        }
    }

    /* Helpers */
    protected function existingItem($externalId)
    {
        if (!$externalId) {
            return null;
        }

        $existingItem = Goods::model()->where('external_id', '=', $externalId)->find();
        return $existingItem->loaded()
            ? $existingItem
            : null;
    }

    protected function existingCollection($externalId)
    {
        if (!$externalId) {
            return null;
        }

        $existingCollection = CollectionProduct::model()->where('external_id', '=', $externalId)->find();
        return $existingCollection->loaded()
            ? $existingCollection
            : null;
    }

    protected function onDelete($item)
    {
        if (!key_exists('delete', $item)) {
            return false;
        }

        return (bool)$item['delete'];
    }

    protected function productIsEditable($produt)
    {
        return $produt->moder == 1 && $produt->status == 0;
    }

    protected function createMonoItem($item, $externalId, $collection = null)
    {
        $this->log('export1c | updateGoods | create >> begin >> ' . $externalId);
        if ($this->onDelete($item)) {
            $this->log('export1c | updateGoods | create >> deleted >> ' . $externalId);
            return null;
        }

        $product = new Goods;

        $name = key_exists('name', $item) ? $item['name'] : '';
        $article = key_exists('article', $item) ? $item['article'] : '';
        $unit = key_exists('unit', $item) ? $item['unit'] : null;
        $vendor = key_exists('vendor', $item) ? $item['vendor'] : null;
        $amount = key_exists('amount', $item) ? (int)$item['amount'] : 0.0;
        $price = key_exists('price', $item) ? $item['price'] : 0;
        $realArticle = key_exists('t_article', $item) ? $item['t_article'] : null;
        $otherArticles = key_exists('a_articles', $item) ? $item['a_articles'] : null;
        $cid = key_exists('cid', $item) ? $item['cid'] : 0;
        $parentEId = key_exists('parent_external_id', $item) ? $item['parent_external_id'] : null;

        $product->name = $name;
        $product->external_id = $externalId;
        $product->article = $article;
        $product->unit_id = $this->getUnitIdByAbbr($unit);
        $product->vendor_id = $this->getVendorSiteByName($vendor);
        $product->amount = $amount;
        $product->value = $price;
        $product->t_article = $realArticle;
        $product->a_articles = $otherArticles;

        $product->status = 0;
        $product->update = 1;
        $product->moder = 1;

        $product->cid = $cid;
        $product->parent_external_id = $parentEId;

        try {
            $product->save();
        } catch (OrmValidationError $e) {
            $this->log('export1c | updateGoods | create >> fail >> ' . $externalId);
            $this->log('export1c | updateGoods | create >> fail >> message:' . PHP_EOL . $e->getErrorsMessage());
        }

        $this->log('export1c | updateGoods | create >> success >> ' . $externalId);

        if (is_null($collection)) {
            return true;
        }
        
        $this->log('export1c | updateGoods | create >> collection >> ' . $externalId);

        $collection->addItem(array(
            'name' => $this->getCollectionValue($product->name, $collection->name),
            'product_id' => $product->pk()
        ));

        $cpi = CollectionProductItem::model()
            ->select('product_id')
            ->where('coll_id', '=', $collection->pk())
            ->sort()
            ->find();
        $characteristics = CharacteristicsGoods::model()
            ->select('character_id', 'value_id')
            ->where('target_id', '=', $cpi['product_id'])
            ->where('target_type', '=', Helpers::getTableId(Goods::model()))
            ->find_all();

        foreach ($characteristics as $char) {
            // libs/project/Shop/Commodity/Behaviors/GoodsCharacteristicsBehavior.php
            $product->addCharacteristics(
                array(
                    'character_id' => $char['character_id'],
                    'value_id' => $char['value_id'],
                )
            );
        }

        $this->log('export1c | updateGoods | create >> collection >> success >> ' . $externalId);
        return true;
    }

    protected function updateItem($existingItem, $item, $externalId, $collection = null)
    {
        if (!$existingItem->external_change) {
            return null;
        }

        $name = key_exists('name', $item) ? $item['name'] : null;
        $article = key_exists('article', $item) ? $item['article'] : null;
        $amount = key_exists('amount', $item) ? (int)$item['amount'] : null;
        $price = key_exists('price', $item) ? $item['price'] : null;
        $realArticle = key_exists('t_article', $item) ? $item['t_article'] : null;
        $otherArticles = key_exists('a_articles', $item) ? $item['a_articles'] : null;

        $onUpdate = false;
        if (!is_null($name) && $existingItem->name != $name) {
            $existingItem->name = $name;
            $onUpdate = true;
        }

        if (!is_null($article) && $existingItem->article != $article) {
            $existingItem->article = $article;
            $onUpdate = true;
        }

        if (!is_null($amount) && (float)$existingItem->amount != (float)$amount) {
            $existingItem->amount = $amount;
            $onUpdate = true;
        }

        if (!is_null($price) && $existingItem->value != $price) {
            $existingItem->value = $price;
            $onUpdate = true;
        }

        if (!is_null($realArticle) && $existingItem->t_article != $realArticle) {
            $existingItem->t_article = $realArticle;
            $onUpdate = true;
        }

        if (!is_null($otherArticles) && $existingItem->a_articles != $otherArticles) {
            $existingItem->a_articles = $otherArticles;
            $onUpdate = true;
        }

        if (!$onUpdate) {
            return null;
        }

        $this->log('export1c | updateGoods | update >> begin >> ' . $externalId);

        $existingItem->update = 1;
        if (!$existingItem->moder) {
            $existingItem->status = 1;
        }

        try {
            $existingItem->save();
        } catch (OrmValidationError $e) {
            $this->log('export1c | updateGoods | update >> fail >> ' . $externalId);
            $this->log('export1c | updateGoods | update >> fail >> message:' . PHP_EOL . $e->getErrorsMessage());
        }

        $this->log('export1c | updateGoods | update >> success >> ' . $externalId);

        if (is_null($collection)) {
            return true;
        }
        
        $this->log('export1c | updateGoods | update >> collection >> ' . $externalId);

        $cpi = CollectionProductItem::model()
            ->where('product_id', '=', $existingItem->pk())
            ->where('coll_id', '=', $collection->pk())
            ->find();

        if (!$cpi->loaded() && $existingItem->name && $collection->name) {
            try {
                $collection->addItem(array(
                    'name' => $this->getCollectionValue($existingItem->name, $collection->name),
                    'product_id' => $existingItem->pk()
                ));
            } catch (OrmValidationError $e) {
                $this->log('export1c | updateGoods | update >> collection >> fail >> ' . $externalId);
                $this->log('export1c | updateGoods | update >> collection >> fail >> message:' . PHP_EOL . $e->getErrorsMessage());
            }
        }

        $this->log('export1c | updateGoods | update >> collection >> success >> ' . $externalId);
        return true;
    }

    protected function getUnitIdByAbbr($abbr)
    {
        if (!$abbr) {
            return 0;
        }

        $unit = Unit::model()->where('abbr', 'LIKE', $abbr . '%')->find();
        return $unit->loaded() ? $unit->pk() : 0;
    }

    protected function getVendorSiteByName($name)
    {
        if (!$name) {
            return '0';
        }

        $vendor = Vendor::model()->where('name', '=', $name)->find();
        return $vendor->loaded() ? $vendor->pk() : '0';
    }

    protected function getCollectionLabel($item)
    {
        $name = $item['name'];
        $sub = $item['products'][0]['name'];
        $service = new \CMS\Core\Component\Snippet\Parser;
        $title = $service->html($sub);
        $title_parent = $service->html($name);

        $variant = str_replace($title_parent, "", $title);
        $variant = trim($variant);
        $variant = mb_substr($variant, 1, -1);
        $variant = explode(':', $variant);
        if (count($variant) == 2) {
            return $variant[0];
        }

        return 'Опция';
    }

    protected function getCollectionValue($title, $title_parent)
    {
        $service = new \CMS\Core\Component\Snippet\Parser;
        $title = $service->html($title);
        $title_parent = $service->html($title_parent);

        $variant = str_replace($title_parent, "", $title);
        $variant = trim($variant);
        $variant = mb_substr($variant, 1, -1);
        $variant = explode(':', $variant);
        if (count($variant) == 2) {
            return $variant[1];
        }
        return $variant[0];
    }

    protected function log($sMsg)
    {
        $this->container->getService('logger')->info($sMsg, 'cron');
    }

    protected function debug($item, $exit = false)
    {
        echo var_export($item);
        echo PHP_EOL;
        if ($exit) {
            exit();
        }
    }
}
