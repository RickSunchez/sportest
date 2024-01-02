<?php

namespace Boat\Store\Cron;

use CMS\Core\Entity\Image;
use CMS\Core\Helper\Helpers;
use CMS\Core\Helper\Jevix\JevixEasy;
use Delorius\Core\Cron;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Finder;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Helpers\Catalog;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CollectionProduct;
use Shop\Commodity\Entity\CollectionProductItem;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Section;
use Shop\Commodity\Entity\Unit;
use Shop\Commodity\Entity\Vendor;

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');

class Export1cCron extends Cron
{
    protected $_lockdown_time = 1; // minutes

    protected function client()
    {
    //    $this->parser();
    //    return;
        $path_dir = realpath(Environment::getContext()->getParameters('path.export'));
        $this->log('step1');
        logger('[import][script] init script');
        // @changes
        // if (file_exists($path_dir . '/.lock')) {
        //     $time = filemtime($path_dir . '/.lock') + (60 * $this->_lockdown_time);
        //     if ($time > time()) {
        //         $this->log('Директория импорта заблокирована');
        //         throw new Error('Директория импорта заблокирована');
        //     }
        // }

        $path_dir_export = $path_dir;
        $this->log('step2');
        if (file_exists($path_dir . '/.lock')) {

            /** @var \Delorius\Configure\File\Config $cfg */
            $cfg = Environment::getContext()->getService('config')->deliver('import');


            $this->goods = $cfg->get('goods');

            if (
                file_exists($path_dir_export . '/import.xml') ||
                file_exists($path_dir_export . '/offers.xml')
            ) {
                logger('[import][script] read files');

                $import = @simplexml_load_file($path_dir_export . '/import.xml');
                if (!$import) throw new Error('incorrect XML file "' . $path_dir_export . '/import.xml "');

                $this->log('init catalog');
                if (false && count($import->Классификатор->Группы->Группа)) {
                    foreach ($import->Классификатор->Группы->Группа as $elm) {
                        $this->importCategories($elm);
                    }

//                    $cfg->set('categories', $this->categories);
//                    $cfg->save();

                }

                $this->log('init props');
                if (count($import->Классификатор->Свойства->Свойство)) {

                    foreach ($import->Классификатор->Свойства->Свойство as $props) {

                        $cid = $props->Ид->__toString();
                        if (count($props->ВариантыЗначений->Справочник)) {
                            foreach ($props->ВариантыЗначений->Справочник as $prop) {
                                $id = $prop->ИдЗначения->__toString();
                                $value = $prop->Значение->__toString();

                                if ($value)
                                    $this->props[$cid][$id] = $value;
                            }
                        }

                        if ($props->ДляТоваров->__toString() == 'true') {
                            if (!is_array($this->props['names'])) {
                                $this->props['names'] = [];
                            }

                            $this->props['names'][$cid] = $props->Наименование->__toString();
                        }
                    }
                }

                logger('[import][script] define goods');
                $this->log('init goods');
                if (count($import->Каталог->Товары->Товар))
                    foreach ($import->Каталог->Товары->Товар as $goods) {
                        $this->importGoods($goods, realpath($path_dir_export));
                    }


                $offers = @simplexml_load_file($path_dir_export . '/offers.xml');
                if (!$offers) {
                    $this->log('incorrect XML file "' . $path_dir_export . '/offers.xml" ');
                    $this->log($offers);
                } else {
                    logger('[import][script] define offers');
                    if (count($offers->ПакетПредложений->Предложения->Предложение))
                        foreach ($offers->ПакетПредложений->Предложения->Предложение as $offer) {
                            $this->offersGoods($offer);
                        }
                }

                $this->log('goods save');
                logger('[import][script] end define');
                $cfg->set('goods', $this->goods);
                $cfg->save();
            }

// return; // @dev
            logger('[import][script] update goods');
            if (count($this->goods)) {
                $this->updateGoods();
                $this->log('goods nulled');
                $cfg->set('goods', null);
                $cfg->save();
            }

            logger('[import][script] skip delete files');
            $this->log('files delete');
            // $files = Finder::find('*')->in($path_dir);
            // foreach ($files as $f) {
            //     FileSystem::delete($f->getPathname());
            // }

            Environment::getContext()->getService('sitemaps')->create();

            $this->log('end gen file xml');
            Catalog::counted();
            $this->log('sync end catalog');
            logger('[import][script] end script');
        }


    }

