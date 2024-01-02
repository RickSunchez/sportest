<?php

namespace Shop\Admin\Controller;

use CMS\Core\Component\Register;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryFilter;

/**
 * @Template(name=admin)
 * @Admin
 */
class CategoryFilterController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var  Register
     * @inject
     */
    public $register;

    protected function setBreadCrumbs(Category $category)
    {
        if ($category->loaded()) {
            $this->breadCrumbs->addLink('Каталог', 'admin_category?action=list&type_id=' . $category->type_id);
            $this->breadCrumbs->addLink($category->name, 'admin_category?action=edit&id=' . $category->pk());
        } else {
            $this->breadCrumbs->addLink('Каталог', 'admin_category?action=list&type_id=' . Category::TYPE_GOODS);
        }

    }

    /**
     * @AddTitle Статические фильтры
     */
    public function listAction($cid)
    {
        $var['cid'] = $cid;
        $category = Category::model($cid);
        $this->setBreadCrumbs($category);
        $this->response($this->view->load('shop/category/filter/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить фильтр
     */
    public function addAction($cid)
    {
        $var = array();
        $var['cid'] = $cid;
        $category = Category::model($cid);
        $this->setBreadCrumbs($category);

        $this->response($this->view->load('shop/category/filter/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать фильтр
     * @Model(name=Shop\Catalog\Entity\CategoryFilter)
     */
    public function editAction(CategoryFilter $model)
    {
        $var = array();
        $var['cid'] = $model->cid;
        $category = Category::model($model->cid);
        $this->setBreadCrumbs($category);

        $var['filter'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();

        $this->response($this->view->load('shop/category/filter/edit', $var));

    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $filter = new CategoryFilter($post['filter'][CategoryFilter::model()->primary_key()]);
            $filter->values($post['filter']);

            $register = $this->register;
            $filter->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Статический фильтр изменен: id=[id]',
                    $orm
                );
            };

            $filter->save(true);

            if (count($post['meta'])) {
                $meta = $filter->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => 'Готово',
                'id' => $filter->pk()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);
    }


    /**
     * @Post
     */
    public function getDataAction()
    {
        $cid = $this->httpRequest->getPost('cid', 0);
        $result['filters'] = $this->getFilters($cid);
        $this->response($result);
    }


    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $filter = new CategoryFilter($post['id']);
        try {
            if (!$filter->loaded())
                throw new Error(_t('Shop:Admin', 'No such category'));


            $register = $this->register;
            $filter->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Статический фильтр удален: [name]',
                    $orm
                );
            };

            $filter->delete(true);
            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $filter = new CategoryFilter($post['id']);
        if ($filter->loaded()) {
            $filter->status = (int)$post['status'];
            $filter->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


    /**
     * @param $cid
     * @return array
     * @throws Error
     */
    private function getFilters($cid)
    {
        $list = CategoryFilter::model()
            ->select('id', 'header', 'url', 'name', 'status')
            ->cached()
            ->where('cid', '=', $cid)
            ->find_all();

        $arr = array();
        foreach ($list as $item) {
            $arr[] = $item;
        }
        return $arr;
    }


}