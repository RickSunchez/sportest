<?php

namespace Shop\Commodity\Helpers;

use Delorius\Core\Environment;
use Delorius\DataBase\DB;
use Delorius\Http\SessionSection;
use Shop\Commodity\Entity\Goods;
use Shop\Store\Entity\Item;
use Shop\Store\Helper\OrderHelper;

class Popular
{

    /**
     * Просмотр товара
     * @param $product_id
     */
    public static function view($product_id)
    {
        $section = self::getSession();
        $view = $section->view;
        if (!$view[$product_id]) {
            $view[$product_id] = true;
            $section->view = $view;
            $count = self::getParam('view');
            self::update($product_id, $count);
        }

    }

    /**
     * Добавить в корзину
     * @param $product_id
     */
    public static function add_cart($product_id)
    {
        $section = self::getSession();
        $view = $section->add_cart;
        if (!$view[$product_id]) {
            $view[$product_id] = true;
            $section->add_cart = $view;
            $count = self::getParam('add_cart');
            self::update($product_id, $count);
        }
    }

    /**
     * Добавить в корзину
     * @param $product_id
     */
    public static function one_click($product_id)
    {
        $section = self::getSession();
        $view = $section->one_click;
        if (!$view[$product_id]) {
            $view[$product_id] = true;
            $section->one_click = $view;
            $count = self::getParam('one_click');
            self::update($product_id, $count);
        }
    }


    public static function order($order_id, $status)
    {
        $config = OrderHelper::getStatusById($status);
        if ($config['popular'] != 0) {

            $items = Item::model()->select('goods_id')->where('order_id', '=', $order_id)->find_all();
            foreach ($items as $item) {
                self::update($item['goods_id'], $config['popular']);
            }

        }

    }

    /**
     * @return SessionSection
     */
    protected static function getSession()
    {
        return Environment::getContext()->getService('session')->getSection('_popular');
    }

    /**
     * @param string $name
     */
    protected static function getParam($name)
    {
        $popular = Environment::getContext()->getParameters('popular');
        return isset($popular[$name]) ? $popular[$name] : 0;
    }

    /**
     * Кол-во к популярности
     * @param int $popular
     */
    public static function update($product_id, $popular = 1)
    {
        $goods = Goods::model();
        DB::update($goods->table_name())
            ->set(array('popular' => DB::expr('`popular` + ' . $popular)))
            ->where('goods_id', '=', $product_id)
            ->execute($goods->db_config());
    }
}

