<?php
namespace Shop\Store\Entity;

use Delorius\Core\ORM;
use Shop\Store\Component\Cart\CartType;
use Shop\Store\Component\Cart\Helpers;

/**
 * Class Item
 * @package Shop\Store\Entity
 *
 */
class Item extends ORM
{

    /**
     * @param NULL $direction
     * @return $this
     */
    public function sort($direction = NULL)
    {
        $this->order_created($direction);
        return $this;
    }

    /**
     * @return array
     */
    public function as_array()
    {
        $arr = parent::as_array();
        $arr['price'] = $this->getPrice($this->value);
        $arr['price_raw'] = $this->getPrice($this->value, false);
        $arr['total_value'] = $this->value * $this->amount;
        $arr['total_price'] = $this->getPrice($arr['total_value']);
        $arr['total_price_raw'] = $this->getPrice($arr['total_value'], false);
        $arr['quantity'] = $this->getQuantity();
        return $arr;
    }

    /**
     * @return string
     */
    public function getQuantity()
    {
        return Helpers::digitFormat($this->amount);
    }

    protected $_table_name = 'shop_order_item';
    protected $_config_key = 'config';
    protected $_table_columns_set = array('value');

    public function behaviors()
    {
        return array(
            'priceBehavior' => 'Shop\Store\Behaviors\PriceBehavior',
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'value' => array(
                array(array($this, 'float'))
            ),
        );
    }

    protected function float($value)
    {
        return Helpers::float($value);
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'item_id' => array(
            'column_name' => 'item_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'item_type' => array(
            'column_name' => 'item_type',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => CartType::TYPE_GOODS
        ),
        'order_id' => array(
            'column_name' => 'order_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'article' => array(
            'column_name' => 'article',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'amount' => array(
            'column_name' => 'amount',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 3,
            'column_default' => 0
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
        ),
        'config' => array(
            'column_name' => 'config',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
    );
}