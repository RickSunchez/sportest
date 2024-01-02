<?php

namespace Shop\Catalog\Helpers;


use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;

class Shop
{

    public static function isShowCollection($get)
    {
        return false;
    }


    /**
     * @var array
     */
    private static $_categories_list = array();

    public static function getCategoriesListStr($glue = '/', $cid, $type_id = Category::TYPE_GOODS)
    {
        if (!$cid) {
            return '';
        }

        if (!self::$_categories_list[$type_id]) {
            $orm = Category::model()
                ->type($type_id)
                ->cached()
                ->select('cid', 'pid', 'name', 'type_id')
                ->active()
                ->find_all();
            self::$_categories_list[$type_id] = Arrays::resultAsArrayKey($orm, 'cid');
        }

        if (isset(self::$_categories_list[$type_id][$cid])) {
            $category = Category::mock(self::$_categories_list[$type_id][$cid]);
            $parents = $category->getParents();
            if (count($parents) > 1)
                $parents = array_reverse($parents);

            $names = array();
            foreach ($parents as $item) {
                $names[] = $item['name'];
            }

            $names[] = $category->name;
            if (count($names) == 1) {
                return $category->name;
            }

            return implode($glue, $names);
        }

        return '';
    }

}