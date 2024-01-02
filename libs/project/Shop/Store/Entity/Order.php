<?php
namespace Shop\Store\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Shop\Store\Helper\OrderHelper;

/**
 * Class Order
 * @package Shop\Store\Entity
 *
 * @property int $order_id Primary key
 * @property int $user_id  Current user id
 * @property string $email Email
 * @property float $value Total price
 * @property int $status Status order
 * @property int $paid Status order of paid
 * @property int $salt
 * @property int $date_cr Date create
 * @property int $date_edit Date edit
 * @property int $date_paid Date paid order
 * @property string $hash Hash current
 * @property string $note About
 * @property string $code Code order
 */
class Order extends ORM
{
    // @note чтобы не ломать логику, добавил поля в объект заказа
    private $checkoutError = false;
    private $checkoutErrorType;
    private $errorData;

    public function checkoutError($errorType, $errorData)
    {
        $this->checkoutError = true;
        $this->checkoutErrorType = $errorType;
        $this->errorData = $errorData;
    }

    public function getErrorData()
    {
        return array(
            'type' => $this->checkoutErrorType,
            'data' => $this->errorData
        );
    }

    public function onCheckoutError()
    {
        return $this->checkoutError;
    }

    /**
     * @param $number
     * @return $this
     */
    public function whereNumber($number)
    {
        $pk = (int)str_replace(SHOP_CODE_PREFIX, '', Strings::trim($number));
        $this->where('order_id', '=', $pk);
        return $this;
    }

    /**
     * Number code order
     * @return string
     */
    public function getNumber()
    {
        return SHOP_CODE_PREFIX . Strings::padLeft($this->pk(), 6, '0');
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_created('desc')->order_pk();
        return $this;
    }

    public function as_array()
    {

        $arr = parent::as_array();
        $arr['price'] = $this->getPrice();
        $arr['price_raw'] = $this->getPrice(null, false);
        $arr['status_name'] = $this->getStatusName();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        $arr['updated'] = $arr['date_edit'] > 0 ? DateTime::dateFormat($arr['date_edit'], true) : null;
        $arr['number'] = $this->getNumber();
        return $arr;
    }

    /**
     * @param $hash
     * @return bool
     */
    public static function hasHash($hash)
    {
        return Order::model()->where('hash', '=', $hash)->find()->loaded();
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = OrderHelper::getStatusId();
        return $statuses[$this->status];
    }

    public function behaviors()
    {
        return array(
            'userBehavior' => 'CMS\Users\Behaviors\UserBehavior',
            'optionsBehavior' => 'CMS\Core\Behaviors\OptionsBehavior',
            'linkBehavior' => array(
                'class' => 'CMS\Core\Behaviors\LinkBehavior',
                'router' => 'shop_order_show',
                'params' => array(
                    'order_code' => 'code',
                    'hash' => 'hash'
                ),
            ),
            'priceBehavior' => 'Shop\Store\Behaviors\PriceBehavior',
            'itemOrderBehavior' => 'Shop\Store\Behaviors\ItemOrderBehavior',
            'orderBehavior' => 'Shop\Store\Behaviors\OrderBehavior',
            'accountBehavior' => array(
                'class' => 'Shop\Payment\Behaviors\AccountBehavior',
                'desc' => 'Оплата заказа {number}',
                'field_price' => 'value',
                'callback' => 'Shop\Payment\Callback\OrdersCallback',
            )
        );
    }

    protected $_primary_key = 'order_id';
    protected $_table_name = 'shop_order';
    protected $_config_key = 'config';
    protected $_table_columns_set = array('value', 'code');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'value' => array(
                array(array($this, 'float'))
            ),
            'code' => array(
                array(array($this, 'code'))
            ),
            'status' => array(
                array(array($this, 'setStatus'))
            ),
        );
    }

    protected function setStatus($value)
    {
        if ($value == ORDER_STATUS_PAID) {
            $this->paid = 1;
            $this->date_paid = time();
        }
        return $value;
    }

    protected function float($value)
    {
        return str_replace(',', '.', $value);
    }

    protected function code($value = null)
    {
        $value = $this->generateHashCode($value);
        return $value;
    }

    protected function generateHashCode($code = null)
    {
        if ($this->code) {
            return $this->code;
        }
        $code = $code ? $code : Strings::random(9, '0-9a-zA-Z');
        $salt = Strings::random(6, '0-9a-zA-Z');
        $hash = md5($code . $salt);
        if (!Order::hasHash($hash)) {
            $this->hash = $hash;
            $this->salt = $salt;
            return $code;
        } else
            return $this->generateHashCode($code);

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
        'order_id' => array(
            'column_name' => 'order_id',
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
        'external_id' => array(
            'column_name' => 'external_id',
            'data_type' => 'varchar',
            'character_maximum_length' => 36,
            'collation_name' => 'utf8_general_ci',
        ),
        'email' => array(
            'column_name' => 'email',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'salt' => array(
            'column_name' => 'salt',
            'data_type' => 'varchar',
            'character_maximum_length' => 6,
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'paid' => array(
            'column_name' => 'paid',
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
        'date_paid' => array(
            'column_name' => 'date_paid',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
        ),
        'note' => array(
            'column_name' => 'note',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'config' => array(
            'column_name' => 'config',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
    );
}