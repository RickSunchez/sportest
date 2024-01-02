<?php
namespace Shop\Catalog\Entity;

use Delorius\Core\ORM;

class CategoryMulti extends ORM
{

    protected $_table_name = 'shop_category_multi';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'cid' => array(
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'product_id' => array(
            'column_name' => 'product_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )

    );
}