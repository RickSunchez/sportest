<?php
namespace Shop\Payment\Handler;

use Delorius\Core\Environment;
use Delorius\Exception\BadRequest;
use Delorius\Exception\Error;
use Delorius\Forms\Form;
use Delorius\Utils\Json;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Shop\Payment\Entity\Account;

class YandexHandler extends HandlerPayment
{
    const SERVER_URL = 'https://money.yandex.ru/quickpay/confirm.xml';
    /** @var  string Названия платежа (max=50) */
    protected $formcomment;
    /** @var  string Номер кошелька */
    protected $receiver;
    /** @var  string (GET|POST) */
    protected $method = 'POST';
    /** оплата со счета Яндекс.Денег  */
    protected $payment_type = 'PC';
    /** @var  string */
    protected $secret;


    protected function init()
    {
        $config = Environment::getContext()->getParameters('shop.payment.yandex');
        $this->formcomment = $config['formcomment'];
        $this->receiver = $config['receiver'];
        $this->secret = $config['secret'];
    }

    /**
     * Get Url server merchant
     * @return string
     */
    protected function getServerUrl()
    {
        return self::SERVER_URL;
    }

    /**
     * @return string
     */
    protected function getPrice()
    {
        return number_format($this->account->getPrice(null, false), 2, '.', '');
    }

    /**
     * Return payment type (PC|AC)
     * @return string
     */
    protected function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @return string|Html
     */
    public function render()
    {

        $form = new Form('yandex');
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = NULL;
        $renderer->wrappers['label']['container'] = NULL;
        $renderer->wrappers['control']['container'] = NULL;
        $form->setAction($this->getServerUrl());
        $form->setMethod($this->method);
        $form->addHidden('receiver')->setValue($this->receiver);
        $form->addHidden('formcomment')->setValue($this->formcomment);
        $form->addHidden('short-dest')->setValue($this->formcomment)->no_separator();
        $form->addHidden('targets')->setValue($this->account->desc);
        $form->addHidden('quickpay-form')->setValue('shop')->no_separator();
        $form->addHidden('paymentType', $this->getPaymentType());
        $form->addHidden('sum')->setValue($this->getPrice());
        $form->addHidden('label')->setValue($this->account->pk());
        /****** config *****/
        $form->addHidden('need-fio')->setValue(false)->no_separator();
        $form->addHidden('need-email')->setValue(false)->no_separator();
        $form->addHidden('need-phone')->setValue(false)->no_separator();
        $form->addHidden('need-address')->setValue(false)->no_separator();

        $form->addSubmit('payment', 'Перевести');

        return $form;
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function result($trans = array())
    {
        if (!$this->load($trans) || !$this->is_crc($trans)) {
            $this->badSignal($trans);
        }

        try {
            $this->account->paid();
            $this->ok($trans);
        } catch (Error $e) {
            $this->logger->error($e->getMessage(), 'YandexHandler');
            $this->badSignal($trans);
        }

    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function success($trans = array())
    {
        return '';
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function fail($trans = array())
    {
        return '';
    }

    /** exit */
    protected function badSignal($trans)
    {
        $this->logger->error('Bad sign: ' . Json::encode($trans), 'YandexHandler');
        throw new BadRequest();
        exit();
    }

    /** exit */
    protected function ok($trans)
    {
        $this->logger->info('Ok ' . Json::encode($trans), 'YandexHandler');
        exit();
    }

    protected function is_crc($trans)
    {
        $sha1  = hash("sha1",_sf('{0}&{1}&{2}&{3}&{4}&{5}&{6}&{7}&{8}',
            $trans['notification_type'],
            $trans['operation_id'],
            $trans['amount'],
            $trans['currency'],
            $trans['datetime'],
            $trans['sender'],
            $trans['codepro'],
            $this->secret,
            $trans['label']
        ));

        if ($sha1 == $trans['sha1_hash']) {
            return true;
        } else {
            $this->logger->error('sha1_hash не сошлись: ' . Json::encode($trans), 'YandexHandler');
            return false;
        }
    }

    /**
     * @param array $trans
     * @return bool
     */
    protected function load(array $trans)
    {
        $this->account = new Account($trans['label']);
        return $this->account->loaded();
    }
}