    protected function parser()
    {
        return;
        /** @var \Delorius\Configure\File\Config $cfg */
        $cfg = Environment::getContext()->getService('config')->deliver('import');


        $this->goods = $cfg->get('goods');
        echo "test";
        echo "<pre>";

        foreach ($this->goods as $hash => $item) {

            if ($this->check_goods_mono($item)) {
//                $this->inset_mono_goods($item, $hash);
            } else {
//                if ($hash == "d21827c3-eed7-11eb-b54c-001e67647f08") {
                $goods = $this->getGoodsSiteByExternal($hash);
                if ($goods->loaded()) {
                    var_dump($hash);
                    echo count($item['products']);
                    echo var_dump($item['name']);


                    $ar_goods = $goods->as_array();
                    var_dump($ar_goods);
                    foreach ($item['products'] as $item) {
                        if ($item['name'] == $ar_goods['name']) {
                            var_dump($item);
                            $goods->external_id = $item['id'];
                            $goods->save();

                        }
                    }
                    echo "<br/>======================<br/>";
                }

//                $this->inset_collection_goods($item, $hash);

//                $collection = $this->getGoodsCollectionByExternal($hash);
//                $goods = $this->getGoodsSiteByExternal($hash);
//                }
            }
        }
    }


    /** Парсинг товаров */
    protected $props = array();
    protected $goods = array();

    protected function importGoods($elm)
    {
        $cid = 0;
        foreach ($elm->Группы->Ид as $id) {
            $cid = $id->__toString();
        }


        $id = $elm->Ид->__toString();
        $this->goods[$id]['id'] = $id;
        $this->goods[$id]['cid'] = $cid;
        $this->goods[$id]['category'] = $this->categories[$cid]['name'];
        $this->goods[$id]['vendor'] = $elm->Изготовитель ? $elm->Изготовитель->Наименование->__toString() : '';
        $this->goods[$id]['name'] = $elm->Наименование->__toString();
        $this->goods[$id]['unit'] = $elm->БазоваяЕдиница->__toString();
//        $this->goods[$id]['brief'] = $elm->Описание->__toString();

        $this->goods[$id]['article'] = null;
        $this->goods[$id]['t_article'] = $elm->Артикул->__toString();

        // @note достаем дополнительные свойства
        if (is_array($this->props['names'])) {
            $goodsAArticles = array();
            foreach ($elm->ЗначенияСвойств->ЗначенияСвойства as $key => $value) {
                $propId = $value->Ид->__toString();

                $propName = $this->props['names'][$propId];
                $propValue = $value->Значение->__toString();

                if (empty($propValue)) {
                    continue;
                }

                if (!mb_stristr($propName, 'артикул')) {
                    continue;
                }

                $goodsAArticles[$propId] = array(
                    'name' => $propName,
                    'value' => $propValue
                );
            }

            $this->goods[$id]['a_articles'] = json_encode($goodsAArticles);
        }

        if (count($elm->ЗначенияРеквизитов->ЗначениеРеквизита)) {
            foreach ($elm->ЗначенияРеквизитов->ЗначениеРеквизита as $props) {
                if ($props->Наименование->__toString() == 'Код') {
                    $this->goods[$id]['article'] = $props->Значение->__toString();
                }
            }
        }

        if (is_null($this->goods[$id]['article']) || empty($this->goods[$id]['article'])) {
            $article = null;

            if ($elm->Код && !empty($elm->Код)) {
                $article = $elm->Код->__toString();
            }
            if (is_null($article) && $elm->Артикул && !empty($elm->Артикул)) {
                $article = $elm->Артикул->__toString();
            }

            $this->goods[$id]['article'] = $article;
        }

        if (count($elm->ЗначенияСвойств->ЗначенияСвойства)) {
            foreach ($elm->ЗначенияСвойств->ЗначенияСвойства as $prop) {
                $code = $prop->Значение->__toString();

                if ('77a5adc5-b75c-11e7-8404-001e67647f08' == $code) { #_Удалить с сайта
                    $this->goods[$id]['delete'] = true;
                }
                if ('2a842d78-e0c1-11e3-b038-001e67647f08' == $code) { #Удален
                    $this->goods[$id]['delete'] = true;
                }

            }
        }

    }

