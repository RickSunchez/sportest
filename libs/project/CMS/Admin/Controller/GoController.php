<?php
namespace CMS\Admin\Controller;

use CMS\Go\Entity\Go;
use CMS\Go\Entity\GoStat;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Cсылки #admin_go?action=list
 */
class GoController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {

        $go = Go::model()->order_by('visit', 'DESC');
        $get = $this->httpRequest->getQuery();
        if ($get['url']) {
            $go->where('url', 'like', '%' . $get['url'] . '%');
        }
        if ($get['redirect']) {
            $go->where('redirect', 'like', '%' . $get['redirect'] . '%');
        }
        $pagination = PaginationBuilder::factory($go)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(30)
            ->addQueries($get)
            ->setRoute('admin_go');

        $this->getHeader()->setPagination($pagination);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['go'] = array();
        foreach ($result as $item) {
            $var['go'][] = $item->as_array();
        }
        $this->response($this->view->load("cms/go/list", $var));
    }


    /**
     * @AddTitle Статистика
     */
    public function statAction()
    {
        $get = $this->httpRequest->getQuery();
        $goStats = new GoStat();
        $goStats->where('go_id', '=',$get['id'])->order_created('desc');
        $pagination = PaginationBuilder::factory($goStats)
            ->setItemCount(false)
            ->setPage((int)$get['page'])
            ->setItemsPerPage(50)
            ->addQueries($get+array('action'=>'stat'))
            ->setRoute('admin_go');

        $var['stats'] = $pagination->result();
        $var['pagination'] = $pagination;
        $this->response($this->view->load("cms/go/stat", $var));
    }


    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        try {
            $go = new Go();
            $go->values($post, array('comment', 'redirect'));
            $go->save(true);
            $result['ok'] = _t('CMS:Admin','These modified');
            $result['go'] = $go->as_array();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function clearDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        $go = new Go((int)$post['go_id']);
        if ($go->loaded()) {
            try {
                $go->visit = 0;
                $go->save(true);
                $go->clearStatistics();
                $result['ok'] = _t('CMS:Admin','These modified');
                $result['go'] = $go->as_array();
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = 'Нет такого объекта';
        }
        $this->response($result);
    }

    public function editDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        $go = new Go((int)$post['go_id']);
        if ($go->loaded()) {
            try {
                $go->values($post, array('comment', 'redirect'));
                $go->save(true);
                $result['ok'] = _t('CMS:Admin','These modified');
                $result['go'] = $go->as_array();
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = 'Нет такого объекта';
        }
        $this->response($result);
    }

    public function deleteAction()
    {
        $id = (int)$this->httpRequest->getQuery('id');
        $go = new Go($id);
        if ($go->loaded()) {
            $go->delete(true);
        }
        $this->httpResponse->redirect(link_to('admin_go', array('action' => 'list')));
    }


}