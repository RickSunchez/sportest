<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class CollectionProductItem extends ORM
{

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }


    protected $_table_name = 'shop_product_collection_item';
    protected $_table_columns_set = array('name');

    protected function behaviors()
    {
        return array(
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'product_item',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250,
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
        'product_id' => array(
            'column_name' => 'product_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'coll_id' => array(
            'column_name' => 'coll_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
            'collation_name' => 'utf8_general_ci',
        )
    );
}