    protected function offersGoods($elm)
    {
        $id = $elm->Ид->__toString();

        if (strpos($id, '#') != false) {
            $ids = explode('#', $id);
            $id = $ids[0];
            $sub = $ids[1];

            $price = $elm->Цены->Цена->ЦенаЗаЕдиницу->__toString();
            $amount = (int)$elm->Количество->__toString();
            $name = $elm->Наименование->__toString();

            $this->goods[$id]['products'][$sub]['id'] = $sub;
            if ($price)
                $this->goods[$id]['products'][$sub]['price'] = $price;
            if ($amount || $amount == 0)
                $this->goods[$id]['products'][$sub]['amount'] = $amount;
            if ($name)
                $this->goods[$id]['products'][$sub]['name'] = $name;

        } else {
            $this->goods[$id]['id'] = $id;
            $this->goods[$id]['price'] = $elm->Цены->Цена->ЦенаЗаЕдиницу->__toString();
            $this->goods[$id]['amount'] = (int)$elm->Количество->__toString();
            $this->goods[$id]['name'] = $elm->Наименование->__toString();
        }
    }

    /**
     * @param $external_id
     * @return Goods
     */
    protected function getGoodsSiteByExternal($external_id)
    {
        return Goods::model()->where('external_id', '=', $external_id)->find();
    }

    /**
     * @param $article
     * @return Goods
     */
    // @changes
    protected function getGoodsSiteByArticle($article)
    {
        return Goods::model()->where('article', 'LIKE', '%'.$article.'%')->find();
    }

    protected function getAllGoodsByArticle($article)
    {
        return Goods::model()->where('article', 'LIKE', '%'.$article.'%')->find_all();
    }

    protected function getGoodsByItem($item, $isCollection = false, $parent = null)
    {
        return $this->getGoodsSiteByExternal($item['id']);

        // @note все что дальше - уже не нужно
        // запустить код в том случае, если случился очередной переход на 1С
        // надеюсь, это больше не понадобится.
        // а вообще, по-хорошему, при новом переезде просто сделать перечисление новых
        // идентификаторов из 1С на созданные товары
        
        if ($isCollection) {
            $goods = $this->getGoodsSiteByExternal($item['id']);
            if ($goods->loaded()) {
                return $goods;
            }

            $article = trim($parent['article']);
            $goods = $this->getAllGoodsByArticle($article);
            if ($goods->count() == 1) {
                return $goods->current();
            }
            if ($goods->count() == 0) {
                return $this->getGoodsSiteByArticle('100500undefined-article-ever100500');
            }

            $goodsName = $goods->current()->name;
            $compare = -1;
            $goodsItem = null;
            while (!is_null($goodsName)) {
                $result = similar_text($goodsName, $item['name']);
                if ($result > $compare) {
                    $compare = $result;
                    $goodsItem = $goods->current();
                };

                $goods->next();
                $goodsName = $goods->current()->name;
            }
         
            return $goodsItem;
        }

        $goods = $this->getGoodsSiteByExternal($item['id']);
        if ($goods->loaded()) {
            return $goods;
        }

        if (is_null($item['article'])) {
            return $this->getGoodsSiteByArticle('100500undefined-article-ever100500');
        }

        $article = trim($item['article']);
        $goods = $this->getAllGoodsByArticle($article);
        if ($goods->count() == 0) {
            return $this->getGoodsSiteByArticle('100500undefined-article-ever100500');
        }
        if ($goods->count() == 1) {
            return $goods->current();
        }

        return $this->getGoodsSiteByArticle('100500undefined-article-ever100500');
    }


