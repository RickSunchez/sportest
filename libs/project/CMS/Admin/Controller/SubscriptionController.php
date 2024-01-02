<?php
namespace CMS\Admin\Controller;

use CMS\Mail\Entity\Subscription;
use CMS\Mail\Entity\SubscriptionBid;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Подписки #admin_subscription?action=list
 */
class SubscriptionController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $subs = new Subscription();
        $subs->order_by('date_edit', 'DESC')->order_by('group_id');
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($subs)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->setRoute('admin_subscription');
        $arr = $pagination->result();
        foreach ($arr as $item) {
            $var['subs'][] = $item->as_array();
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $this->response($this->view->load('cms/subscription/list', $var));
    }

    /**
     * @AddTitle Добивить
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/subscription/edit'));
    }

    /**
     * @AddTitle Редактировать
     * @Model(name=CMS\Mail\Entity\Subscription)
     */
    public function editAction(Subscription $model)
    {
        $var['sub']= $model->as_array();
        $var['config'] = $model->getConfig();
        $this->response($this->view->load('cms/subscription/edit', $var));
    }

    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $sub = new Subscription();
            $sub->values($post['sub'], array('type', 'name', 'is_name', 'is_phone', 'is_email', 'is_comment'));
            $sub->setConfig((array)$post['config']);
            $sub->count = 0;
            $sub->save(true);
            $result['ok'] = _t('CMS:Admin','These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function editDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $sub = new Subscription((int)$post['sub']['group_id']);
            $sub->values($post['sub'], array('name', 'is_name', 'is_phone', 'is_email', 'is_comment'));
            $sub->setConfig($post['config']);
            $sub->count = 0;
            $sub->save(true);
            $result['ok'] =_t('CMS:Admin','These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function deleteAction(){
        $id = (int)$this->httpRequest->getQuery('id');
        $subs = new Subscription($id);
        if($subs->loaded()){
            $subs->delete(true);
        }
        $this->httpResponse->redirect(link_to('admin_subscription',array('action'=>'list')));
    }

    public function bidAction($id,$page){
        $bid = new SubscriptionBid();

        $get = $this->httpRequest->getQuery();
        if(isset($get['sort'])){
            $bid->order_created('DESC');
            unset($get['sort']);
        }
        else{
            $bid->order_created();
            $get['sort'] = 'date';
        }
        $pagination = PaginationBuilder::factory($bid)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->addQueries(array('action'=>'bid'))
            ->setRoute('admin_subscription');
        $var['pagination'] = $pagination;
        $var['bid'] = $pagination->result();
        $var['get'] = $get;
        $this->response($this->view->load('cms/subscription/bid',$var));
    }



}