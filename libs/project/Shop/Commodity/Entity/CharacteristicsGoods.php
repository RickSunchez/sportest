<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class CharacteristicsGoods extends ORM
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

    protected $_primary_key = 'id';
    protected $_table_name = 'shop_characteristics_goods';

    protected $_table_columns_set = array('character_id','value_id','target_id');

    protected function rules()
    {
        return array(
            'character_id'=>array(
                array(array($this,'checkInitId'),array(':value'),'Выберите характиристику'),
            ),
            'value_id'=>array(
                array(array($this,'checkInitId'),array(':value'),'Выберите значение характеристики'),
            ),
            'target_id'=>array(
                array(array($this,'checkInitId'),array(':value'),'Выберите товар'),
            ),
        );
    }

    protected function checkInitId($value){
        if($value>0){
            return true;
        }
        return false;
    }

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'character_id' => array(
            'column_name' => 'character_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'value_id' => array(
            'column_name' => 'value_id',
            'data_type' => 'int unsigned',
            'display' => 11,
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
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'main' => array(
            'column_name' => 'main',
            'data_type' => 'tinyint unsigned',
            'display' => 11,
            'column_default' => 0
        ),

    );
}