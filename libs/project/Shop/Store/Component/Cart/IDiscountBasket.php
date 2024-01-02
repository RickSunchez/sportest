<?php
namespace Shop\Store\Component\Cart;


interface IDiscountBasket {


    /**
     * @return int
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * Is discount valid?
     * @return bool
     */
    public function isValid();


} 