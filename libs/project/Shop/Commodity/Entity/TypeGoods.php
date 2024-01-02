<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class TypeGoods extends ORM
{
    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = NULL)
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_name = 'shop_type_goods';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        )
    );
}