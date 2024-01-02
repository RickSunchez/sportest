<?php
namespace Shop\Payment\Handler;

use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Forms\Form;
use Delorius\Http\Curl\ClientCurl;
use Delorius\Utils\Json;
use Delorius\View\Html;
use Shop\Store\Entity\Order;

class SberbankHandler extends HandlerPayment
{

    const SERVER_TEST = 'https://3dsec.sberbank.ru/payment/rest/';
    const SERVER_PRODUCTION = 'https://securepayments.sberbank.ru/payment/rest/';

    /** @var  string */
    protected $login;
    /** @var  string */
    protected $pass;
    /** @var  bool */
    protected $test;
    /** @var  string (GET|POST) */
    protected $method;

    protected function init()
    {
        $config = Environment::getContext()->getParameters('shop.payment.sberbank');
        $this->login = $config['login'];
        $this->pass = $config['pass'];
        $this->test = $config['test'];
        $this->method = $config['method'];
    }

    /**
     * Get Url server merchant
     * @return string
     */
    protected function getServerUrl()
    {
        return $this->test ? self::SERVER_TEST : self::SERVER_PRODUCTION;
    }

    /**
     * @return string|Html
     */
    public function render()
    {
        $form = new Form('sberbank');
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'dl';
        $renderer->wrappers['pair']['container'] = NULL;
        $renderer->wrappers['label']['container'] = NULL;
        $renderer->wrappers['control']['container'] = NULL;
        $form->setAction(link_to('payment_sberbank', array('action' => 'payment')));
        $form->setMethod($this->method);
        $form->addHidden('orderId')->setValue($this->account->target_id);
        $form->addSubmit('payment', 'Оплата заказа банковской картой');

        return $form;
    }

    public function register($trans = array())
    {
        $order = Order::model($trans['orderId']);
        if (!$order->loaded()) {
            $this->badSignal($trans);
        }
        $this->account = $order->getAccount();
        if (!$this->account->loaded()) {
            $this->badSignal($trans);
        }

        if ($order->external_id) {
            $config = $order->getConfig();
            if (($url = $config['sberbank']['formUrl'])) {
                Environment::getContext()->getService('httpResponse')
                    ->redirect($url);
                exit();
            }
        }

        $filed = array(
            'userName' => $this->login,
            'password' => $this->pass,
            'orderNumber' => $this->account->target_id,
            'amount' => $this->account->value * 100,
            'returnUrl' => link_to('payment_sberbank', array('action' => 'success')),
            'failUrl' => link_to('payment_sberbank', array('action' => 'fail')),
        );

        $response = $this->gateway('register.do', $filed);
        $result = Json::decode($response->getResponse());
        $this->logger->error('Response: ' . var_export($result,true), 'SberbankHandlerGateway');

        if ($result['errorCode']) {
            $this->logger->error('Error api ' . $result['errorMessage'], 'SberbankHandler');
            Environment::getContext()->getService('httpResponse')
                ->redirect(link_to('payment_sberbank', array(
                    'action' => 'fail',
                    'msg' => $result['errorMessage']
                )));
            exit();
        }

        $config = $order->getConfig();
        $config['sberbank'] = $result;
        $order->setConfig($config);
        $order->external_id = $result['orderId'];
        $order->save();

        Environment::getContext()->getService('httpResponse')
            ->redirect($result['formUrl']);
        exit();
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function success($trans = array())
    {
        $order = Order::model()->where('external_id', '=', $trans['orderId'])->find();
        if (!$order->loaded()) {
            $this->badSignal($trans);
        }
        $this->account = $order->getAccount();
        if (!$this->account->loaded()) {
            $this->badSignal($trans);
        }

        $filed = array(
            'userName' => $this->login,
            'password' => $this->pass,
            'orderId' => $trans['orderId'],
            'orderNumber' => $this->account->target_id
        );

        $response = $this->gateway('getOrderStatusExtended.do', $filed);
        $result = Json::decode($response->getResponse());

        if (($result['orderStatus'] == 1 || $result['orderStatus'] == 2) && $result['errorCode'] == 0) {

            try {
                $this->account->paid();
                $result = $this->account->success();
                $this->ok($trans);
            } catch (Error $e) {
                $this->logger->error($e->getMessage(), 'SberbankHandler');
                $this->badSignal($trans);
            }

        } else {
            try {
                $result = $this->account->fail();
            } catch (Error $e) {
                $this->logger->error($e->getMessage(), 'SberbankHandler');
                $this->badSignal($trans);
            }
        }

        return $result;
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function fail($trans = array())
    {
        $order = Order::model()->where('external_id', '=', $trans['orderId'])->find();
        if (!$order->loaded()) {
            $this->badSignal($trans);
        }
        $this->account = $order->getAccount();
        if (!$this->account->loaded()) {
            $this->badSignal($trans);
        }

        return $this->account->fail();
    }

    /** exit */
    protected function badSignal($trans)
    {
        $this->logger->error('Bad sign: ' . Json::encode($trans), 'SberbankHandler');
        exit();
    }

    /** exit */
    protected function ok($trans)
    {
        $this->logger->info('Ok ' . Json::encode($trans), 'SberbankHandler');
    }

    /**
     * @param string $method
     * @param array $data
     * @return mixed
     */
    protected function gateway($method, $data)
    {
        $curl = new ClientCurl($this->getServerUrl() . $method);
        $response = $curl
            ->setOption(CURLOPT_SSLVERSION, 6)
            ->setFollow()
            ->setPosts($data)
            ->exec();
        return $response;
    }

    public function result($trans = array())
    {
        // TODO: Implement result() method.
    }
}