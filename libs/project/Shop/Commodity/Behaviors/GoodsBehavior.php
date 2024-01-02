<?php

namespace Shop\Commodity\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Utils\Arrays;
use Shop\Catalog\Helpers\Shop;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Unit;

class GoodsBehavior extends ORMBehavior
{

    public function beforeSave(ORM $orm)
    {
        $orm->value_system = $this->currency->convert($orm->value, $orm->code ? $orm->code : SYSTEM_CURRENCY, SYSTEM_CURRENCY);
        $orm->is_amount = $orm->amount > 0 ? 1 : 0;
    }

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * @param array $get Http Query
     * @return \Shop\Commodity\Entity\Goods
     */
    public function sort($get = array())
    {
        /** @var \Shop\Commodity\Entity\Goods $orm */
        $orm = $this->getOwner();
        $table_name = $orm->table_name();

        $orm->order_by($table_name . '.is_amount', 'desc')
            ->order_by($table_name . '.pos', 'desc');

        if (!sizeof($get)) {
            $orm->order_by($table_name . '.popular', 'desc');
        } elseif ($get['sort'] == 'price' && Arrays::keysAnyoneExists('asc|desc', array($get['order'] => 0))) {
            $orm->order_by($table_name . '.value_system', $get['order'] == 'desc' ? $get['order'] : null);
        } elseif ($get['sort'] == 'name' && Arrays::keysAnyoneExists('asc|desc', array($get['order'] => 0))) {
            $orm->order_by($table_name . '.name', $get['order']);
        } else {
            $orm->order_by($table_name . '.popular', 'desc');
        }

        $orm->order_pk('desc');

        return $orm;
    }

    /**
     * @return \Shop\Commodity\Entity\Goods
     */
    public function sortByPopular()
    {
        /** @var \Shop\Commodity\Entity\Goods $orm */
        $orm = $this->getOwner();
        $table_name = $orm->table_name();

        $orm->order_by($table_name . '.is_amount', 'desc')
            ->order_by($table_name . '.pos', 'desc')
            ->order_by($table_name . '.popular', 'desc')
            ->order_pk('desc');
        return $orm;
    }

    /**
     * @param array $get Http Query
     * @return \Shop\Commodity\Entity\Goods
     */
    public function filters($get = array())
    {
        /** @var \Shop\Commodity\Entity\Goods $orm */
        $orm = $this->getOwner();

        if (!sizeof($get)) {
            return $orm;
        }


        if (count($get['vendors'])) {
            $ids = array();
            foreach ($get['vendors'] as $vendor) {
                $ids[] = (int)$vendor;
            }
            $orm->where($orm->table_name() . '.vendor_id', 'in', $ids);
        }

        if (count($get['cats']) && !$orm->isMulti()) {
            $ids = array();
            foreach ($get['cats'] as $cat) {
                $ids[] = (int)$cat;
            }
            $orm->where($orm->table_name() . '.cid', 'in', $ids);
        }

        if (isset($get['price_max'])) {
            $value_max = $this->currency->convert($get['price_max'], $this->currency->getCode(), SYSTEM_CURRENCY);
            $orm->where($orm->table_name() . '.value_system', '<=', $value_max);
        }

        if (isset($get['price_min'])) {
            $value_min = $this->currency->convert($get['price_min'], $this->currency->getCode(), SYSTEM_CURRENCY);
            $orm->where($orm->table_name() . '.value_system', '>=', $value_min);
        }

        //feature
        if (count($get['feature'])) {
            $chara_goods = CharacteristicsGoods::model()
                ->select()
                ->where('target_type', '=', Helpers::getTableId(Goods::model()))
                ->cached();
            $chara_goods->and_where_open();
            foreach ($get['feature'] as $feature_id => $value) {
                if (is_array($value)) {
                    $chara_goods->or_where_open();
                    $chara_goods->where('character_id', '=', (int)$feature_id);
                    $chara_goods->where_open();
                    foreach ($value as $key => $id) {
                        if ((int)$id) {
                            $chara_goods->or_where('value_id', '=', (int)$id);
                        }
                    }
                    $chara_goods->where_close();
                    $chara_goods->or_where_close();

                } else {
                    if ((int)$value) {
                        $chara_goods->or_where_open()
                            ->where('character_id', '=', (int)$feature_id)
                            ->where('value_id', '=', (int)$value)
                            ->or_where_close();
                    }
                }
            }
            $chara_goods->and_where_close();

            $result = $chara_goods->find_all();
            $arr = array();
            foreach ($result as $item) {
                $arr[$item['target_id']][$item['character_id']] = $item['value_id'];
            }

            $goodsIds = array();
            foreach ($arr as $id => $item) {
                if (count($item) == count($get['feature'])) {
                    $goodsIds[] = $id;
                }
            }

            if ($get['goods']) {
                $goodsIds = array_unique(array_merge($goodsIds, $get['goods']));
            }


            if (count($goodsIds)) {
                $orm->where($orm->table_name() . '.goods_id', 'in', $goodsIds);
            } else {
                $orm->where($orm->table_name() . '.goods_id', '=', 0);
            }
        } else {
            if ($get['goods']) {
                $orm->where($orm->table_name() . '.goods_id', 'in', $get['goods']);
            }
        }

        return $orm;
    }

