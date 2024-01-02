<?php
namespace Shop\Payment\Entity;

use Delorius\Core\ORM;

/**
 * Class Account
 * @package Shop\Payment\Entity
 *
 * @property int $account_id Primary key
 * @property int $status Status account
 * @property int $target_id Owner ID
 * @property string $target_type Owner type
 * @property float $value Price
 * @property string $desc Description
 * @property string callback Class name callback
 * @property int $date_paid Date status = success
 * @property int $date_cr Date create
 * @property int $date_edit Date edit
 * @property string $ip Ip address remove
 */
class Account extends ORM
{
    const STATUS_NEW = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAIL = 3;
    const STATUS_DELETE = 4;

    /** @return array */
    public static function getStatuses()
    {
        return array(
            self::STATUS_NEW => _t('Shop:Payment','New'),
            self::STATUS_SUCCESS => _t('Shop:Payment','Success'),
            self::STATUS_FAIL => _t('Shop:Payment','Fail'),
            self::STATUS_DELETE => _t('Shop:Payment','Delete')
        );
    }

    /**
     * @return string
     */
    public function getStatusName(){
        $statuses = Account::getStatuses();
        return $statuses[$this->status];
    }


    public function behaviors(){
        return array(
            'editAccountBehavior' => 'Shop\Payment\Behaviors\EditAccountBehavior',
            'priceBehavior' => 'Shop\Store\Behaviors\PriceBehavior',
        );
    }

    protected $_primary_key = 'account_id';
    protected $_table_name = 'shop_account';
    protected $_config_key = 'config_account';
    protected $_table_columns_set = array();

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'value' =>array(
                array(array($this,'float'))
            )
        );
    }

    protected function float($value){
        return str_replace(',','.',$value);
    }

    protected function setCallback($value){
        if(!class_exists($value)){
            return false;
        }
        return true;
    }

    protected function rules()
    {
        return array(
            'callback'=>array(
                array(array($this,'setCallback'),array(':value'),'Класс callback не существует'),
            ),
        );
    }

    protected $_table_columns = array(
        'account_id' => array(
            'column_name' => 'account_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' =>1
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
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'desc' => array(
            'column_name' => 'desc',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'callback' => array(
            'column_name' => 'callback',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_paid' => array(
            'column_name' => 'date_paid',
            'data_type' => 'int',
            'display' => 11,
            'column_default' =>0
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
            'column_default' =>0
        ),
        'config_account' => array(
            'column_name' => 'config_account',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
    );
}