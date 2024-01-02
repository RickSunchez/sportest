<?php
namespace Shop\Store\Entity;

use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Balance extends ORM
{

    public static function getByUserId($userId)
    {
        $balance = Balance::model()->where('user_id', '=', $userId)->find();

        if ($balance->loaded())
        {
            if($balance->hash != $balance->hashBalance()){
                Environment::getContext()->getService('logger')->error(_sf('Ошибка подсчета баланса #balance_{0} ',$balance->pk()),'balance');
            }
            return $balance;
        }

        $balance->user_id = $userId;
        $balance->save();
        return $balance;
    }

    protected function behaviors()
    {
        return array(
            'cashflowBehavior' => 'Shop\Store\Behaviors\CashflowBehavior',
            'priceBehavior' => 'Shop\Store\Behaviors\PriceBehavior',
        );
    }

    public function as_array(){
        $arr = parent::as_array();
        unset($arr['hash']);
        $arr['is_balance'] = $this->hashBalance();
        return $arr;
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
            'hash' => array(
                array(array($this, 'hashBalance'))
            ),
        );
    }

    public function hashBalance($value = null)
    {
        return Strings::codePassword((int)$this->value . $this->user_id);
    }

    protected function float($value)
    {
        return str_replace(',', '.', $value);
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_primary_key = 'balance_id';
    protected $_table_name = 'shop_balance';
    protected $_table_columns_set = array('value','user_id','hash');

    protected $_table_columns = array(
        'balance_id' => array(
            'column_name' => 'balance_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
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
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
            'collation_name' => 'utf8_general_ci',
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
    );
}