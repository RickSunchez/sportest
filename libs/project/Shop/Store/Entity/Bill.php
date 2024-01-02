<?php
namespace Shop\Store\Entity;

use Delorius\Core\Common;
use Delorius\Core\DateTime;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class Bill
 * @package Shop\Store\Entity
 *
 * @property int $bill_id Primary key
 * @property int $user_id  Current user id
 * @property int $payment_id Payment method
 * @property float $value Total price
 * @property int $status Status order
 * @property int $date_cr Date create
 * @property int $date_paid Date paid order
 */
class Bill extends ORM
{
    /**
     * @return $this
     */
    public function sort(){
        $this->order_created('desc')->order_pk();
        return $this;
    }

    public function as_array(){

        $arr = parent::as_array();
        $arr['price'] = $this->getPrice(null,false);
        $arr['status_name'] = $this->getStatusName();
        $arr['date_cr'] = DateTime::dateFormat($arr['date_cr'],true);
        $arr['date_paid'] = $arr['date_paid'] > 0 ? DateTime::dateFormat($arr['date_paid'],true) : null;
        return $arr;
    }

    const STATUS_NEW = 1;
    const STATUS_PAID = 2;


    /** @return array */
    public static function getStatuses()
    {
        return array(
            self::STATUS_NEW => 'Ожидает оплаты',
            self::STATUS_PAID => 'Исполненный',
        );
    }

    /**
     * @return string
     */
    public function getStatusName(){
        $statuses = Bill::getStatuses();
        return $statuses[$this->status];
    }

    public function behaviors(){
        return array(
            'userBehavior' => 'CMS\Users\Behaviors\UserBehavior',
            'priceBehavior' => 'Shop\Store\Behaviors\PriceBehavior',
            'helpBillBehavior' => 'Shop\Store\Behaviors\HelpBillBehavior',
            'accountBehavior' => array(
                'class'=>'Shop\Payment\Behaviors\AccountBehavior',
                'desc'=>'Пополнения счета профиля №{user_id}',
                'field_price'=>'value',
                'callback'=>'Shop\Payment\Callback\BillCallback',

            )
        );
    }

    protected $_primary_key = 'bill_id';
    protected $_table_name = 'shop_bill';
    protected $_table_columns_set = array('value','status');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'value' =>array(
                array(array($this,'float'))
            ),
            'status' =>array(
                array(array($this,'setStatus'))
            ),
        );
    }

    protected function setStatus($value){
        if($value == self::STATUS_PAID){
            $this->date_paid = time();
        }
        return $value;
    }

    protected function float($value){
        return str_replace(',','.',$value);
    }

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'bill_id' => array(
            'column_name' => 'bill_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'payment_id' => array(
            'column_name' => 'payment_id',
            'data_type' => 'int unsigned',
            'display' => 11,
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
            'display' => self::STATUS_NEW,
            'column_default' =>1
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
        'date_paid' => array(
            'column_name' => 'date_paid',
            'data_type' => 'int',
            'display' => 11,
            'column_default' =>0
        ),
    );
}