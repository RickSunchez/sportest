<?php
namespace Shop\Store\Component\Cart\City;

use Delorius\Core\Environment;
use Shop\Store\Component\Cart\ACart;
use Shop\Store\Component\Cart\IItems;

abstract class CityBase implements IItems
{
    protected $id;
    protected $name;
    protected $value;
    protected $is_active;

    /**
     * @var \Shop\Store\Component\Cart\ACart
     */
    protected $cart;

    public function __construct(ACart $cart,$value)
    {
        $this->cart = $cart;
        $this->id = $value['id'];
        $this->name = $value['name'];
        $this->value = $value['value'];
        $this->is_active = true;
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
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return _sf('Доставка в {0}', $this->name);
    }

    /**
     * @return string
     */
    public function getPrice($format = true)
    {
        return Environment::getContext()
            ->getService('currency')
            ->format($this->getValue(), SYSTEM_CURRENCY, null, $format);
    }
}