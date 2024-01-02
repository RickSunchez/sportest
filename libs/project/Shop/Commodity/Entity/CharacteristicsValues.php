<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class CharacteristicsValues extends ORM
{
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected $_primary_key = 'value_id';
    protected $_table_name = 'shop_characteristics_values';

    protected $_table_columns_set = array('name');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите значение'),
            ),
            'character_id' => array(
                array(array($this, 'checkInitId'), array(':value'), 'Выберите характиристику'),
            ),
            'info' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 1000), 'Описание значения не должно превышать 1000 символов'),
            ),
            'code' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 200), 'Код значения  не должно превышать 200 символов'),
            ),
        );
    }

    protected function checkInitId($value)
    {
        if ($value > 0) {
            return true;
        }
        return false;
    }

    protected $_table_columns = array(
        'value_id' => array(
            'column_name' => 'value_id',
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
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'info' => array(
            'column_name' => 'info',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'unit_id' => array(
            'column_name' => 'unit_id',
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