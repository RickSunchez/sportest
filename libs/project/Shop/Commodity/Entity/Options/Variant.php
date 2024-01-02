<?php
namespace Shop\Commodity\Entity\Options;

use Delorius\Core\ORM;

class Variant extends ORM
{

    const TYPE_SUM = 1;
    const TYPE_PER = 2;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_SUM => 'n',
            self::TYPE_PER => '%',
        );
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getTypes();
        return $types[$this->type];
    }

    /**
     * @return $this
     */
    public function inventory()
    {
        $this->where('inventory', '=', 1);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    /**
     * @param int|array $id
     * @return $this
     */
    public function byGoodsId($id)
    {
        if (is_array($id)) {
            $this->where('goods_id', 'IN', $id);
        } else {
            $this->where('goods_id', '=', $id);
        }
        return $this;
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }

    protected function behaviors()
    {
        return array(
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'variant',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250,
            ),
        );
    }

    protected $_table_name = 'shop_goods_options_variants';

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), 'Укажите название'),
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
        'external_id' => array(
            'column_name' => 'external_id',
            'data_type' => 'varchar',
            'character_maximum_length' => 36,
            'collation_name' => 'utf8_general_ci',
        ),
        'external_change' => array(
            'column_name' => 'external_change',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'option_id' => array(
            'column_name' => 'option_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'required' => array(
            'column_name' => 'required',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'comment' => array(
            'column_name' => 'comment',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'modifier' => array(
            'column_name' => 'modifier',
            'data_type' => 'decimal',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => self::TYPE_SUM
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'inventory' => array(
            'column_name' => 'inventory',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
    );
}