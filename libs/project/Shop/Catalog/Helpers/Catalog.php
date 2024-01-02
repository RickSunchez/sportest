<?php

namespace Shop\Catalog\Helpers;

use Delorius\DataBase\DB;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryMulti;
use Shop\Commodity\Entity\Goods;

class Catalog
{

    /**
     * Обновления данных каталога
     * @param bool|true $active
     */
    public static function counted($active = true)
    {
        self::nulled();
        self::countedGoods($active);
        self::countedChildren();
    }

    /**
     * Пересчитать кол-во дочерних категорий
     * @param bool|true $active
     */
    public static function countedChildren()
    {
        $counted = self::getCountedChildren();
        $model = Category::model();
        foreach ($counted as $id => $children) {
            DB::update($model->table_name())
                ->where('cid', '=', $id)
                ->value('children', $children)
                ->execute($model->db_config());
        }
        $model->cache_delete();
    }

    /**
     * id:count
     * @param bool|true $active
     * @return array
     * @throws \Delorius\Exception\Error
     */
    public static function getCountedChildren($active = true)
    {
        $orm = Category::model()->select('pid', 'cid', 'goods');
        $cats = array();
        foreach ($orm->find_all() as $item) {
            if ($active && $item['goods'])
                $cats[$item['pid']][$item['cid']] = $item;
            elseif (!$active)
                $cats[$item['pid']][$item['cid']] = $item;
        }

        $counted = array();
        foreach ($orm->find_all() as $item) {
            $counted[$item['cid']] = count($cats[$item['cid']]);
        }

        return $counted;
    }

    /**
     *  Обновления кол-во товаров
     * @param bool|true $active
     * @return array
     * @throws \Delorius\Exception\Error
     */
    public static function getCountedGoods($active = true)
    {

        $categories = Category::model()->select('pid', 'cid');
        $cats = array();
        foreach ($categories->find_all() as $item) {
            $cats[$item['cid']] = $item;
        }

        self::$cats = $cats;

        $multi = Goods::model()->isMulti();

        if ($multi) {

            $goods = Goods::model()->select('goods_id', 'popular');
            if ($active)
                $goods->active();
            $products = Arrays::resultAsArrayKey($goods->find_all(), 'goods_id');


            $multiGoods = CategoryMulti::model()->select()->find_all();
            $ids = array();
            foreach ($multiGoods as $item) {
                $ids[$item['cid']]['goods'] += isset($products[$item['product_id']]) ? 1 : 0;
                $ids[$item['cid']]['popular'] += $products[$item['product_id']]['popular'];
            }

        } else {

            $goods = Goods::model()->select('cid', 'popular');
            if ($active)
                $goods->active();
            $ids = array();
            foreach ($goods->find_all() as $item) {
                $ids[$item['cid']]['goods'] += 1;
                $ids[$item['cid']]['popular'] += $item['popular'];
            }

        }

        foreach ($ids as $cid => $goods) {
            if (isset(self::$cats[$cid]) && count(self::$cats[$cid])) {
                self::$cats[$cid]['goods'] += $goods['goods'];
                self::$cats[$cid]['popular'] += $goods['popular'];
                self::setParentGoods(self::$cats[$cid]['pid'], $goods);
            }
        }
        return self::$cats;
    }

    protected static $cats = array();

    /**
     * Пересчитать кол-во дочерних категорий
     * @param bool|true $active
     */
    public static function countedGoods($active = true)
    {
        $counted = self::getCountedGoods($active);
        $model = Category::model();
        foreach ($counted as $id => $cat) {
            DB::update($model->table_name())
                ->where('cid', '=', $id)
                ->value('goods', (int)$cat['goods'])
                ->value('popular', (int)$cat['popular'])
                ->value('status', (int)$cat['goods'] > 0 ? 1 : 0)
                ->execute($model->db_config());
        }
        $model->cache_delete();
    }

    protected static function setParentGoods($cid, $goods)
    {

        if (isset(self::$cats[$cid]) && count(self::$cats[$cid])) {
            self::$cats[$cid]['goods'] += $goods['goods'];
            self::$cats[$cid]['popular'] += $goods['popular'];
            self::setParentGoods(self::$cats[$cid]['pid'], $goods);
        }
    }

    /**
     * Обнуляет данные каталога
     */
    protected static function nulled()
    {
        $model = Category::model();
        DB::update($model->table_name())
            ->value('children', 0)
            ->value('goods', 0)
            ->value('popular', 0)
            ->execute($model->db_config());
    }

}