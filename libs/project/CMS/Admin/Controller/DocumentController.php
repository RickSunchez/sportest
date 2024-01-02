<?php
namespace CMS\Admin\Controller;

use CMS\Catalog\Entity\Category;
use CMS\Core\Entity\Document;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Документы #admin_doc?action=list
 *
 */
class DocumentController extends Controller
{
    /**
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @AddTitle Список
     */
    public function listAction($page,$cid)
    {
        $files = Document::model()->order_created('desc');
        if($cid){
            $category = new Category($cid);
            if($category->loaded()){
                $var['category'] = $category;
                $files->where('cid','=',$cid);
                $this->breadCrumbs->setLastItem('Категория: '.$category->name);
            }
        }

        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($files)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->setRoute('admin_doc');

        $arr = $pagination->result();
        foreach ($arr as $item) {
            $var['files'][] = $item->as_array();
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['multi'] =  Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load('cms/doc/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактирование файла
     * @Model(name=CMS\Core\Entity\Document)
     */
    public function editAction($model)
    {
        $var = array();
        $var['file'] = $model->as_array();
        $this->response($this->view->load('cms/doc/edit', $var));
    }

    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $file = new Document($post['file_id']);
            if ($file->loaded()) {
                $file->values($post);
                $file->save(true);
            }
            $result = array(
                'ok' => _t('CMS:Admin', 'Ready'),
                'id' => $file->pk()
            );
        } catch (OrmValidationError $e) {
            $result['error'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $file = new Document($post['file_id']);
            if ($file->loaded()) {
                $file->delete(true);
            }
            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (OrmValidationError $e) {
            $result['error'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


    public function uploadDataAction($file_id)
    {
        $cid = $this->httpRequest->getPost('cid');
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        try {
            $doc = new Document($file_id);
            if($cid){
                $doc->cid = $cid;
            }
            $file = $this->httpRequest->getFile('file');
            $res =  $doc->setFile($file);
            if ($res) {
                $result = $doc->as_array();
            }
        } catch (OrmValidationError $e) {
            $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        }
        $this->response($result);
    }
}