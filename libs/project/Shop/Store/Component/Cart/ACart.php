<?php
namespace Shop\Store\Component\Cart;

use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Tools\ILogger;
use Shop\Store\Component\Cart\City\CityNull;
use Shop\Store\Component\Cart\Delivery\DeliveryNull;
use Shop\Store\Component\Cart\Payment\PaymentNull;
use Shop\Store\Model\CurrencyBuilder;

abstract class ACart extends Object
{

    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $logger;

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    public function __construct(ILogger $logger, CurrencyBuilder $currencyBuilder)
    {
        $this->logger = $logger;
        $this->currency = $currencyBuilder;
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
    abstract function add(
        $id,
        $options = array(),
        $quantity = Helpers::DEFAULT_STEP,
        $set = false,
        $type = CartType::TYPE_GOODS
    );

    /**
     * @param int $cartId ИД cart item
     * @param string|int $quantity Кол-во товара up - добавить; down - уменьшить товар; int|float - установить указаное кол-во
     */
    abstract function setQuantity($cartId, $quantity);

    /**
     * Кол-во товара одного наименования
     * @param $cartId
     * @return int|float
     */
    abstract function getQuantity($cartId);

    /**
     * Получить тип продукта
     * @param $cartId
     * @return mixed
     */
    abstract function getType($cartId);

    /**
     * Возращает весь товар
     * @return array(Goods)
     */
    abstract function getProducts();

    /**
     * Кол-во товара в корзине
     * @return int
     */
    abstract function count();

    /**
     * @return bool
     */
    abstract function isEmpty();

    /**
     * Убирает товара
     * @param $cartId
     * @return void
     */
    abstract function remove($cartId);

    /**
     * Очистка корзины
     */
    abstract function clear();

    /************** method for storage config *************/

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    abstract function set($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    abstract function get($name);

    /**
     * @return array
     */
    abstract function config();


    /********************** price **********************/

    /**
     * Возращает общую стоимость товара в корзине в выбраной валюте
     * @return string
     */
    public function getPriceGoods($format = true)
    {
        return $this->currency->format($this->getValueGoods(), SYSTEM_CURRENCY, null, $format);
    }

    /**
     * Возращает общую стоимость товара в корзине в выбраной валюте с возможными скидками и накрутками
     * @return string
     */
    public function getPriceTotal($format = true)
    {
        return $this->currency->format($this->getValueTotal(), SYSTEM_CURRENCY, null, $format);
    }

    /**
     * Позращает значения в системной валюте
     * @return float|int
     */
    public function getValueTotal()
    {
        $value = 0;
        if ($this->getDiscount()->isValid()) {
            $value = $this->getDiscount()->getValue();
        } else {
            $value = $this->getValueGoods();
        }

        $payment = $this->getPayment();
        // 1 - это вроде как банк
        if ($payment->getId() == 1) {
            if ($value > 5000) {
                // 2.5% комиссия
                $value += $value * 0.025;
            }
        }
        
        // стоимость выбраной доставки
        $value += $this->getDelivery()->getValue();

        return $value;
    }

    /**
     * Возращает общию стоимость товара в корзине в системной валюте
     * @return float|int
     */
    public function getValueGoods()
    {
        $goods = $this->getProducts();
        $total = 0;
        foreach ($goods as $item) {
            $total += $item->getValue() * $this->getQuantity($item->combination_hash);
        }
        return $total;
    }

    /********************** end price **********************/


    /**************** accompanying *********************/


    /**
     * Способ доставки
     * @var IItems
     */
    protected $_delivery;

    /**
     * @return IItems
     */
    public function getDelivery()
    {
        if (!$this->_delivery) {
            $id = $this->getDeliveryId();
            if (!$id) {
                return $this->_delivery = new DeliveryNull();
            }
            $deliveries = Environment::countedConfig(
                Environment::getContext()->getParameters('shop.store.delivery', array())
            );
            $delivery_default = Environment::getContext()->getParameters('shop.store.delivery_default');

            if (
                !$id ||
                !isset($deliveries[$id]) ||
                !$deliveries[$id]['status']
            ) {

                if ($delivery_default && $deliveries[$delivery_default]) {
                    $default = $deliveries[$delivery_default];
                    $class = $default['type'];
                    if (class_exists($class) && is_array($default)) {
                        return $this->_delivery = new $class($this, $default);
                    } else {
                        $this->logger->error(_sf('Not found default class by delivery : "{0}" ', $class), 'basket');
                    }
                }

            } else {
                $current = $deliveries[$id];
                $class = $current['type'];
                if (class_exists($class))
                    return $this->_delivery = new $class($this, $current);
                else {
                    $this->logger->error(_sf('Not found class by delivery : "{0}" ', $class), 'basket');
                }
            }

            return $this->_delivery = new DeliveryNull();
        }

        return $this->_delivery;
    }

    /**
     * @param int $id
     */
    public function setDeliveryId($id)
    {
        $this->set('delivery_id', $id);
        $this->_delivery = null;
    }

    /**
     * @return int
     */
    public function getDeliveryId()
    {
        return $this->get('delivery_id');
    }

    /**
     * Город доставки
     * @var IItems|CityNull
     */
    protected $_city;

    /**
     * @return IItems|CityNull
     */
    public function getCity()
    {
        if (!$this->_city) {
            $id = $this->getCityId();
            if (!$id) {
                return $this->_city = new CityNull();
            }
            $cities = Environment::countedConfig(
                Environment::getContext()->getParameters('shop.store.cities', array())
            );
            $city_default = Environment::getContext()->getParameters('shop.store.city_default');
            $cities_type = Environment::getContext()->getParameters('shop.store.cities_type');

            if (isset($cities[$id])) {
                $current = $cities[$id];
                $class = $cities_type;
                if (class_exists($class) && is_array($current)) {
                    return $this->_city = new $class($this, $current);
                } else {
                    $this->logger->error(_sf('Not found default class by city : "{0}" ', $class), 'basket');
                }
            } elseif (isset($cities[$city_default])) {
                $default = $cities[$city_default];
                if (class_exists($cities_type) && is_array($default)) {
                    return $this->_city = new $cities_type($this, $default);
                } else {
                    $this->logger->error(_sf('Not found default class by city : "{0}" ', $cities_type), 'basket');
                }
            }
            $this->_city = new CityNull();
        }

        return $this->_city;
    }

    /**
     * @param int $id
     */
    public function setCityId($id)
    {
        $this->set('city_id', $id);
        $this->_city = null;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->get('city_id');
    }

    /**
     * @param int $id
     */
    public function setPointId($id)
    {
        $this->set('point_id', $id);
    }

    /**
     * @return int
     */
    public function getPointId()
    {
        return $this->get('point_id');
    }

    /**
     * @return mixed
     * @throws \Delorius\Exception\Error
     */
    public function getPoint()
    {
        $cities = Environment::countedConfig(Environment::getContext()->getParameters('shop.store.cities', array()));
        $cityId = $this->getCity()->getId();
        $pointId = $this->getPointId();
        return $cities[$cityId]['points'][$pointId];
    }

    /**
     * Способ оплаты
     */
    protected $_payment;

    /**
     * @return IItems|PaymentNull
     */
    public function getPayment()
    {
        if (!$this->_payment) {
            $id = $this->getPaymentId();
            if (!$id) {
                return $this->_payment = new PaymentNull();
            }
            $payment_methods = Environment::countedConfig(
                Environment::getContext()->getParameters('shop.store.payment_method')
            );
            $payment_method_default = Environment::getContext()->getParameters('shop.store.payment_method_default');

            if (isset($payment_methods[$id])
            ) {
                $current = $payment_methods[$id];
                $class = $current['type'];
                if (class_exists($class)) {
                    return $this->_payment = new $class($this, $current);
                } else {
                    $this->logger->error(_sf('Not found default class by payment : "{0}" ', $class), 'basket');
                }
            } else {
                $default = $payment_methods[$payment_method_default];
                $class = $default['type'];
                if (class_exists($class) && is_array($default)) {
                    return $this->_payment = new $class($this, $default);
                } else {
                    $this->logger->error(_sf('Not found default class by payment : "{0}" ', $class), 'basket');
                }
            }

            return $this->_payment = new PaymentNull();
        }

        return $this->_payment;
    }

    /**
     * @param int $id
     */
    public function setPaymentId($id)
    {
        $this->set('payment_id', $id);
        $this->_payment = null;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->get('payment_id');
    }

    /**
     * @var \Shop\Store\Component\Cart\DiscountBasket
     */
    protected $_discount;

    /**
     * @return DiscountBasket
     */
    public function getDiscount()
    {
        if (!$this->_discount) {
            $this->_discount = new DiscountBasket($this);
            $this->_discount->init();
        }
        return $this->_discount;
    }

    /**
     * @param IDiscountBasket $discountBasket
     */
    public function setDiscount(IDiscountBasket $discountBasket)
    {
        $this->_discount = $discountBasket;
    }

    /**************** end accompanying *********************/

    public function as_array()
    {
        $arr = array(
            'count' => $this->count(),
            'total' => array(
                'value' => $this->getValueTotal(),
                'price' => $this->getPriceTotal(),
                'price_raw' => $this->getPriceTotal(false),
            ),
            'goods' => array(
                'value' => $this->getValueGoods(),
                'price' => $this->getPriceGoods(),
                'price_raw' => $this->getPriceGoods(false),
            ),
            'discount' => array(
                'id' => $this->getDiscount()->getId(),
                'is_active' => $this->getDiscount()->isValid(),
                'label' => $this->getDiscount()->getLabel(),
                'percent' => $this->getDiscount()->getPercent(),
                'value' => $this->getDiscount()->getValue(),
                'price' => $this->getDiscount()->getPrice(),
            ),
            'city' => array(
                'id' => $this->getCity()->getId(),
                'is_active' => $this->getCity()->isActive(),
                'name' => $this->getCity()->getName(),
                'value' => $this->getCity()->getValue(),
                'price' => $this->getCity()->getPrice(),
                'desc' => $this->getCity()->getDesc(),
            ),
            'payment' => array(
                'id' => $this->getPayment()->getId(),
                'is_active' => $this->getPayment()->isActive(),
                'name' => $this->getPayment()->getName(),
                'value' => $this->getPayment()->getValue(),
                'price' => $this->getPayment()->getPrice(),
                'desc' => $this->getPayment()->getDesc(),
            ),
            'delivery' => array(
                'id' => $this->getDelivery()->getId(),
                'is_active' => $this->getDelivery()->isActive(),
                'name' => $this->getDelivery()->getName(),
                'value' => $this->getDelivery()->getValue(),
                'price' => $this->getDelivery()->getPrice(),
                'desc' => $this->getDelivery()->getDesc(),
            ),
            'config' => $this->config()
        );
        return $arr;
    }

} 