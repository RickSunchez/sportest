<?php

namespace Shop\Commodity\Entity\Options;

use Delorius\Core\ORM;

class Item extends ORM
{

    const TYPE_SELECT = 1;
    const TYPE_RADIO = 2;
    const TYPE_FLAG = 3;
    const TYPE_VARCHAR = 4;
    const TYPE_TEXT = 5;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_SELECT => 'Список вариантов',
            self::TYPE_RADIO => 'Радиогруппа',
            self::TYPE_FLAG => 'Флажок',
            self::TYPE_VARCHAR => 'Текст',
            self::TYPE_TEXT => 'Текстовая область',
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
     * @return bool
     */
    public function isInventory()
    {
        return (bool)$this->inventory && (
                $this->type == self::TYPE_FLAG ||
                $this->type == self::TYPE_SELECT ||
                $this->type == self::TYPE_RADIO
            );
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isText()
    {
        return (bool)(
            $this->type == self::TYPE_TEXT ||
            $this->type == self::TYPE_VARCHAR
        );
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
    public function required()
    {
        $this->where('required', '=', 1);
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

    /**
     * @return array
     */
    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getNameType();
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'editOptionItemBehavior' => 'Shop\Commodity\Behaviors\EditOptionItemBehavior'
        );
    }

    protected $_table_name = 'shop_goods_options';

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
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'tinyint unsigned',
            'display' => 2,
            'column_default' => self::TYPE_SELECT
        ),
        'pos' => array(
            'column_name' => 'pos',
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
        'inventory' => array(
            'column_name' => 'inventory',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        )
    );
}