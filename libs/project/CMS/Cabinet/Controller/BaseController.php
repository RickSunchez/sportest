<?php
namespace CMS\Cabinet\Controller;

use Delorius\Application\UI\Controller;

class BaseController extends Controller
{

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var array
     */
    protected $config = array();

    public function before()
    {
        $this->breadCrumbs->addLink(_t('CMS:Cabinet', 'Private office'), 'cabinet');
        $this->config = $this->container->getParameters('cms');
        if ($this->config['cabinet']['layout'])
            $this->layout($this->config['cabinet']['layout']);
        if ($this->config['cabinet']['template'])
            $this->template($this->config['cabinet']['template']);
    }

}