<?php

namespace Shop\Store\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Callback;
use CMS\Mail\Model\Notification\NotifySystem;
use Delorius\Application\UI\Controller;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Helpers\Options;
use Shop\Commodity\Helpers\Popular;
use Shop\Store\Component\Cart\CartType;
use Shop\Store\Component\Cart\Helpers;

/**
 * @User(isLoggedIn=false)
 */
class CartController extends Controller
{

    /**
     * @var \Shop\Store\Component\Cart\Basket
     * @service basket
     * @inject
     */
    public $basket;

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * @var NotifySystem
     * @service notify.system
     * @inject
     */
    public $notifySystem;

    /**
     * @var Register
     * @inject
     */
    public $register;

    /** @var array */
    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('shop.store');
        $this->layout($this->config['layout']['cart']);
    }

    /**
     * @AddTitle Корзина
     * @Get
     */
    public function listAction()
    {
        $this->httpResponse->setHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->getHeader()->setMetaTag('robots', 'noindex');

        if (!$this->basket->isEmpty()) {
            $this->response($this->view->load('shop/cart/list'));
        } else {
            $this->response($this->view->load('shop/cart/none'));
        }
    }

    /**
     * @Post
     */
    public function getCartDataAction($is_config = true)
    {
        $goods = $this->basket->getProducts();
        $result['goods'] = array();
        foreach ($goods as $item) {
            $result['goods'][] = Helpers::parserGoods($item);
        }
        $result['basket'] = $this->basket->as_array();


        if ($is_config) {
            if ($this->user->isLoggedIn())
                $result['user'] = $this->user->getIdentity()->getData();

            $result['delivery'] = Helpers::parserConfig($this->config['delivery']);
            $result['cities'] = Environment::countedConfig((array)$this->config['cities']);
            $result['payment_method'] = Helpers::parserConfig($this->config['payment_method']);
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function changeParamsDataAction()
    {
        $post = $this->httpRequest->getPost();

        if (isset($post['type']) && isset($post['cart_id'])) {
            $this->basket->setQuantity($post['cart_id'], $post['type']);
        }

        if (isset($post['delivery_id']) && is_scalar($post['delivery_id'])) {
            $this->basket->setDeliveryId($post['delivery_id']);
        }

        if (isset($post['payment_id']) && is_scalar($post['payment_id'])) {
            $this->basket->setPaymentId($post['payment_id']);
        }

        if (isset($post['city_id']) && is_scalar($post['city_id'])) {
            $this->basket->setCityId($post['city_id']);
        }

        if (isset($post['point_id']) && is_scalar($post['point_id'])) {
            $this->basket->setPointId($post['point_id']);
        }

        if (isset($post['point_id']) && is_scalar($post['point_id'])) {
            $this->basket->setPointId($post['point_id']);
        }

        if (isset($post['config']) && count($post['config'])) {
            foreach ($post['config'] as $name => $value) {
                $this->basket->set($name, $value);
            }
        }

        $this->forward('Shop:Store:Cart:getCartData', array('is_config' => false));
    }

    /**
     * @Post
     */
    public function deleteGoodsAction()
    {
        $cartId = $this->httpRequest->getRequest('cartId');
        $this->basket->remove($cartId);
        $this->forward('Shop:Store:Cart:getCartData', array('is_config' => false));
    }

    /**
     * @Post
     */
    public function addGoodsAction()
    {
        /**
         * $product_data = [
         *  [goods] = [goods_id,amount,options[option=>value..]]
         *  [additions] = [goods_id,amount,options[option=>value..]] ..
         * ]
         */
        $product_data = $this->httpRequest->getRequest('product_data', array());

        try {
            $goods = new Goods($product_data['goods']['goods_id']);
            if (!$goods->loaded()) {
                throw new Error('Не указан товар');
            }
            if ($errors = Options::checkout($goods, $product_data['goods']['options'])) {
                throw new Error('Заполните обезательные поля в товаре');
            }

            $quantity = Helpers::calc($product_data['goods']['amount'], $goods->minimum, $goods->maximum, $goods->step);
            Options::accept($goods, $product_data['goods']['options']);
            $this->basket->add($goods->pk(), $product_data['goods']['options'], $quantity, false, CartType::TYPE_GOODS);

            #additions
            $additions = array();
            if (count($product_data['additions'])) {
                foreach ($product_data['additions'] as $data) {
                    $addition = new Goods($data['goods_id']);
                    if ($addition->loaded()) {
                        $quantity = Helpers::calc($data['amount'], $addition->minimum, $addition->maximum, $addition->step);
                        Options::accept($addition, $data['options']);
                        $this->basket->add($addition->pk(), $data['options'], $quantity, false, CartType::TYPE_GOODS);
                        $additions[] = $addition;
                    }
                }
            }


            $result['total'] = $this->basket->getPriceGoods();
            $result['count'] = $this->basket->count();
            $result['html'] = $this->view->load('shop/goods/_item_basket', array(
                'goods' => $goods,
                'additions' => $additions,
                'quantity' => $quantity,
            ));
            $result['ok'] = 'Товар добавлен в корзину';
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
            $result['options'] = $errors;
        }

        $this->response($result);

    }

    /**
     * @Post
     */
    public function callbackFormDataAction()
    {

        /**
         * $product_data = [
         *  [goods] = [goods_id,amount,options[option=>value..]]
         *  [additions] = [goods_id,amount,options[option=>value..]] ..
         *  [image] = bool / default = false
         * ]
         */
        $product_data = $this->httpRequest->getRequest('product_data', array());

        try {
            $goods = new Goods($product_data['goods']['goods_id']);
            if (!$goods->loaded()) {
                throw new Error('Не указан товар');
            }
            Options::accept($goods, $product_data['goods']['options'], $product_data['goods']['image']);

            #additions
            $additions = array();
            if (count($product_data['additions'])) {
                foreach ($product_data['additions'] as $data) {
                    $addition = new Goods($data['goods_id']);
                    if ($addition->loaded()) {
                        Options::accept($addition, $data['options'], $data['image']);
                        $additions[] = $addition;
                    }
                }
            }

            $result['html'] = $this->view->load('shop/goods/_one_click', array(
                'product_data' => $product_data,
                'goods' => $goods,
                'additions' => $additions,
            ));
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function callbackDataAction()
    {
        /**
         * $product_data = [
         *  [goods] = [goods_id,amount,options[option=>value..]]
         *  [additions] = [goods_id,amount,options[option=>value..]] ..
         *  [image] = bool / default = false
         * ]
         */


        $product_data = $this->httpRequest->getRequest('product_data', array());
        $form = $this->httpRequest->getRequest('form', array());
        $pos = $this->httpRequest->getRequest('pos', array());

        try {

            if (count($form) == 0) {
                throw new Error('Не указаны данные заказа');
            } else {
                $form['one_click'] = 'true';
            }

            #basket_start
            $goods = new Goods($product_data['goods']['goods_id']);
            if (!$goods->loaded()) {
                throw new Error('Не указан товар');
            }
            if ($errors = Options::checkout($goods, $product_data['goods']['options'])) {
                throw new Error('Заполните обезательные поля в товаре');
            }

            #strat
            $this->basket->clear();

            #goods
            $quantity = Helpers::calc($product_data['goods']['amount'], $goods->minimum, $goods->maximum, $goods->step);
            Options::accept($goods, $product_data['goods']['options']);
            $this->basket->add($goods->pk(), $product_data['goods']['options'], $quantity, false, CartType::TYPE_GOODS);

            $this->basket->setDeliveryId(0);
            $this->basket->setPaymentId(0);

            $result['products'][] = array(
                'id' => $goods->pk(),
                'name' => $goods->name,
                'price' => $goods->getPrice(false, false),
                'category' => $goods->getCategoriesStr(),
                'brand' => $goods->getVendor(),
                'quantity' => $quantity,
                'variant' => $goods->combination_hash
            );

            #additions
            $additions = array();
            if (count($product_data['additions'])) {
                foreach ($product_data['additions'] as $data) {
                    $addition = new Goods($data['goods_id']);
                    if ($addition->loaded()) {
                        $quantity = Helpers::calc($data['amount'], $addition->minimum, $addition->maximum, $addition->step);
                        Options::accept($addition, $data['options']);
                        $this->basket->add($addition->pk(), $data['options'], $quantity, false, CartType::TYPE_GOODS);
                        $additions[] = $addition;

                        $result['products'][] = array(
                            'id' => $addition->pk(),
                            'name' => $addition->name,
                            'price' => $addition->getPrice(false, false),
                            'category' => $addition->getCategoriesStr(),
                            'brand' => $addition->getVendor(),
                            'quantity' => $quantity,
                            'variant' => $addition->combination_hash
                        );

                    }
                }
            }
            #basket_end

            if ($this->basket->isEmpty()) {
                throw new Error(_t('Shop:Store', 'Cart is empty'));
            }

            $order = Helpers::checkout($form, $pos);

            $html = $this->view->load('shop/checkout/_mail_admin', array(
                'basket' => $this->basket,
                'order' => $order
            ));
            $this->notifySystem->send('Быстрый заказ № ' . $order->getNumber(), $html);
            $this->basket->clear();

            $result['ok'] = _t('Shop:Store', 'Order issued');
            $result['link'] = $order->link();
            $result['order'] = $order->as_array();

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function clearGoodsAction()
    {
        $this->basket->clear();
        $this->response(array('ok'));
    }

    /**
     * MINI CART
     */

    /**
     * @param bool|false $list
     * @param null $theme
     */
    public function cartMiniPartial($list = false, $theme = null)
    {
        if ('shop_cart' == $this->getRouterName()) {
            return;
        }

        if ($list) {
            $goods = $this->basket->getProducts();
            $var['goods'] = array();
            foreach ($goods as $item) {
                $var['goods'][] = Helpers::parserGoods($item);;
            }
        }

        $count = $this->basket->count();
        $var['count'] = $count;
        $var['count_prefix'] = Strings::pluralForm($count, 'товар', 'товара', 'товаров');
        $var['value'] = $this->basket->getValueGoods();
        $var['price'] = $this->basket->getPriceGoods();
        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('shop/cart/_mini' . $theme, $var));
    }

    /**
     * @Post
     */
    public function getCartMiniDataAction()
    {
        $list = $this->httpRequest->getRequest('list', false);
        if ($list) {
            $goods = $this->basket->getProducts();
            $result['goods'] = array();
            foreach ($goods as $item) {
                $result['goods'][] = Helpers::parserGoods($item);;
            }
        }
        $count = $this->basket->count();
        $result['count'] = $count;
        $result['count_prefix'] = Strings::pluralForm($count, 'товар', 'товара', 'товаров');
        $result['value'] = $this->basket->getValueGoods();
        $result['price'] = $this->basket->getPriceGoods();
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteMiniGoodsAction()
    {
        $cartId = $this->httpRequest->getRequest('cartId');
        $list = $this->httpRequest->getRequest('list', false);
        $this->basket->remove($cartId);
        $this->forward('Shop:Store:Cart:getCartMiniData', array('list' => $list));
    }

}