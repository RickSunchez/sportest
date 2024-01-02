<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

/**
 * Class CollectionGoods
 * @package Shop\Commodity\Entity
 *
 * @property int $id Primary key
 * @property int $coll_id Vendor primary key
 * @property int $package_id Package primary key
 * @property int $goods_id Goods primary key
 * @property int $pos Position
 */
class CollectionGoods extends ORM
{
    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected $_table_name = 'shop_collection_goods';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'coll_id' => array(
            'column_name' => 'coll_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'package_id' => array(
            'column_name' => 'package_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}