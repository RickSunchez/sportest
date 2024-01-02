<?php
namespace Shop\Payment\Controller;

use Delorius\Application\UI\Controller;
use Shop\Payment\Handler\YandexHandler;

class YandexController extends Controller
{
    /** @var  \Shop\Payment\Handler\YandexHandler */
    protected $handler;

    protected $config = array();

    public function before()
    {
        $this->httpResponse->setHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->getHeader()->setMetaTag('robots', 'noindex');

        $this->config = $this->container->getParameters('shop.payment');
        $this->layout($this->config['layout']);
        $this->handler = new YandexHandler();
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

}