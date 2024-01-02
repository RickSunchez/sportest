<?php
namespace Shop\Payment\Callback;

use CMS\Core\Component\Register;
use CMS\Core\Helper\Helpers;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Shop\Store\Entity\Order;

class OrdersCallback extends PaymentCallback
{

    /**
     * @return void
     * @throws \Delorius\Exception\Error
     */
    public function paid()
    {
        $order = $this->getOrder();
        $order->status = ORDER_STATUS_PAID;
        $register = Environment::getContext()->getService('register');
        $order->onAfterSave[] = function ($orm) use ($register) {
            $register->add(
                Register::TYPE_INFO,
                Register::SPACE_ADMIN,
                'Принета оплата по закзау ID = [order_id]',
                $orm
            );
        };
        $order->save();
        $this->accountPaid();
    }

    /**
     * @return mixed
     */
    public function success()
    {
        $order = $this->getOrder();
        return _sf('<p>Спасибо, Ваша оплата по заказу {1} на сумму {0} принята!</p><p>Мы свяжемся с Вами в ближайшее время!</p>', $order->getPrice(), $order->getNumber());
    }

    /**
     * @return mixed
     */
    public function fail()
    {
        $order = $this->getOrder();
        if ($order->loaded()) {
            $this->accountFail();
        }
        return _sf('<p>К сожалению, оплата не прошла. Попробуйте повторить попытку или обратится к администратору сайта по заказ #{1}</p>', $order->getPrice(), $order->getNumber());
    }

    /**
     * @return Order
     * @throws \Delorius\Exception\Error
     */
    protected function getOrder()
    {
        $order = new Order($this->account->target_id);
        if ($this->account->target_type != Helpers::getTableId($order)) {
            throw new Error(_sf('Account #{0} does not belong to this {1}',
                $this->account->pk(),
                get_class($order)
            ));
        }
        return $order;
    }
}