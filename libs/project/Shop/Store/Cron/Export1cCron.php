<?php
namespace Shop\Store\Cron;

use CMS\Core\Entity\Image;
use CMS\Core\Helper\Jevix\JevixEasy;
use Delorius\Core\Cron;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Finder;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Helpers\Catalog;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Section;

use Shop\Commodity\Entity\Unit;

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');

class Export1cCron extends Cron
{
    protected $_lockdown_time = 1; // minutes

    protected function client()
    {
        $path_dir = realpath(Environment::getContext()->getParameters('path.export'));
        $this->log('step1');
        if (file_exists($path_dir . '/.lock')) {
            $time = filemtime($path_dir . '/.lock') + (60 * $this->_lockdown_time);
            if ($time > time()) {
                $this->log('Директория импорта заблокирована');
                throw new Error('Директория импорта заблокирована');
            }
        }

        $path_dir_export = $path_dir;
        $this->log('step2');
        if (file_exists($path_dir . '/.lock')) {

            /** @var \Delorius\Configure\File\Config $cfg */
            $cfg = Environment::getContext()->getService('config')->deliver('import');


            if (file_exists($path_dir_export . '/import.xml')) {
                $this->log('init catalog');
                /** Import Catalog return (array)$this->categories */
                $xml = @simplexml_load_file($path_dir_export . '/import.xml');
                if (!$xml) throw new Error('incorrect XML file "' . $path_dir_export . '/import.xml "');
                if (count($xml->Классификатор->Группы->Группа)) {
                    foreach ($xml->Классификатор->Группы->Группа as $elm) {
                        $this->importCategories($elm);
                    }

                    $cfg->set('categories', $this->categories);
                    $cfg->save();
                    FileSystem::delete($path_dir_export . '/import.xml');
                }
            }

            $this->goods = $cfg->get('goods');

            $lvl = 1;
            $this->log($path_dir_export . '/' . $lvl . '/import.xml' . '<br />');
            while (
                file_exists($path_dir_export . '/' . $lvl . '/import.xml') &&
                file_exists($path_dir_export . '/' . $lvl . '/offers.xml')
            ) {

                if (file_exists($path_dir_export . '/' . $lvl . '/.lock')) {
                    $lvl++;
                    continue;
                }

                $this->log('init goods');
                $import = @simplexml_load_file($path_dir_export . '/' . $lvl . '/import.xml');
                if (!$import) {
                    $this->log('incorrect XML file "' . $path_dir_export . '/' . $lvl . '/import.xml" ');
                    $this->log($import);

                } else {

                    if (count($import->Каталог->Товары->Товар)) foreach ($import->Каталог->Товары->Товар as $goods) {
                        $this->importGoods($goods, realpath($path_dir_export));
                    }

                    $offers = @simplexml_load_file($path_dir_export . '/' . $lvl . '/offers.xml');
                    if (!$offers) {
                        $this->log('incorrect XML file "' . $path_dir_export . '/' . $lvl . '/offers.xml" ');
                        $this->log($offers);
                    } else {
                        if (count($offers->ИзмененияПакетаПредложений->Предложения->Предложение))
                            foreach ($offers->ИзмененияПакетаПредложений->Предложения->Предложение as $offer) {
                                $this->offersGoods($offer);
                            }
                    }
                }


                $cfg->set('goods', $this->goods);
                $cfg->save();
                FileSystem::write($path_dir_export . '/' . $lvl . '/.lock', date('d.m.Y H:i'));

                $this->log($path_dir_export . '/' . $lvl . '/ - end');

                $lvl++;
            }


            $this->categories = $cfg->get('categories');
            if (count($this->categories)) {
                $this->updateCategories();
                $this->syncCatalog();
                $cfg->set('categories', null);
            }

            $this->goods = $cfg->get('goods');
            if (count($this->goods)) {
                $this->updateGoods();
                $cfg->set('goods', null);
            }

            $files = Finder::find('*')->in($path_dir);
            foreach ($files as $f) {
                FileSystem::delete($f->getPathname());
            }

            Environment::getContext()->getService('sitemaps')->create();

            $this->log('end gen file xml');
            Catalog::counted();
            $this->log('sync end catalog');

        }


    }


