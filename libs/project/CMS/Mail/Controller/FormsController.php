<?php
namespace CMS\Mail\Controller;


use CMS\Go\Model\GoCookieHelper;
use CMS\Mail\Entity\Subscription;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;
use Delorius\Exception\Error;
use Delorius\Exception\NotFound;

class FormsController extends Controller
{
    protected $config = array();
    public function before()
    {
        $this->config = Common::getConfig('CMS:Mail');
        if($this->config['layout']['forms'])
            $this->layout($this->config['layout']['forms']);
    }

    /**
     * @param $url
     * @throws \Delorius\Exception\NotFound
     * @Get
     */
    public function showAction($url)
    {
        $subscription = Subscription::model()
            ->where('hash','=',md5($url))
            ->find();
        if(!$subscription->loaded()){
            throw new NotFound('Not found Subscription with URL = '.$url);
        }
        $var['sub'] = $subscription;
        $var['config'] = $subscription->getConfig();
        $this->response($this->view->load('page/show', $var));
    }

    /**
     * @Post
     */
    public function sendAction()
    {
        $result = array();
        try {
            $post = $this->httpRequest->getPost();
            $subscription = new Subscription((int)$post['group_id']);
            if (!$subscription->loaded())
                throw new Error('Not found item');
            if (!$subscription->addBid($post))
                throw new Error(_t('CMS:Mail','Failed to add application'));
            $result['ok'] = _t('CMS:Mail','Application is accepted');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }


}