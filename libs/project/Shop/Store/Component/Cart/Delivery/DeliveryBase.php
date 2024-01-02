<?php
namespace Shop\Store\Component\Cart\Delivery;

use Delorius\Core\Environment;
use Shop\Store\Component\Cart\ACart;
use Shop\Store\Component\Cart\IItems;

abstract class DeliveryBase implements IItems
{
    protected $id;
    protected $name;
    protected $value;
    protected $desc;
    protected $status;

    /**
     * @var \Shop\Store\Component\Cart\ACart
     */
    protected $cart;

    public function __construct(ACart $cart, $config)
    {
        $this->cart = $cart;
        $this->id = $config['id'];
        $this->name = $config['name'];
        $this->value = $config['value'];
        $this->desc = $config['desc'];
    }


    /**
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
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