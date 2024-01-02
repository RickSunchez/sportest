<?php
namespace Shop\Store\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

class Cashflow extends ORM
{
    /**
     * @return $this
     */
    public function sort(){
        $this->order_pk('desc');
        return $this;
    }

    public function as_array(){
        $arr = parent::as_array();
        $arr['type_name'] = $this->type == self::PLUS?'Приход (+)':'Расход (-)';
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']],true);
        return $arr;
    }

    const PLUS = 1;
    const MINUS = 2;

    /**
     * @return array
     */
    public static function getTransactionTypes()
    {
        return array(
            self::PLUS => '+',
            self::MINUS => '-',
        );
    }

    /**
     * @return string
     */
    public function getNameTransactionTypes()
    {
        $status = self::getTransactionTypes();
        return $status[$this->type];
    }

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_primary_key = 'cash_id';
    protected $_table_name = 'shop_cash_flow';
    protected $_table_columns_set = array('value', 'type','reason','balance_id','user_id');

    protected $_table_columns = array(
        'cash_id' => array(
            'column_name' => 'cash_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'type' => array(  // 1 - plus ,  2 - minus
            'column_name' => 'type',
            'data_type' => 'tinyint',
            'display' => 1
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11
        ),
        'balance_id' => array(
            'column_name' => 'balance_id',
            'data_type' => 'int unsigned',
            'display' => 11
        ),
        'reason' => array(
            'column_name' => 'reason',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
    );
}