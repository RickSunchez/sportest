<?php
namespace Shop\Payment\Controller;

use Delorius\Application\UI\Controller;
use Shop\Payment\Handler\RobokassaHandler;

class RobokassaController extends Controller
{
    /** @var  \Shop\Payment\Handler\RobokassaHandler */
    protected $handler;

    protected $config = array();

    public function before()
    {
        $this->httpResponse->setHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->getHeader()->setMetaTag('robots', 'noindex');

        $this->config = $this->container->getParameters('shop.payment');
        $this->layout($this->config['layout']);
        $this->handler = new RobokassaHandler();
    }

    protected function getTrans()
    {
        return $this->httpRequest->getRequest();
    }


    public function resultAction()
    {
        $this->layout(null);
        $this->handler->result($this->getTrans());
    }

    public function successAction()
    {
        $var['success'] = $this->handler->success($this->getTrans());
        $this->response($this->view->load('shop/payment/success', $var));
    }

    public function failAction()
    {
        $var['fail'] = $this->handler->fail($this->getTrans());
        $this->response($this->view->load('shop/payment/fail', $var));
    }

}