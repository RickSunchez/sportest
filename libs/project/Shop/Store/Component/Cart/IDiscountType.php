<?php
namespace Shop\Store\Component\Cart;


interface IDiscountType
{

    /**
     * @return  bool
     */
    public function valid();

    /**
     * @param float $value
     * @return float
     */
    public function getValue($value);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getPercent();

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @return int
     */
    public function getId();


} 