    /**
     * @param $external_id
     * @return CollectionProduct
     */
    protected function getGoodsCollectionByExternal($external_id)
    {
        return CollectionProduct::model()->where('external_id', '=', $external_id)->find();
    }

    /**
     * @param $abbr
     * @return int|mixed
     * @throws Error
     */
    protected function getUnitIdByAbbr($abbr)
    {
        $unit = Unit::model()->where('abbr', 'LIKE', $abbr . '%')->find();
        return $unit->loaded() ? $unit->pk() : 0;
    }


    protected function updateGoods()
    {
        $this->log('updateGoods');
        $this->log('count = ' . count($this->goods));
        foreach ($this->goods as $hash => $item) {
            if ($this->check_goods_mono($item)) {
                $this->inset_mono_goods($item, $hash);
            } else {
                $this->inset_collection_goods($item, $hash);
            }
        }
    }

    /**
     * @param $item
     * @throws Error
     */
    protected function inset_mono_goods($item, $hask)
    {
        $goods = $this->getGoodsByItem($item);

        if ($this->is_delete_goods($item) && $goods->loaded()) {
            if ($goods->moder == 1 && $goods->status == 0) {
                $goods->delete();
                $this->log('delete external_id' . $item['id'] . ' name = ' . $item['name']);
            } elseif ($goods->status == 1) {
                $goods->status = 0;
                $goods->save();
                $this->log('offline external_id' . $item['id'] . ' name = ' . $item['name']);
            }
            return;
        }

        try {
            if ($goods->loaded()) {
                $goods = $this->updateProduct($goods, $item);
                $this->log('update external_id: ' . $item['id'] . ' name = ' . $item['name']);
            } else {
                $goods = $this->addProduct($item);
                $this->log('add external_id: ' . $item['id'] . ' name = ' . $item['name']);
            }
        } catch (OrmValidationError $e) {
            $this->log($e->getErrorsMessage());
            $this->log('error inset_mono_goods external_id: ' . $item['id'] . ' name = ' . $item['name']);
        }

    }

    /**
     * @param $items
     * @throws Error
     */
    protected function inset_collection_goods($items, $hash)
    {
        $collection = $this->getGoodsCollectionByExternal($hash);
        if (!$collection->loaded()) {
            if (!$items['name']) {
                $this->log('not name collection ' . $hash);
                return;
            }
            $collection->name = $items['name'] ? $items['name'] : "Не указано";
            $collection->label = $this->getCollectionLabel($items);
            $collection->external_id = $hash;
            try {
                $collection->save();
            } catch (OrmValidationError $e) {

                $this->log($e->getErrorsMessage());
                $this->log('Error Add collection  external_id: ' . $hash . ' name = ' . $items['name']);;
            }
            $this->log('add collection ' . $collection->name . ' label = ' . $collection->label);
        }

        $goods_test = $this->getGoodsSiteByExternal($hash);

        foreach ($items['products'] as $item) {
            $goods = $this->getGoodsByItem($item, true, $items);
            
            $tmp = array();
            $tmp['id'] = $item['id'];
            $tmp['name'] = $item['name'];
            $tmp['price'] = $item['price'];
            $tmp['amount'] = $item['amount'];
            $tmp['article'] = $items['article'];
            $tmp['t_article'] = $items['t_article'];
            $tmp['a_articles'] = $items['a_articles'];

            if ($goods_test != null && $goods_test->loaded() && $goods->loaded()) {
                $del = new Goods($goods->pk());
                $del->delete();
                $goods = $goods_test;
                $goods->external_id = $item['id'];
                $goods_test = null;
            }

            try {

                if ($goods->loaded()) {
                    $goods = $this->updateProduct($goods, $tmp, $collection);
                    $this->log('update coll external_id' . $tmp['id'] . ' name = ' . $tmp['name']);
                } else {
                    #новый продукт
                    $goods = $this->addProduct($tmp, $collection);
                    $this->log('add coll external_id' . $tmp['id'] . ' name = ' . $tmp['name']);
                }
            } catch (OrmValidationError $e) {

                $this->log($e->getErrorsMessage());
                $this->log(' external_id: ' . $item['id'] . ' name = ' . $item['name']);
            }
        }
    }

