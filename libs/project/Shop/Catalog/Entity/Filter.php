<?php

namespace Shop\Catalog\Entity;

use Delorius\Core\ORM;

/**
 * Class Filter
 * @package Shop\Catalog\Filter
 *
 * @property int $filter_id Primary key
 * @property string $name Name (max 200)
 * @property int $pos Position
 * @property int $type_id Type
 * @property int $cid Category id
 * @property string $value Params (max 500)
 */
class Filter extends ORM
{

    const TYPE_GOODS = 1;
    const TYPE_FEATURE = 2;
    const TYPE_CATEGORY = 3;
    const TYPE_COLLECTION = 4;
    const TYPE_COLLECTION_MAIN = 5;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_GOODS => 'по параметрам товара',
            self::TYPE_FEATURE => 'по харатеристика товара',
            self::TYPE_CATEGORY => 'вывод подкатегорий',
            self::TYPE_COLLECTION => 'вывод подборок',
            self::TYPE_COLLECTION_MAIN => 'вывод главных подборок',
        );
    }

    public static function getGoodsParams()
    {
        return array(
            'price' => 'по цене',
            'vendor_id' => 'по производителю',
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
     * @param $typeId
     * @return $this
     */
    public function type($typeId)
    {
        $this->where('type_id', '=', $typeId);
        return $this;
    }

    /**
     * @param null $direction
     * @return Category
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected $_primary_key = 'filter_id';
    protected $_table_name = 'shop_category_filter';
    protected $_table_columns_set = array();

    protected $_table_columns = array(
        'filter_id' => array(
            'column_name' => 'filter_id',
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
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
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
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        )
    );
}