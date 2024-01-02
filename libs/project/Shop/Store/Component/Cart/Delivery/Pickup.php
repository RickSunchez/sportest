<?php
namespace Shop\Store\Component\Cart\Delivery;

class Pickup extends DeliveryBase
{

    /**
     * @return string
     */
    public function getDesc()
    {
        $point = $this->getPoint();
        return $this->desc . $point['address'] ? ' из ' . $point['address'] : '';
    }

    /**
     * @return float
     */
    public function getValue()
    {
        $point = $this->getPoint();
        return $this->value + $point['value'];
    }

    private $_point = null;

    private function getPoint()
    {
        if (!$this->_point) {
            $this->_point = $this->cart->getPoint();
        }
        return $this->_point;
    }

}