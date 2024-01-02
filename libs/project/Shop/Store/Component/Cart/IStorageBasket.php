<?php
namespace Shop\Store\Component\Cart;


interface IStorageBasket
{
    /**
     * @param int $id
     * @param array $options
     * @param int|float $quantity
     * @param bool|false $set
     * @param int $type
     * @return mixed
     */
    public function addItem($id, $options, $quantity, $set = false, $type = CartType::TYPE_GOODS);

    /**
     * @param $cartId
     * @return void
     */
    public function removeItem($cartId);

    /**
     * @param null|int $cartId
     * @return mixed
     */
    public function getItems($cartId = null);

    /**
     * @param $cartId
     * @param array $product_data
     * @return mixed
     */
    public function setItem($cartId, array $product_data);

    /**
     * @param $cartId
     * @return bool
     */
    public function hasItem($cartId);

    /**
     * @return int
     */
    public function countItems();

    /**
     * @return void
     */
    public function clear();

    /**
     * @param $name
     * @param null $value
     * @return void
     */
    public function set($name, $value = null);

    /**
     * @param $name
     * @return mixed
     */
    public function get($name);

    /**
     * @return mixed
     */
    public function config();

} 