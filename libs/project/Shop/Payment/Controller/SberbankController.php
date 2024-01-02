<?php
namespace Shop\Payment\Controller;

use Delorius\Application\UI\Controller;
use Delorius\View\Html;
use Shop\Payment\Handler\SberbankHandler;

/**
 * @AddTitle Оптала
 */
class SberbankController extends Controller
{
    /** @var  \Shop\Payment\Handler\SberbankHandler */
    protected $handler;

    protected $config = array();

    public function before()
    {
        $this->httpResponse->setHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->getHeader()->setMetaTag('robots', 'noindex');

        $this->config = $this->container->getParameters('shop.payment');
        $this->layout($this->config['layout']);
        $this->handler = new SberbankHandler();
    }

    protected function getTrans()
    {
        return $this->httpRequest->getRequest();
    }

    public function paymentAction()
    {
        $this->layout(null);
        $this->handler->register($this->getTrans());
    }

    public function successAction()
    {
        $var['success'] = $this->handler->success($this->getTrans());
        $this->response($this->view->load('shop/payment/success', $var));
    }


    public function failAction($msg)
    {
        if ($msg) {
            $var['fail'] = Html::clearTags($msg);
        } else {
            $var['fail'] = $this->handler->fail($this->getTrans());
        }
        $this->response($this->view->load('shop/payment/fail', $var));
    }

}