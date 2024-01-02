<?php
namespace Boat\Store\Payment\Callback;

use CMS\Core\Component\Register;
use CMS\Core\Helper\Helpers;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Shop\Payment\Callback\PaymentCallback;
use Shop\Store\Entity\Order;

class OrdersCallback extends \Shop\Payment\Callback\OrdersCallback
{

    /**
     * @return void
     * @throws \Delorius\Exception\Error
     */
    public function paid()
    {
        $order = $this->getOrder();
        $order->status = ORDER_STATUS_PAID;
        $order->exchange_status = EXCHANGE_NOT;
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
}