    /**
     * @param Goods $product
     * @param $data
     * @return Goods
     * @throws Error
     */
    protected function updateProduct(Goods $product, $data, $collection = null)
    {
        if ($this->is_delete_goods($data)) {
            if ($product->moder == 1 && $product->status == 0) {
                $product->delete();
                $this->log('delete coll external_id' . $data['id'] . ' name = ' . $data['name']);
            } elseif ($product->status == 1) {
                $product->status = 0;
                $product->save();
                $this->log('offline coll external_id' . $data['id'] . ' name = ' . $data['name']);
            }
            return;
        }

        if ($product->external_change) {
            if ($data['name']) {
                $product->name = $data['name'];
            }
            if (isset($data['amount']))
                $product->amount = (int)$data['amount'];
            if (isset($data['price']))
                $product->value = $data['price'];
            if (isset($data['article']))
                $product->article = $data['article'];
            if (isset($data['t_article']))
                $product->t_article = $data['t_article'];
            if (isset($data['a_articles']))
                $product->a_articles = $data['a_articles'];

            $product->update = 1;
            if (!$product->moder)
                $product->status = 1;


            try {
                $product->save();

            } catch (OrmValidationError $e) {
                $this->log($e->getErrorsMessage());
                $this->log('Error updateProduct -  external_id: ' . $data['id'] . ' name = ' . $data['name']);
            }

            if ($data['brief'] && false) {
                $section = Section::model()
                    ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId(Goods::model()->table_name()))
                    ->where('target_id', '=', $product->pk())
                    ->order_pk()
                    ->find();
                if ($section->loaded() && !$section->text) {
                    $section->text = JevixEasy::Parser($data['brief']);
                    $section->save();
                } else {
                    $section->name = 'Описание';
                    $section->text = JevixEasy::Parser($data['brief']);
                    $section->target_id = $product->pk();
                    $section->target_type = \CMS\Core\Helper\Helpers::getTableId(Goods::model()->table_name());
                    $section->save();
                }
            }

        } else {

//            if (isset($data['amount']))
//                $product->amount = (int)$data['amount'];
//            if (isset($data['price']))
//                $product->value = $data['price'];

            $product->update = 1;
//            if (!$product->moder)
//                $product->status = 1;
            try {
                $product->save();

            } catch (OrmValidationError $e) {
                $this->log($e->getErrorsMessage());
                $this->log('Error updateProduct -  external_id: ' . $data['id'] . ' name = ' . $data['name']);
            }
        }

        if ($collection) {

            $it = CollectionProductItem::model()
                ->where('product_id', '=', $product->pk())
                ->where('coll_id', '=', $collection->pk())
                ->find();

            if (!$it->loaded() && $product->name && $collection->name) {

                try {
                    #добавили к цепочке
                    $collection->addItem(array(
                        'name' => $this->getCollectionValue($product->name, $collection->name),
                        'product_id' => $product->pk()
                    ));
                } catch (OrmValidationError $e) {
                    $this->log($e->getErrorsMessage());
                    $this->log('Error updateProduct add collection -  external_id: ' . $product->external_id . ' name = ' . $product->name);
                }


            }
        }

