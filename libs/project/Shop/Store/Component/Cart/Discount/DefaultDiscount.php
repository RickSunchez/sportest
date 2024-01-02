<?php
namespace Shop\Store\Component\Cart\Discount;


class DefaultDiscount
{

    protected $values = array();


    public function __construct($values)
    {
        $this->values = $values;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->values['id'];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->values['value'];
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->values['price'];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->values['label'] ? $this->values['label'] : 'Скидка';
    }

    /**
     * Is discount valid?
     * @return bool
     */
    public function isValid()
    {
        return $this->values['is_active'];
    }


    public function getPercent()
    {
        return $this->values['percent'];
    }
} 