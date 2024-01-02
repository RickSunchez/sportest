<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class Attribute extends ORM
{
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected $_primary_key = 'attr_id';
    protected $_table_name = 'shop_attribute';

    protected $_table_columns_set = array('name');

    protected $_table_columns = array(
        'attr_id' => array(
            'column_name' => 'attr_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}