    /**
     * @param $external_id
     * @return Category
     */
    protected function getCategorySiteByExternal($external_id)
    {
        return Category::model()->where('external_id', '=', $external_id)->find();
    }

    protected function syncCatalog()
    {
        $this->log('syncCatalog start');
        $categories = Arrays::resultAsArrayKey(
            Category::model()
                ->where('external_change', '=', 1)
                ->find_all(),
            'external_id'
        );

        foreach ($this->categories as $id => $cats) {
            foreach ($cats as $cat) {
                if (isset($categories[$cat['id']])) {
                    unset($categories[$cat['id']]);
                }
            }
        }

        if (sizeof($categories)) {
            foreach ($categories as $item) {
                $item->status = 0;
                $item->save();
                $this->log('cid = ' . $item->pk() . ' , status = 0');
                $item = null;
            }
        }
        $this->log('syncCatalog end');

    }

    protected function updateCategories($pid = 0)
    {
        $this->log('updateCategories pid=' . $pid);
        if (sizeof($this->categories[$pid])) {
            foreach ($this->categories[$pid] as $categories) {
                $category = $this->getCategorySiteByExternal($categories['id']);
                if ($category->loaded()) {
                    if ($category->external_change) {

                        if ($categories['pid']) {
                            $parent = $this->getCategorySiteByExternal($categories['pid']);
                            $parent_id = $parent->pk();
                            $category->pid = $parent_id;
                        }
                        $category->name = $categories['name'];
                        $category->save();
                    }
                } else {
                    $parent_id = 0;
                    if ($categories['pid']) {
                        $parent = $this->getCategorySiteByExternal($categories['pid']);
                        $parent_id = $parent->pk();
                    }
                    $category->external_id = $categories['id'];
                    $category->name = $categories['name'];
                    $category->pid = $parent_id;
                    $category->type_id = Category::TYPE_GOODS;
                    try {
                        $category->save();
                    } catch (OrmValidationError $e) {
                        $this->log($e->getErrorsMessage());
                    }
                }
                $category = null;
                $this->updateCategories($categories['id']);
            }
        }
    }


    /** Парсинг товаров */
    protected $goods = array();

    protected function importGoods($elm, $dir)
    {
        $cid = 0;
        foreach ($elm->Группы->Ид as $id) {
            $cid = $id->__toString();
        }

        $weight = 0;
        foreach ($elm->ЗначенияРеквизитов->ЗначениеРеквизита as $item) {
            if ($item->Наименование == 'Вес') {
                $weight = $item->Значение->__toString();
            }
        }
        $id = $elm->Ид->__toString();
        $this->goods[$id]['id'] = $id;
        $this->goods[$id]['article'] = $elm->Артикул->__toString();
        $this->goods[$id]['name'] = $elm->Наименование->__toString();
        $this->goods[$id]['unit'] = $elm->БазоваяЕдиница->__toString();
        $this->goods[$id]['brief'] = $elm->Описание->__toString();
        $this->goods[$id]['delete'] = $elm->Статус->__toString() == 'Удален' ? true : false;
        $this->goods[$id]['image'] = $elm->Картинка->__toString() ?
            $dir . '/' . $elm->Картинка->__toString()
            : null;
        $this->goods[$id]['cid'] = $cid;
        $this->goods[$id]['weight'] = $weight;

    }

    protected function offersGoods($elm)
    {
        $id = $elm->Ид->__toString();
        $this->goods[$id]['id'] = $id;
        $this->goods[$id]['price'] = $elm->Цены->Цена->ЦенаЗаЕдиницу->__toString();
    }


    /** Парсинг каталог */
    protected $categories = array();

