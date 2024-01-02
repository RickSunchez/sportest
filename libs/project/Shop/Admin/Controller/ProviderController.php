<?php
namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Shop\Commodity\Entity\Provider;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Поставщики #admin_provider?action=list
 */
class ProviderController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $providers = Provider::model()->sort();
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($providers)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get);

        $var['providers'] =  array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $var['providers'][] = $item->as_array();            
        }
       
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $this->response($this->view->load('shop/goods/provider/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать
     * @Model(name=Shop\Commodity\Entity\Provider)
     */
    public function editAction(Provider $model)
    {
        $var['provider'] = $model->as_array();
        $this->response($this->view->load('shop/goods/provider/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $provider = new Provider($post['provider'][Provider::model()->primary_key()]);
            $provider->values($post['provider']);
            $provider->save(true);
            
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'provider' => $provider->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Provider)
     */
    public function deleteDataAction(Provider $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }
}