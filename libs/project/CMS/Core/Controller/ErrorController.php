<?php
namespace CMS\Core\Controller;

use Delorius\Application\UI\Controller;

class ErrorController extends Controller
{
    /**
     * @var array
     */
    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('cms.error');
        if ($this->config['layout'])
            $this->layout($this->config['layout']);
    }

    /**
     * @AddTitle Error 404
     */
    public function e400Action()
    {
        $this->response($this->view->load('cms/page/404'));
    }

    /**
     * @AddTitle Error 404
     */
    public function e410Action()
    {
        $this->response($this->view->load('cms/page/404'));
    }

    /**
     * @AddTitle Error 404
     */
    public function e404Action()
    {
        $this->response($this->view->load('cms/page/404'));
    }

    /**
     * @AddTitle Error 500
     */
    public function e500Action()
    {
        $this->response($this->view->load('cms/page/500'));
    }

    /**
     * @AddTitle Error 403
     */
    public function e403Action()
    {
        $this->response($this->view->load('cms/page/403'));
    }

}

