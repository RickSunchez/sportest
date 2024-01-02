<?php

namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;
use Delorius\Exception\Error;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use CMS\Catalog\Entity\Category;
use Delorius\Utils\Json;

/**
 * @Template(name=admin)
 * @Admin
 */
class CategoryController extends Controller
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

    /**
     * @SetTitle Категории #admin_cms_category?action=list
     * @AddTitle Список
     */
    public function listAction($type_id = Category::TYPE_NEWS)
    {
        $var['categories'] = $this->getCategories($type_id);

        $ids = array();
        foreach ($var['categories'] as $cat) {
            $ids[] = $cat['cid'];
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Category::model());
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }


        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $var['config'] = Common::getConfig('CMS:Admin');
        $var['type_id'] = $type_id;
        $this->response($this->view->load('cms/category/list', $var));
    }

    /**
     * @SetTitle Категории #admin_cms_category?action=list
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить категорию
     */
    public function addAction($type, $id)
    {
        $var = array();
        if ($id) {
            $category = new Category($id);
            if ($category->loaded()) {
                $type = $category->type_id;
            }
        }
        $type = $type ? $type : Category::TYPE_NEWS;
        $var['pid'] = (int)$id;
        $var['type_id'] = $type;
        $var['select'] = $this->selectCategories(0, $type);
        $this->response($this->view->load('cms/category/edit', $var));

    }

    /**
     * @SetTitle Категории #admin_cms_category?action=list
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать категорию
     * @Model(name=CMS\Catalog\Entity\Category)
     */
    public function editAction(Category $model)
    {
        $var = array();
        $var['category'] = $model->as_array();
        $var['pid'] = $model->pid;
        $var['type_id'] = $model->type_id;
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['select'] = $this->selectCategories($model->pk(), $model->type_id);
        $this->response($this->view->load('cms/category/edit', $var));

    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Массовое редактирования каталога
     * @Get
     */
    public function editIdsAction($ids, $type_id)
    {
        $this->breadCrumbs->addLink('Категории', 'admin_cms_category?action=list&type_id=' . $type_id);
        $var = array();
        $var['pid'] = 0;
        $var['ids'] = $ids;
        $var['type_id'] = $type_id;
        $var['select'] = $this->selectCategories(0, $type_id);
        $this->response($this->view->load('cms/category/edit_ids', $var));
    }

    /**
     * @Post
     */
    public function categoriesDataAction()
    {
        $type_id = $this->httpRequest->getPost('type_id', Category::TYPE_NEWS);
        $result['categories'] = $this->getCategories($type_id);
        $this->response($result);
    }

    public function catsJsonPartial($pid, $typeId = Category::TYPE_NEWS, $placeholder = 'Без вложений')
    {
        $list = $this->selectCategories($pid, $typeId, $placeholder);
        $this->response(Json::encode($list));
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $category = new Category();
            $category->values($post['category']);
            $register = $this->register;
            $category->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Категория в CMS добавлена: id=[cid]',
                    $orm
                );
            };
            $category->save(true);

            if (count($post['meta'])) {
                $meta = $category->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => 'Готово',
                'id' => $category->pk()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $category = new Category($post['id']);
        try {
            if (!$category->loaded())
                throw new Error(_t('CMS:Admin', 'No such category'));

            if ($category->object) {
                throw new Error(_t('CMS:Admin', 'Category have object, it can not be removed'));
            }

            if ($category->children) {
                throw new Error(_t('CMS:Admin', 'Category have subcategories, it can not be removed'));
            }
            $register = $this->register;
            $category->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Категория удалена: [name]',
                    $orm
                );
            };

            $category->delete(true);
            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @throws \Delorius\Exception\NotFound
     */
    public function editDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $category = new Category($post['category']['cid']);
            if (!$category->loaded()) {
                throw new NotFound(_t('CMS:Admin', 'Not found {0} by id = {1}', 'Category', $post['category']['cid']));
            }
            $category->values($post['category']);

            $register = $this->register;
            $category->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Категория в CMS изменена: id=[cid]',
                    $orm
                );
            };
            $category->save(true);

            $meta = $category->getMeta();
            $meta->values($post['meta']);
            $meta->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'category' => $category->as_array()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);

    }

    /**
     * @Post
     */
    public function saveIdsDataAction()
    {
        $post = $this->httpRequest->getPost();
        $arrOrm = Category::model()->where('cid', 'IN', explode(',', $post['ids']))->find_all();
        foreach ($arrOrm as $category) {
            try {
                $category->values($post['category']);
                $category->save(true);

                #meta
                $meta = $category->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);

            } catch (OrmValidationError $e) {
                $result = array('errors' => $e->getErrorsMessage());
            }

        }

        if (!sizeof($result))
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified') . ' ID:' . $post['ids'],
            );

        $this->response($result);

    }

    /**
     * @Post
     */
    public function selectEditDataAction()
    {
        $post = $this->httpRequest->getPost();

        switch ($post['action']) {
            case 'delete':
                $orm = Category::model()->where('cid', 'IN', $post['ids'])->find_all();
                foreach ($orm as $item) {
                    $item->delete();
                }
                break;
            case 'active':
            case 'deactivate':
                $orm = Category::model()->where('cid', 'IN', $post['ids'])->find_all();
                foreach ($orm as $item) {
                    $item->status = ($post['action'] == 'active') ? 1 : 0;
                    $item->save();
                }
                break;
        }
        Category::model()->cache_delete();
        $this->response(array('ok' => 1));
    }


    /**
     * @Post
     */
    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $category = new Category($post['id']);
        if ($category->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $category->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $category->pos++;
                } else if ($post['type'] == 'down') {
                    $category->pos--;
                }
                $category->save(true);
                $result['ok'] = _t('CMS:Admin', 'Ready');

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $result['categories'] = $this->getCategories($category->type_id);
        $this->response($result);
    }


    public function uploadDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        $category_json = $this->httpRequest->getPost('category');
        $category_arr = json_decode($category_json, true);
        $category = new Category($category_arr['cid']);
        if ($category->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $category->setImage($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }

    /************************* select catalog *****************/
    private $lvl = 0;
    private $categories = array();
    private $selectId = 0;
    private $list = array();

    private function selectCategories($selectId, $typeId = Category::TYPE_NEWS, $placeholder = 'Без вложений', $seporator = '-')
    {
        $this->selectId = $selectId;
        $this->categories = $this->getCategories($typeId);
        $this->list[] = array(
            'value' => 0,
            'disabled' => false,
            'object' => 0,
            'name' => $placeholder,
            'lvl' => $this->lvl,
            'selected' => false,
        );
        foreach ($this->categories as $category) {
            if ($category['pid'] == 0) {
                $disabled = $this->selectId == $category['cid'] ? true : false;
                $this->list[] = array(
                    'seporator' => str_pad('', $this->lvl, $seporator),
                    'value' => $category['cid'],
                    'disabled' => $disabled,
                    'object' => $category['object'],
                    'name' => $category['name'],
                    'lvl' => $this->lvl,
                    'selected' => $this->selectId == $category['cid'] ? true : false
                );
                $this->getOptionCatalog($category['cid'], $disabled, $seporator);
            }
        }
        return $this->list;
    }

    private function getOptionCatalog($parentId, $disabled = false, $seporator = '-')
    {
        ++$this->lvl;
        $categories = $this->getCategoryByParentId($parentId);
        if (sizeof($categories)) {
            foreach ($categories as $category) {
                $disabled = (($this->selectId == $category['cid']) || $disabled) ? true : false;
                $this->list[] = array(
                    'seporator' => str_pad('', $this->lvl, $seporator),
                    'value' => $category['cid'],
                    'disabled' => $disabled,
                    'object' => $category['object'],
                    'name' => $category['name'],
                    'lvl' => $this->lvl,
                    'selected' => $this->selectId == $category['cid'] ? true : false
                );
                $this->getOptionCatalog($category['cid'], $disabled, $seporator);
            }
        }
        --$this->lvl;
    }

    private function getCategoryByParentId($parentId)
    {
        $arr = array();
        foreach ($this->categories as $category) {
            if ($category['pid'] == $parentId) {
                $arr[] = $category;
            }
        }
        return $arr;
    }
    /*************************end select catalog *****************/

    /**
     * @param $typeId
     * @return array
     */
    private function getCategories($typeId)
    {
        $list = Category::model()
            ->sort()
            ->select()
            ->cached()
            ->type($typeId)
            ->find_all();
        $arr = array();
        foreach ($list as $item) {
            $arr[] = $item;
        }
        return $arr;
    }

}