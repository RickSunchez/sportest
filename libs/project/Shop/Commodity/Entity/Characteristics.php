<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class Characteristics extends ORM
{

    const FILTER_CHECKBOX = 1;
    const FILTER_RADIO = 2;
    const FILTER_SLIDER = 3;
    const FILTER_SELECT = 4;
    const FILTER_LINK = 5;
    const FILTER_COLOR = 6;
    const FILTER_OTHER = 7;

    /** @return array */
    public static function getFilters()
    {
        return array(
            self::FILTER_CHECKBOX => 'CHECKBOX',
            self::FILTER_RADIO => 'RADIO',
            self::FILTER_SLIDER => 'SLIDER',
            self::FILTER_SELECT => 'SELECT',
            self::FILTER_LINK => 'LINK',
            self::FILTER_COLOR => 'COLOR',
            self::FILTER_OTHER => 'Другой',
        );
    }

    /**
     * @return string
     */
    public function getFilterName()
    {
        $statuses = self::getFilters();
        return $statuses[$this->filter];
    }

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = NULL)
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }


    protected function behaviors()
    {
        return array(
            'characteristicsBehavior' => 'Shop\Commodity\Behaviors\CharacteristicsBehavior',
        );
    }

    protected $_primary_key = 'character_id';
    protected $_table_name = 'shop_characteristics';

    protected $_table_columns_set = array('name','info');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название характеристики'),
            ),
            'info' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 1000), 'Описание характеристики не должно превышать 1000 символов'),
            ),
        );
    }

    protected $_table_columns = array(
        'character_id' => array(
            'column_name' => 'character_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
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
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'filter' => array(
            'column_name' => 'filter',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => self::FILTER_CHECKBOX
        ),
        'filter_other' => array(
            'column_name' => 'filter_other',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
    );
}