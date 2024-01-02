<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

class Accompany extends ORM
{

    const TYPE_OTHER = 0;
    const TYPE_ACCESSORIES = 1;
    const TYPE_ADDITIONS = 2;
    const TYPE_SUBSTITUTE = 3;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_OTHER => 'Прочие',
            self::TYPE_ACCESSORIES => 'Аксессуары',
            self::TYPE_ADDITIONS => 'Дополнения',
            self::TYPE_SUBSTITUTE => 'Замена',
        );
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getTypes();
        return $types[$this->type_id];
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this
            ->order_by('type_id')
            ->order_by('pos', $direction)
            ->order_pk();
        return $this;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getNameType();
        return $arr;
    }

    protected $_table_name = 'shop_accompany_goods';
    protected $_table_columns_set = array('target_id', 'current_id');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
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
        'current_id' => array(
            'column_name' => 'current_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}