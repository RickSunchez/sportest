<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class LineProductItem extends ORM
{

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }


    protected $_table_name = 'shop_line_products_item';
    protected $_table_columns_set = array();
    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'product_id' => array(
            'column_name' => 'product_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'line_id' => array(
            'column_name' => 'line_id',
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