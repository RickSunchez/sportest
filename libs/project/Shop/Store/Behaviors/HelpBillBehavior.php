<?php
namespace Shop\Store\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\Environment;

class HelpBillBehavior extends ORMBehavior
{
    /**
     * @var array
     */
    protected static $config = array();

    /** @var \Shop\Payment\Handler\HandlerPayment */
    private $_payment;

    /**
     * @return \Shop\Payment\Handler\HandlerPayment
     */
    protected function getPayment()
    {
        if (!count(self::$config)) {
            self::$config['bill'] = Environment::getContext()->getParameters('shop.store.bill');
            self::$config['bill_default'] = Environment::getContext()->getParameters('shop.store.bill_default');
        }
        
        if (!$this->_payment) {
            $paymentId = $this->getOwner()->payment_id;
            if (!isset(self::$config['bill'][$paymentId])) {
                $paymentId = self::$config['bill_default'];
            }
            $class = self::$config['bill'][$paymentId]['type'];
            $this->_payment = new $class($this->getOwner()->getAccount());
        }
        return $this->_payment;
    }

    public function render()
    {
        return $this->getPayment()->render();
    }

}