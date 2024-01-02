<?php
namespace Shop\Store\Component\Cart\Discount;

use Delorius\ComponentModel\Component;
use Delorius\Exception\Error;
use Shop\Store\Component\Cart\IDiscountType;

abstract class BaseDiscount extends Component implements IDiscountType
{

    protected $id;
    protected $label;
    protected $percent = 0;
    protected $func;
    protected $priority = 0;
    protected $status = 0;

    public function __construct($init)
    {
        $this->monitor('Shop\Store\Component\Cart\DiscountBasket');

        $this->id = $init['id'];
        $this->label = $init['label'];
        $this->percent = $init['percent'];
        $this->func = $init['func'];
        $this->priority = $init['priority'];
        $this->status = $init['status'];
    }

    /**
     * @return bool
     */
    public function valid()
    {
        throw new Error('It is necessary to implement the method "valid" ');
    }

    /**
     * @param float $value
     * @return float
     */
    public function getValue($value)
    {
        if ($this->percent > 0) {

            $value = $value - $value * ($this->percent / 100);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}