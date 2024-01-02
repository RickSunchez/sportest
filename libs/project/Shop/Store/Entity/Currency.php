<?php
namespace Shop\Store\Entity;

use Delorius\Core\ORM;

class Currency extends ORM
{

    const TYPE_ALL = 0;
    const TYPE_DECIMAL = 1;
    const TYPE_NO_DECIMAL = 2;

    /** @return array activity for man */
    public static function getTypeDecimal()
    {
        return array(
            self::TYPE_ALL => _t('Shop:Store','Must fractional parts'),
            self::TYPE_DECIMAL => _t('Shop:Store','Must fractional part, if there is'),
            self::TYPE_NO_DECIMAL => _t('Shop:Store','Do not print the fractional part'),
        );
    }

    protected $_primary_key = 'currency_id';
    protected $_table_name = 'shop_currency';

    protected $_table_columns_set = array('name', 'code');

    protected function filters()
    {
        return array(
            'value' =>array(
                array(array($this,'float')),
                array('trim')
            ),
            'nominal' =>array(
                array(array($this,'float')),
                array('trim')
            ),
            'name' =>array(
                array('trim')
            ),
            'code' =>array(
                array('trim')
            ),
        );
    }

    protected function float($value){
        return str_replace(',','.',$value);
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 46), 'Укажите название валюты'),
            ),
            'code' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 10), 'Укажите название кода валюты'),
            ),
        );
    }

    protected $_table_columns = array(
        'currency_id' => array(
            'column_name' => 'currency_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
        ),
        'symbol_left' => array(
            'column_name' => 'symbol_left',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'symbol_right' => array(
            'column_name' => 'symbol_right',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'decimal_place' => array(
            'column_name' => 'decimal_place',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' =>2
        ),
        'decimal_type' => array(
            'column_name' => 'decimal_type',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 4,
            'column_default' => 0
        ),
        'nominal' => array(
            'column_name' => 'nominal',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 4,
            'column_default' => 0
        ),
    );
}