    protected function importCategories($elm, $pid = 0)
    {
        if (!$elm->Ид) {
            return;
        }

        $arr = array(
            'id' => $elm->Ид[0]->__toString(),
            'name' => $elm->Наименование[0]->__toString(),
            'pid' => $pid,
        );
        $this->categories[$pid][] = $arr;
        if (isset($elm->Группы)) {
            foreach ($elm->Группы->Группа as $elms) {
                $this->importCategories($elms, $elm->Ид[0]->__toString());
            }
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

    protected function getUnitIdByAbbr($abbr)
    {
        $unit = Unit::model()->where('abbr', 'LIKE', $abbr . '%')->find();
        return $unit->loaded() ? $unit->pk() : 0;
    }

    protected function updateGoods()
    {
        $this->log('updateGoods');
        $this->log('count = ' . count($this->goods));
        foreach ($this->goods as $item) {
            if ($this->check_goods($item)) {
                $goods = $this->getGoodsSiteByExternal($item['id']);
                if ($this->is_delete_goods($item)) {
                    if ($goods->loaded()) {
                        $goods->status = 0;
                        $goods->save();
                    }
                    continue;
                }
                try {
                    if ($goods->loaded()) {
                        if ($goods->external_change) {
                            $goods->status = 1;
                            $goods->name = $item['name'];
                            $goods->article = $item['article'];
                            $cat = $this->getCategorySiteByExternal($item['cid']);
                            $goods->cid = $cat->loaded() ? $cat->pk() : 0;
                            $goods->unit_id = $this->getUnitIdByAbbr($item['unit']);
                            if ($item['amount'])
                                $goods->amount = $item['amount'];
                            else
                                $goods->amount = 1;

                            if ($item['price'])
                                $goods->value = $item['price'];

                            $goods->weight = $item['weight'];
                            $goods->update = 1;
                            $goods->save();

                            $img = $goods->getImages();
                            if (count($img) == 0 && $item['image'] && is_file($item['image'])) {
                                $image = $goods->addImagePath($item['image']);
                                if ($image) {
                                    $image = new Image($image['image_id']);
                                    if ($image->loaded()) {
                                        $image->main = 1;
                                        $image->save();
                                        $image = null;
                                    }
                                }
                            }

                            if ($item['brief']) {
                                $section = Section::model()
                                    ->where('target_type', '=', Goods::model()->table_name())
                                    ->where('target_id', '=', $goods->pk())
                                    ->order_pk()
                                    ->find();
                                if ($section->loaded()) {
                                    $section->text = JevixEasy::Parser($item['brief']);
                                    $section->save();
                                } else {
                                    $section->name = 'Описание товара';
                                    $section->text = JevixEasy::Parser($item['brief']);
                                    $section->target_id = $goods->pk();
                                    $section->target_type = Goods::model()->table_name();
                                    $section->save();
                                }
                            }
                        } else {

                            if ($item['amount'])
                                $goods->amount = $item['amount'];
                            if ($item['price'])
                                $goods->value = $item['price'];
                            $goods->update = 1;
                            $goods->status = 1;
                            $goods->save();
                        }
                    } else {

                        $goods->status = 1;
                        $goods->external_id = $item['id'];
                        $goods->weight = $item['weight'];
                        $goods->name = $item['name'];
                        $goods->article = $item['article'];
                        $cat = $this->getCategorySiteByExternal($item['cid']);
                        $goods->cid = $cat->loaded() ? $cat->pk() : 0;
                        $goods->unit_id = $this->getUnitIdByAbbr($item['unit']);
                        if ($item['amount'])
                            $goods->amount = $item['amount'];
                        else
                            $goods->amount = 1;

                        if ($item['price'])
                            $goods->value = $item['price'];
                        $goods->update = 1;
                        $goods->save();

                        $section = new Section();
                        $section->name = 'Описание товара';
                        $section->text = JevixEasy::Parser($item['brief']);
                        $section->target_id = $goods->pk();
                        $section->target_type = Goods::model()->table_name();
                        $section->save();

                        if ($item['image'] && is_file($item['image'])) {
                            $image = $goods->addImagePath($item['image']);
                            if ($image) {
                                $image = new Image($image['image_id']);
                                if ($image->loaded()) {
                                    $image->main = 1;
                                    $image->save();
                                    $image = null;
                                }
                            }
                        }
                    }
                } catch (OrmValidationError $e) {
                    $this->log($e->getErrorsMessage());
                    $this->log('cid=' . $cat->pk() . ' , external_id' . $item['id'] . ' name = ' . $item['name']);
                }
                $goods = null;
            }
        }
    }

    protected function check_goods($item)
    {
        if (!$item['name'] || !$item['price']) {
            return false;
        }
        return true;
    }

    protected function is_delete_goods($item)
    {
        return $item['delete'];
    }
}