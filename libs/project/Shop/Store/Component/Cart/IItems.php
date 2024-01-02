<?php
namespace Shop\Store\Component\Cart;


interface IItems {

    /**
     * @return float
     */
    public function getValue();

    /**
     * @return string
     */
    public function getPrice($format = true);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDesc();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isActive();


} 