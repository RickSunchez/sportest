<?php
namespace Shop\Store\Component\Cart\Delivery;


class DeliveryToCity extends DeliveryBase
{
    /**
     * @return float
     */
    public function getValue()
    {
        return $this->cart->getCity()->getValue();
    }
}