<?php

namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;
use Shop\Catalog\Entity\Category;

class CollectionProduct extends ORM
{

    /**
     * @return $this
     */
    public function active()
    {
        $this->where($this->table_name() . '.status', '=', 1);
        return $this;
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }


    protected $_table_name = 'shop_product_collection';
    protected $_table_columns_set = array('name');

    protected function behaviors()
    {
        return array(
            'collectionProductBehavior' => 'Shop\Commodity\Behaviors\CollectionProductBehavior',
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название коллекции'),
            ),
        );
    }

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
        'label' => array(
            'column_name' => 'label',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => Category::TYPE_GOODS
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
    );
}