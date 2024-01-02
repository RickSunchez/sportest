<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\CollectionGoods;
use Shop\Commodity\Entity\CollectionPackage;
use Shop\Commodity\Entity\Goods;

class CollectionBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(CollectionPackage::model()->table_name())
            ->where('coll_id', '=', $orm->pk())
            ->execute(CollectionPackage::model()->db_config());

        DB::delete(CollectionGoods::model()->table_name())
            ->where('coll_id', '=', $orm->pk())
            ->execute(CollectionGoods::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getPackages()
    {
        $ormPackage = CollectionPackage::model()
            ->where('coll_id', '=', $this->getOwner()->pk())
            ->sort();

        return $ormPackage->find_all();
    }

    /**
     * @param $value
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public function addPackage($value)
    {
        try {
            $ormPackage = new CollectionPackage($value[CollectionPackage::model()->primary_key()]);
            if ($value['delete'] == 1) {
                if ($ormPackage->loaded()) {
                    $ormPackage->delete();
                }
                return true;
            }
            $ormPackage->values($value);
            $ormPackage->coll_id = $this->getOwner()->pk();
            $ormPackage->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getGoods()
    {
        $collectionGoods = new CollectionGoods();
        $goods = new Goods();
        $result = $goods->join($collectionGoods->table_name(), 'INNER')
            ->on($collectionGoods->table_name() . '.goods_id', '=', $goods->table_name() . '.goods_id')
            ->where($collectionGoods->table_name() . '.coll_id', '=', $this->getOwner()->pk())
            ->order_by($collectionGoods->table_name() . '.pos', 'desc')
            ->order_by($collectionGoods->table_name() . '.id')
            ->order_by($goods->table_name() . '.pos', 'desc')
            ->order_by($goods->table_name() . '.goods_id')
            ->find_all();

        return $result;
    }

    /**
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getPackageGoods()
    {
        $package = new CollectionPackage();
        $packageGoods = new  CollectionGoods();
        $result = $packageGoods->join($package->table_name(), 'LEFT')
            ->on($package->table_name() . '.id', '=', $packageGoods->table_name() . '.package_id')
            ->where($packageGoods->table_name() . '.coll_id', '=', $this->getOwner()->pk())
            ->order_by($package->table_name() . '.pos', 'desc')
            ->order_by($package->table_name() . '.id')
            ->order_by($packageGoods->table_name() . '.pos', 'desc')
            ->order_by($packageGoods->table_name() . '.id')
            ->find_all();

        return $result;
    }

    /**
     * @param $value
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public function addGoods($value)
    {
        try {
            $ormGoods = new CollectionGoods($value[CollectionGoods::model()->primary_key()]);
            if ($value['delete'] == 1) {
                if ($ormGoods->loaded()) {
                    $ormGoods->delete();
                }
                return true;
            }
            $ormGoods->values($value);
            $ormGoods->coll_id = $this->getOwner()->pk();
            $ormGoods->save(true);
            $this->updateMinPrice();
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * Changes in the price of goods in a minimal medium
     */
    public function updateMinPrice()
    {
        $goods_table = new Goods();
        $collection = new CollectionGoods();
        $execute = DB::select(array(DB::expr('MIN(value_system)'), 'min'))
            ->from($goods_table->table_name())
            ->join($collection->table_name(), 'INNER')
            ->on($goods_table->table_name() . '.goods_id', '=', $collection->table_name() . '.goods_id')
            ->where($goods_table->table_name() . '.ctype', '=', $this->getOwner()->ctype)
            ->where($collection->table_name() . '.coll_id', '=', $this->getOwner()->pk())
            ->where($goods_table->table_name() . '.status', '=', 1);
        $result = $execute->execute($goods_table->db_config());
        $min = $result->get('min');
        $this->getOwner()->value_min = $min;
        $this->getOwner()->save();
    }

    /**
     * @return string
     */
    public function link()
    {
        return link_to('shop_collection', array('url' => $this->getOwner()->url, 'id' => $this->getOwner()->pk()));
    }


    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * Возращает стоимость в указаной валюте
     * @param bool $format
     * @return string
     */
    public function getPrice($format = true)
    {
        if ($this->getOwner()->value_min <= 0) {
            return 0;
        }
        return $this->currency->format($this->getOwner()->value_min, SYSTEM_CURRENCY, null, $format);
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->currency->convert($this->getOwner()->value_min, SYSTEM_CURRENCY, SYSTEM_CURRENCY);
    }

} 