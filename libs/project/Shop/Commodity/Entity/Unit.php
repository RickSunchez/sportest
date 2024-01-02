<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

/**
 * Class Unit
 * @package Shop\Commodity\Entity
 *
 * @property int $unit_id Primary key
 * @property string $name Name (max = 200)
 * @property string $abbr Abbr (max = 50)
 * @property int $pos Position
 */
class Unit extends ORM
{
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_by('name','ASC')->order_pk();
        return $this;
    }

    protected function behaviors()
    {
        return array(
            'unitBehavior' => 'Shop\Commodity\Behaviors\UnitBehavior',
        );
    }

    protected $_primary_key = 'unit_id';
    protected $_table_name = 'shop_unit';
    protected $_table_columns_set = array('name','abbr');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
            'abbr' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 46), 'Укажите аббревиатуру'),
            ),
        );
    }

    protected $_table_columns = array(
        'unit_id' => array(
            'column_name' => 'unit_id',
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
        'abbr' => array(
            'column_name' => 'abbr',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
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