<?php
namespace Shop\Store\Component\Cart;

use Delorius\Core\Environment;
use Shop\Commodity\Entity\Goods;
use Shop\Store\Component\Cart\Delivery\DeliveryOrder;
use Shop\Store\Component\Cart\Discount\DefaultDiscount;
use Shop\Store\Entity\Order as OrmOrder;

class OrderCart extends ACart
{

    /**
     * @var \Shop\Store\Entity\Order
     */
    protected $orm;

    /**
     * @var bool
     */
    protected $is_change = false;

    /**
     * @var array
     */
    private $_items = array();
    /**
     * @var array
     */
    private $_goods = array();

    public function __construct(OrmOrder $order)
    {
        parent::__construct(
            Environment::getContext()->getService('logger'),
            Environment::getContext()->getService('currency')
        );
        $this->orm = $order;
    }

    public function add(
        $id,
        $options = array(),
        $quantity = Helpers::DEFAULT_STEP,
        $set = false,
        $type = CartType::TYPE_GOODS
    )
    {
    }

    public function setQuantity($cartId, $quantity)
    {
        $items = $this->getItems();
        if (isset($items[$cartId])) {
            $items[$cartId]->amount = $quantity ? $quantity : 1;
            $items[$cartId]->save();
        }

    }

    public function getQuantity($cartId)
    {
        $items = $this->getItems();
        return isset($items[$cartId]) ? $items[$cartId]->amount : 0;
    }

    public function getType($cartId)
    {
        $items = $this->getItems();
        return isset($items[$cartId]) ? $items[$cartId]->item_type : 0;
    }


    public function getProducts()
    {
        if (!sizeof($this->_goods)) {
            foreach ($this->orm->getItems() as $item) {
                $product_data = \Delorius\Utils\Arrays::get($item->getConfig(), 'goods');
                $product_data['value'] = $item->value;
                $product_data['amount'] = $item->amount;
                $this->_goods[] = Goods::mock($product_data);
            }
        }

        return $this->_goods;
    }

    public function getValueGoods()
    {
        $goods = $this->getItems();
        $value = 0;
        foreach ($goods as $item) {
            $value += $item->value * $item->amount;
        }

        return $value;
    }

    public function getValueTotal()
    {
        return $this->orm->value;
    }


    public function count()
    {
        return count($this->getItems());
    }

    public function isEmpty()
    {
        !count($this->getItems()) ? true : false;
    }


    public function remove($cartId)
    {
    }

    public function clear()
    {
    }



    /******************* order orm *****************/

    /**
     * @param int $id
     */
    public function setPaymentId($id)
    {
        $config = $this->get('config');
        $config['payment_id'] = $id;
        $this->set('config', $config);
        $this->_payment = null;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        $config = $this->get('config');
        return $config['payment_id'];
    }


    /************** method for storage config *************/

    function set($name, $value)
    {
        $config = $this->orm->getConfig();
        $config[$name] = $value;
        $this->orm->setConfig($config);
        $this->orm->save();
    }

    function get($name)
    {
        $config = $this->orm->getConfig();
        return $config[$name];
    }

    /**
     * @return OrmOrder
     */
    public function getOwner()
    {
        return $this->orm;
    }

    /**
     * @return array
     */
    public function config()
    {
        $config = $this->orm->getConfig();
        return $config['config'];
    }


    protected function getItems()
    {
        if (!sizeof($this->_items)) {
            foreach ($this->orm->getItems() as $item) {
                $this->_items[$item->item_id] = $item;
            }
        }
        return $this->_items;
    }


    public function getDiscount()
    {
        if (!$this->_discount) {
            $discount = $this->get('discount');
            $this->_discount = new DefaultDiscount($discount);
        }
        return $this->_discount;
    }

    public function getDelivery()
    {
        if (!$this->_delivery) {
            $delivery = $this->get('delivery');
            $this->_delivery = new DeliveryOrder($delivery);
        }
        return $this->_delivery;
    }


    public function update()
    {
        $config = $this->orm->getConfig();
        $goods = $this->getItems();
        $value = 0;
        foreach ($goods as $item) {
            $value += $item->value * $item->amount;
        }

        if ($config['discount']['percent'] == 0) {
            $config['discount']['is_active'] = false;
            $config['discount']['value'] = $value;
            $config['discount']['price'] = $this->currency->format($value, SYSTEM_CURRENCY, null, true);
        } else {
            $value = $value - $value * ($config['discount']['percent'] / 100);
            $config['discount']['is_active'] = true;
            $config['discount']['value'] = $value;
            $config['discount']['price'] = $this->currency->format($value, SYSTEM_CURRENCY, null, true);
        }

        if ($config['delivery']['value'] > 0) {
            $value += $config['delivery']['value'];
        }

        $this->orm->value = $value;
        $this->orm->setConfig($config);
        $this->orm->save();
        $config = $this->as_array();
        $this->orm->setConfig($config);
        $this->orm->save();
    }

}
