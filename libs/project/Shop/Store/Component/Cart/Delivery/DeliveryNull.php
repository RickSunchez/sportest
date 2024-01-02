<?php
namespace Shop\Store\Component\Cart\Delivery;

use Delorius\Core\Environment;
use Shop\Store\Component\Cart\IItems;

class DeliveryNull implements IItems
{

    /**
     * @return float
     */
    public function getValue()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return 0;
    }

    /**
     * @return bool
     */
    public function isActive(){
        return false;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPrice($format = true)
    {
        return Environment::getContext()
            ->getService('currency')
            ->format($this->getValue(),SYSTEM_CURRENCY,null,$format);
    }
}