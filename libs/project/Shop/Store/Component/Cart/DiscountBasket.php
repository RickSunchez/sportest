<?php
namespace Shop\Store\Component\Cart;

use Delorius\ComponentModel\Container;
use Delorius\Core\Environment;

class DiscountBasket extends Container implements IDiscountBasket
{

    /**
     * @var \Shop\Store\Component\Cart\ACart
     */
    public $cart;

    /** @var bool */
    private $_valid;

    /**
     * @var \Shop\Store\Component\Cart\IDiscountType
     */
    protected $type;

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     */
    protected $currency;

    /** @var \Delorius\DI\Container */
    protected $container;

    /**
     * @var array
     */
    protected $discounts;

    public function __construct(ACart $cart)
    {
        $this->cart = $cart;
        $this->container = Environment::getContext();
        $this->currency = $this->container->getService('currency');
    }

    public function init()
    {
        $this->discounts = $this->container->getParameters('shop.store.discount');
        if (count($this->discounts))
            foreach ($this->discounts as $discount) {
                if ($discount['status']) {
                    $class = $discount['type'];
                    $discountType = new $class($discount);
                    $this->addComponent($discountType, $discount['name']);
                }
            }
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        $value = 0;
        if (!$this->isValid()) {
            $value = $this->cart->getValueGoods();
        } else {
            $value = $this->type->getValue($this->cart->getValueGoods());
        }
        $value = round($value);
        return $value;
    }

    /**
     * @return string
     */
    public function getPrice($format = true)
    {
        return $this->currency->format($this->getValue(), SYSTEM_CURRENCY, null, $format);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if ($this->isValid()) {
            return $this->type->getLabel();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPercent()
    {
        if ($this->isValid()) {
            return $this->type->getPercent();
        }

        return '';
    }

    /**
     * Is discount valid?
     * @return bool
     */
    public function isValid()
    {
        if ($this->_valid === NULL) {
            $this->validate();
        }
        return $this->_valid;
    }

    /**
     * @return void
     */
    protected function validate()
    {
        $this->_valid = false;
        $rules = array();

        if ($this->cart instanceof Order) {
            if (
                $this->cart->getOwner()->discount_id != 0 &&
                isset($this->discounts[$this->cart->getOwner()->discount_id]) &&
                class_exists($this->discounts[$this->cart->getOwner()->discount_id]['type'])
            ) {
                $class = $this->discounts[$this->cart->getOwner()->discount_id]['type'];
                $discountType = new $class($this->discounts[$this->cart->getOwner()->discount_id]);
                $rules[$discountType->getPriority()] = $discountType;
            }
        } else {
            foreach ($this->getComponents() as $discountType) {
                if ($discountType->valid()) {
                    if (!isset($rules[$discountType->getPriority()]))
                        $rules[$discountType->getPriority()] = $discountType;
                }
            }
        }
        krsort($rules);
        if (sizeof($rules)) {
            $this->_valid = true;
            $this->type = array_shift($rules);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        if ($this->isValid()) {
            return $this->type->getId();
        }

        return 0;
    }
}