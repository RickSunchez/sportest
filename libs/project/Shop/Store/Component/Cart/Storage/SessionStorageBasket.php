<?php
namespace Shop\Store\Component\Cart\Storage;

use Delorius\Http\Session;
use Delorius\Http\SessionSection;
use Shop\Commodity\Helpers\Options;
use Shop\Store\Component\Cart\CartType;
use Shop\Store\Component\Cart\IStorageBasket;

class SessionStorageBasket implements IStorageBasket
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var SessionSection
     */
    protected $section;

    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->init();
    }


    public function init()
    {
        $this->section = $this->session->getSection('basket');
    }

    /**
     * Добавляет товар в корзину
     * @param $id ИД товара
     * @param array $options опции товара
     * @param int $quantity Кол-во товара
     * @param bool|false $set Если true устанавлевает указаное кол-ва товара, в противном случаи добавляет
     * @param int $type Тип элемента
     * @return mixed
     */
    public function addItem(
        $id,
        $options = array(),
        $quantity = 1,
        $set = false,
        $type = CartType::TYPE_GOODS
    )
    {
        $cartId = Options::getCartId($id, $options);
        if ($this->hasItem($cartId)) {
            if ($set === true)
                $this->section->items['#' . $cartId]['amount'] = $quantity;
            else
                $this->section->items['#' . $cartId]['amount'] += $quantity;
        } else {
            $product_data = array(
                'cart_id' => $cartId,
                'goods_id' => $id,
                'options' => $options,
                'amount' => $quantity,
                'type' => $type
            );
            $this->section->items['#' . $cartId] = $product_data;
        }
    }

    /**
     * @param $cartId
     * @return void
     */
    public function removeItem($cartId)
    {
        unset($this->section->items['#' . $cartId]);
    }

    /**
     * @param null $cartId
     * @return array|mixed
     */
    public function getItems($cartId = null)
    {
        return $this->countItems() != 0 ? isset($this->section->items['#' . $cartId]) ? $this->section->items['#' . $cartId] : $this->section->items : array();
    }

    /**
     * @param int $cartId
     * @param array $product_data
     * @return mixed
     */
    public function setItem($cartId, array $product_data)
    {
        $this->section->items['#' . $cartId] = $product_data;
    }

    /**
     * @param $id
     * @return bool
     */
    public function hasItem($cartId)
    {
        return isset($this->section->items['#' . $cartId]) ? true : false;
    }

    /**
     * @return int
     */
    public function countItems()
    {
        return count($this->section->items);
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->section->items = array();
    }

    /**
     * @param $name
     * @param null $value
     * @return void
     */
    public function set($name, $value = null)
    {
        if (!is_scalar($value) && $value == null) {
            unset($this->section->value[$name]);
        } else {
            $this->section->value[$name] = $value;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->section->value[$name];
    }

    /**
     * @return mixed
     */
    public function config()
    {
        return (array)$this->section->value;
    }
}