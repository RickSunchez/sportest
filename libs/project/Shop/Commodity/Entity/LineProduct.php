<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class LineProduct extends ORM
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

    /**
     * @return $this
     */
    public function active(){
        $this->where('status','=',1);
        return $this;
    }

    protected function behaviors()
    {
        return array(
            'lineProductItemBehavior' => 'Shop\Commodity\Behaviors\LineProductItemBehavior',
        );
    }

    protected $_table_name = 'shop_line_products';
    protected $_table_columns_set = array('name');
    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'btn' => array(
            'column_name' => 'btn',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
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