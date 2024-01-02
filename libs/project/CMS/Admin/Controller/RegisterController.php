<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Register;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 */
class RegisterController extends Controller
{

    /**
     * @var \CMS\Core\Component\Register
     * @service register
     * @inject
     */
    public $register;

    public function listAction($page)
    {
        $get = $this->httpRequest->getQuery();
        $registers = Register::model()->sort();
        $pagination = PaginationBuilder::factory($registers)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(30)
            ->addQueries($get)
            ->setRoute('admin');
        $var['pagination'] = $pagination;
        $var['registers'] = $pagination->result();

        $this->response($this->view->load('cms/register/list', $var));
    }

    public function clearAction(){
        ignore_user_abort(1);
        set_time_limit(0);
        $this->register->createArchive();
        $this->register->clear();
        $this->httpResponse->redirect(link_to('admin'));
        die;
    }


}