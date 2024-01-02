<?php namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Shop\Components\Import1C\Import1C;

class Import1CController extends Controller
{
    protected $import1C;

    public function __construct()
    {
        $this->import1C = new Import1C();
    }

    public function indexAction()
    {
        $data = $this->import1C->getImportStatus();

        $this->response($this->view->load('shop/1c/index', $data));
    }
}