        return $product;

    }

    /**
     * @param $data
     * @return Goods
     * @throws Error
     */
    protected function addProduct($data, $collection = null)
    {
        if ($this->is_delete_goods($data)) {
            return;
        }
        $product = new Goods();
        $product->name = $data['name'];
        $product->external_id = $data['id'];
        $product->article = $data['article'];
        $product->unit_id = $this->getUnitIdByAbbr($data['unit']);
        $product->vendor_id = $this->getVendorSiteByName($data['vendor']);
        $product->amount = 0.0;
        
        if (isset($data['amount']))
            $product->amount = (int)$data['amount'];
        if (isset($data['price']))
            $product->value = $data['price'];
        if (empty($product->vendor_id))
            $product->vendor_id = '0';
        if (isset($data['t_article']))
            $product->t_article = $data['t_article'];
        if (isset($data['a_articles']))
            $product->a_articles = $data['a_articles'];

        $product->status = 0;
        $product->update = 1;
        $product->moder = 1;
        // @change
        $product->cid = 0;

        try {
            $product->save();

        } catch (OrmValidationError $e) {
            $this->log($e->getErrorsMessage());
            $this->log('Error addProduct -  external_id: ' . $data['id'] . ' name = ' . $data['name']);
        }

        if ($data['brief'] && false) {
            $section = Section::model()
                ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId(Goods::model()->table_name()))
                ->where('target_id', '=', $product->pk())
                ->order_pk()
                ->find();
            if ($section->loaded() && !$section->text) {
                $section->text = JevixEasy::Parser($data['brief']);
                $section->save();
            } else {
                $section->name = 'Описание';
                $section->text = JevixEasy::Parser($data['brief']);
                $section->target_id = $product->pk();
                $section->target_type = \CMS\Core\Helper\Helpers::getTableId(Goods::model()->table_name());
                $section->save();
            }
        }


        if ($collection) {
            #добавили к цепочке
            $collection->addItem(array(
                'name' => $this->getCollectionValue($product->name, $collection->name),
                'product_id' => $product->pk()
            ));

            #характеристики
            $cpi = CollectionProductItem::model()
                ->select('product_id')
                ->where('coll_id', '=', $collection->pk())
                ->sort()
                ->find();

            $characs = CharacteristicsGoods::model()
                ->select('character_id', 'value_id')
                ->where('target_id', '=', $cpi['product_id'])
                ->where('target_type', '=', Helpers::getTableId(Goods::model()))
                ->find_all();

            foreach ($characs as $chara) {
                $product->addCharacteristics(
                    array(
                        'character_id' => $chara['character_id'],
                        'value_id' => $chara['value_id'],
                    )
                );
            }
        }

        return $product;

    }


    /**
     * @param $item
     * @return string
     */
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

    /**
     * @param $item
     * @return bool
     */
    protected function check_goods_mono($item)
    {
        return count($item['products']) == 0 ? true : false;
    }

    protected function is_delete_goods($item)
    {
        return $item['delete'];
    }

    /** Парсинг каталог */
    protected $categories = array();

    /**
     * @param $elm
     * @param int $pid
     */
    protected function importCategories($elm, $pid = 0)
    {
        if (!$elm->Ид) {
            return;
        }

        $id = $elm->Ид[0]->__toString();
        $name = $elm->Наименование[0]->__toString();

        $this->categories[$id] = $id;
        $this->categories[$pid] = $pid;

        if ($name)
            $this->categories['name'] = $name;

        if (isset($elm->Группы)) {
            foreach ($elm->Группы->Группа as $elms) {
                $this->importCategories($elms, $elm->Ид[0]->__toString());
            }
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws Error
     */
    protected function getCategorySiteByName($name)
    {
        return Category::model()->where('name', '=', $name)->find()->pk();
    }

    /**
     * @param $name
     * @return mixed
     * @throws Error
     */
    protected function getVendorSiteByName($name)
    {
        return Vendor::model()->where('name', '=', $name)->find()->pk();
    }

}