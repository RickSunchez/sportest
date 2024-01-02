<?php
namespace Shop\Store\Controller;

use CMS\Mail\Model\Notification\NotifySystem;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Exception\NotFound;
use Delorius\Utils\Validators;
use Shop\Store\Component\Cart\Helpers;
use Shop\Store\Component\Cart\OrderCart;
use Shop\Store\Entity\ErrorOrder;
use Shop\Store\Entity\Order;


/**
 * @User(isLoggedIn=false)
 */
class OrderController extends Controller
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
     * @service notify.sender
     * @inject
     */
    public $notify;

    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('shop.store');
        $this->layout($this->config['layout']['order']);
        if ($this->config['order_auth'] == true && !$this->user->isLoggedIn()) {
            throw new ForbiddenAccess('Пользователь должен быть авторизован');
        }
    }

    /**
     * @AddTitle Ваш заказ
     * @param $order_code
     * @param $hash
     * @throws Error
     * @throws NotFound
     */
    public function showAction($order_code, $hash)
    {
        $order = Order::model()->where('hash', '=', $hash)->find();

        if (!$order->loaded() || $order->code != $order_code) {
            throw new NotFound('Заказ не найден');
        }
        $var['order'] = $order;
        $var['form'] = $order->getOptions();
        $var['config'] = $order->getConfig();
        $var['items'] = $order->getItems();
        $var['order_cart'] = new OrderCart($order);
        $this->response($this->view->load('shop/checkout/show', $var));

        $this->httpResponse->setHeader('X-Robots-Tag','noindex, nofollow');
        $this->getHeader()->setMetaTag('robots','noindex');
    }

    /**
     * @Post
     */
    public function checkoutDataAction()
    {
        $form = $this->httpRequest->getPost('form', array());
        $pos = $this->httpRequest->getPost('pos', false);
        try {
            if ($this->basket->isEmpty()) {
                throw new Error(_t('Shop:Store', 'Cart is empty'));
            }

            if (count($form) == 0) {
                throw new Error('Не указаны данные заказа');
            }

            if (!Validators::isEmail($form['email'])) {
                throw new Error(_t('CMS:Users', 'Email Add a valid'));
            }

            $order = Helpers::checkout($form, $pos);
            if ($order->onCheckoutError()) {
                $errorData = $order->getErrorData();
                $type = $errorData['type'];
                $data = $errorData['data'];
                switch ($type) {
                    case 'amount':
                        $goods = $data['goods'];
                        if (!$goods) {
                            throw new Error('Неверные данные');
                        }
                        $goodsName = $goods->name;
                        $goodsAmount = (float)$goods->amount;
                        $basketGoodsAmount = (float)$this->basket->getQuantity($goods->combination_hash);
                        throw new Error(
                            'Товар "' . $goodsName .
                            '" не доступен для заказа в количестве ' .
                            $basketGoodsAmount . ' шт.' .
                            '(доступно на складе: ' . $goodsAmount . ' шт.)'
                        );
                        break;
                }
            }

            $result['ok'] = _t('Shop:Store', 'Order issued');
            $result['link'] = $order->link();
            $result['order'] = $order->as_array();

            $html = $this->view->load('shop/checkout/_mail_admin', array(
                'basket' => $this->basket,
                'order' => $order
            ));
            $this->notifySystem->send('Новый заказ № ' . $order->getNumber(), $html);

            $html = $this->view->load('shop/checkout/_mail', array(
                'basket' => $this->basket,
                'order' => $order
            ));
            $this->notify->setAddressee($order->email, 'Заказ ' . $order->code);
            $this->notify->send('Ваш заказ № ' . $order->getNumber(), $html);

            $this->basket->clear();

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

}