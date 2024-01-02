<?php
namespace Shop\Store\Component\Cart;

use Delorius\Tools\ILogger;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Helpers\Options;
use Shop\Store\Model\CurrencyBuilder;

class Basket extends ACart
{

    public $onAddItem;
    public $onRemoveItem;
    public $onCleanItem;

    /**
     * @var IStorageBasket
     * @inject
     */
    public $store;

    /**
     * @var array
     */
    private $_items = array();

    public function __construct(
        ILogger $logger,
        CurrencyBuilder $currencyBuilder,
        IStorageBasket $storageBasket
    )
    {
        parent::__construct($logger, $currencyBuilder);
        $this->store = $storageBasket;
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
    public function add(
        $id,
        $options = array(),
        $quantity = Helpers::DEFAULT_STEP,
        $set = false,
        $type = CartType::TYPE_GOODS
    )
    {
        $this->onAddItem($this, $id, $options, $quantity, $set, $type);
        $this->clean();
        $this->store->addItem($id, $options, $quantity, $set, $type);
    }

    /**
     * @param int $cartId ИД cart item
     * @param string|int $quantity Кол-во товара up - добавить; down - уменьшить товар; int|float - установить указаное кол-во
     */
    function setQuantity($cartId, $quantity)
    {
        $product_data = $this->getItem($cartId);
        $goods = new Goods($product_data['goods_id']);
        if (!$goods->loaded()) {
            $this->remove($cartId);
            return;
        }

        if ($quantity == 'up') {
            $product_data['amount'] = Helpers::calc($product_data['amount'] + $goods->step, $goods->minimum, $goods->maximum, $goods->step);
        } elseif ($quantity == 'down') {
            $product_data['amount'] = Helpers::calc($product_data['amount'] - $goods->step, $goods->minimum, $goods->maximum, $goods->step);
        } else {
            $product_data['amount'] = Helpers::calc($quantity, $goods->minimum, $goods->maximum, $goods->step);
        }

        $this->store->setItem($cartId, $product_data);
    }


    /**
     * Кол-во товара одного наименования
     * @param $cartId
     * @return int|float
     */
    public function getQuantity($cartId)
    {
        $item = $this->getItem($cartId);
        return $item['amount'];
    }

    /**
     * Получить тип продукта
     * @param $cartId
     * @return mixed
     */
    public function getType($cartId)
    {
        $item = $this->getItem($cartId);
        return $item['type'];
    }


    /**
     * Убирает товара из корзины
     * @param $cartId
     * @return void
     */
    public function remove($cartId)
    {
        $this->onRemoveItem($this, $cartId);
        $this->clean();
        $this->store->removeItem($cartId);
    }

    /**
     * Очистка корзины
     */
    public function clear()
    {
        $this->onCleanItem($this);
        $this->clean();
        $this->store->clear();
    }

    /**
     * Возращает весь товар в корзине
     * @return array(Goods)
     */
    public function getProducts()
    {
        if (!sizeof($this->_items)) {
            foreach ($this->store->getItems() as $product_data) {

                $goods = Goods::model($product_data['goods_id']);
                if (!$goods->loaded() || $goods->status == 0) {
                    $this->remove($product_data['cart_id']);
                    continue;
                }
                Options::accept($goods, $product_data['options']);
                $this->_items[] = $goods;
            }
        }
        return $this->_items;
    }

    /**
     * Кол-во товара в корзине
     * @return int
     */
    public function count()
    {
        return $this->store->countItems();
    }

    /**
     * @param null $cartId
     * @return mixed
     */
    protected function getItem($cartId = null)
    {
        return $this->store->getItems($cartId);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->store->countItems() == 0 ? true : false;
    }

    /************** method for storage config *************/
    function set($name, $value)
    {
        $this->store->set($name, $value);
    }

    function get($name)
    {
        return $this->store->get($name);
    }


    /**
     * @return void
     */
    protected function clean()
    {
        $this->_items = array();
    }

    /**
     * @return array
     */
    function config()
    {
        return $this->store->config();
    }
}