<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class CharacteristicsGroup extends ORM
{
    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = NULL)
    {
        $this->order_by('pos', $direction);
        return $this;
    }


    protected function behaviors()
    {
        return array(
            'characteristicsGroupBehavior' => 'Shop\Commodity\Behaviors\CharacteristicsGroupBehavior',
        );
    }

    protected $_primary_key = 'group_id';
    protected $_table_name = 'shop_characteristics_group';

    protected $_table_columns_set = array('name');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название группы'),
            ),
        );
    }

    protected $_table_columns = array(
        'group_id' => array(
            'column_name' => 'group_id',
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
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}