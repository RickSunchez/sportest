<?php
namespace Boat\Store\Component\Cart\Delivery;

use Shop\Store\Component\Cart\Delivery\DeliveryBase;

class DeliveryToCity extends DeliveryBase
{
    /**
     * @return float
     */
    public function getValue()
    {
        return $this->is_free() ? 0 : $this->value;
    }

    protected function is_free()
    {
        if ($this->cart->getValueGoods() >= PRICE_DELIVERY_FREE) {
            return true;
        }
        return false;
    }

    public function getDesc()
    {
        return $this->is_free() ? 'Доставка свыше 10000 р. по Екатеринбургу бесплатно' : $this->desc;
    }
}