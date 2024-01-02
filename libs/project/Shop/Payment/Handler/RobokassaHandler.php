<?php
namespace Shop\Payment\Handler;

use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Forms\Form;
use Delorius\Utils\Json;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Shop\Payment\Entity\Account;

class RobokassaHandler extends HandlerPayment
{

    const SERVER_TEST = 'http://test.robokassa.ru/Index.aspx';
    const SERVER_PRODUCTION = 'https://merchant.roboxchange.com/Index.aspx';

    /** @var  string */
    protected $login;
    /** @var  string */
    protected $pass1;
    /** @var  string */
    protected $pass2;
    /** @var  bool */
    protected $test;
    /** @var  string (GET|POST) */
    protected $method;

    protected function init()
    {
        $config = Environment::getContext()->getParameters('shop.payment.robokassa');
        $this->login = $config['login'];
        $this->pass1 = $config['pass1'];
        $this->pass2 = $config['pass2'];
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

    protected function getPrice()
    {
        return number_format($this->account->getPrice(null, false), 2, '.', '');
    }

    protected function crc()
    {
        return md5(_sf(
            '{0}:{1}:{2}:{3}',
            $this->login,
            $this->getPrice(),
            $this->account->pk(),
            $this->pass1
        ));
    }

    protected function crc_result($trans, $pass = true)
    {
        return md5(_sf(
            '{0}:{1}:{2}',
            $trans['OutSum'],
            $trans['InvId'],
            $pass ? $this->pass2 : $this->pass1
        ));
    }

    /**
     * @return string|Html
     */
    public function render()
    {
        $form = new Form('robokassa');
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = 'dl';
        $renderer->wrappers['pair']['container'] = NULL;
        $renderer->wrappers['label']['container'] = NULL;
        $renderer->wrappers['control']['container'] = NULL;
        $form->setAction($this->getServerUrl());
        $form->setMethod($this->method);
        $form->addHidden('MrchLogin')->setValue($this->login);
        $form->addHidden('OutSum')->setValue($this->getPrice());
        $form->addHidden('InvId')->setValue($this->account->pk());
        $form->addHidden('Desc')->setValue($this->account->desc);
        $form->addHidden('SignatureValue')->setValue($this->crc());
        $form->addHidden('IncCurrLabel');
        $form->addHidden('Culture')->setValue('ru');
        $form->addSubmit('payment', 'Оплатить');

        return $form;
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function result($trans = array())
    {
        if (!$this->load($trans)) {
            $this->badSignal($trans);
        }

        if (Strings::upper($trans['SignatureValue']) != Strings::upper($this->crc_result($trans))) {
            $this->badSignal($trans);
        }

        try {
            $this->account->paid();
            $this->ok($trans);
        } catch (Error $e) {
            $this->logger->error($e->getMessage(), 'RobokassaHandler');
            $this->badSignal($trans);
        }

    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function success($trans = array())
    {
        $httpResponse = Environment::getContext()->getService('httpResponse');

        if (count($trans)) {

            if (!$this->load($trans)) {
                $this->logger->error('error ' . var_export($trans, true), 'RobokassaHandler');
                $httpResponse->redirect(link_to('payment_robokassa', array('action' => HandlerPayment::URL_FAIL)));
                exit;
            }

            if (
                Strings::upper($trans['SignatureValue']) !=
                Strings::upper($this->crc_result($trans, false))
            ) {
                $this->logger->error('crc' . Strings::upper($trans['SignatureValue']) . ' = ' . Strings::upper($this->crc_result($trans, false)), 'RobokassaHandler');
                $httpResponse->redirect(link_to('payment_robokassa', array('action' => HandlerPayment::URL_FAIL)));
                exit;
            }

            return $this->account->success();
        }

        return '';
    }

    /**
     * @param array $trans
     * @return mixed
     */
    public function fail($trans = array())
    {
        if (count($trans) && $this->load($trans)) {
            return $this->account->fail();
        }

        return '';
    }

    /** exit */
    protected function badSignal($trans)
    {
        $this->logger->error('Bad sign: ' . Json::encode($trans), 'RobokassaHandler');
        echo "bad sign\n";
        exit();
    }

    /** exit */
    protected function ok($trans)
    {
        $this->logger->info('Ok ' . Json::encode($trans), 'RobokassaHandler');
        echo "OK" . $this->account->pk() . "\n";
        exit();
    }

    /**
     * @param array $trans
     * @return bool
     */
    protected function load(array $trans)
    {
        $this->account = new Account($trans['InvId']);
        return $this->account->loaded();
    }
}