    /**
     * @return string
     */
    public function link()
    {
        return link_to('shop_goods', array('url' => $this->getOwner()->url, 'id' => $this->getOwner()->pk()));
    }

    /** @return int|float */
    public function getMinimum()
    {
        $value = $this->getOwner()->minimum;
        $decimals = 3;
        if (($value - floor($value)) == 0) {
            $decimals = 0;
        }

        $number = number_format($value, $decimals, ',', '');
        if (strpos($number, ',') !== false) {
            $number = rtrim(rtrim($number, '0'), '.');
        }
        return $number;
    }

    public function getMaximum()
    {
        $value = $this->getOwner()->maximum;

        $decimals = 3;
        if (($value - floor($value)) == 0) {
            $decimals = 0;
        }

        $number = number_format($value, $decimals, ',', '');
        if (strpos($number, ',') !== false) {
            $number = rtrim(rtrim($number, '0'), '.');
        }
        return $number;
    }

    public function getStep()
    {
        $value = $this->getOwner()->step;

        $decimals = 3;
        if (($value - floor($value)) == 0) {
            $decimals = 0;
        }

        $number = number_format($value, $decimals, ',', '');
        if (strpos($number, ',')) {
            $number = rtrim(rtrim($number, '0'), ',');
        }

        return $number;
    }

    protected static $units = null;

    /**
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public function getUnit($name = 'abbr')
    {
        if (!self::$units) {
            $units = Unit::model()->find_all();
            self::$units = Arrays::resultAsArrayKey($units, 'unit_id');
        }
        return self::$units[$this->getOwner()->unit_id]->{$name};
    }

    /**
     * @return int
     */
    public function getPerDiscount()
    {
        if ($this->getOwner()->value_old == 0) {
            return 0;
        }

        $per = (($this->getOwner()->value_old - $this->getOwner()->value) / $this->getOwner()->value_old) * 100;
        return (int)$per;
    }

    /**
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public function getCategoriesStr($glue = '/')
    {
        return Shop::getCategoriesListStr($glue, $this->getOwner()->cid, $this->getOwner()->ctype);
    }


    /**
     * @var array
     */
    private static $_vendor_list = null;

    /**
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public function getVendor()
    {

        if (!self::$_vendor_list) {
            $orm = \Shop\Commodity\Entity\Vendor::model()
                ->cached()
                ->select('vendor_id', 'name')
                ->find_all();
            self::$_vendor_list = \Delorius\Utils\Arrays::resultAsArrayKey($orm, 'vendor_id');
        }

        if (isset(self::$_vendor_list[$this->getOwner()->vendor_id])) {
            return self::$_vendor_list[$this->getOwner()->vendor_id]['name'];
        }

        return '';

    }
} 