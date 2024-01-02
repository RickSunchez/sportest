<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class Provider extends ORM
{
    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }


    protected $_table_name = 'shop_provider';
    protected $_table_columns_set = array('name');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 1000), 'Описание не должно превышать больше 1000 символов'),
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
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
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