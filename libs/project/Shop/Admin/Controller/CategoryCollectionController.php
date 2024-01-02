<?php

namespace Shop\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\Collection;
use Shop\Catalog\Entity\Filter;
use Shop\Commodity\Entity\Characteristics;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Unit;

/**
 * @Template(name=admin)
 * @Admin
 */
class CategoryCollectionController extends Controller
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
     * @AddTitle Подборки
     */
    public function listAction($cid)
    {
        $var['cid'] = $cid;
        $category = Category::model($cid);
        $this->setBreadCrumbs($category);
        $this->response($this->view->load('shop/category/collection/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить подборку
     */
    public function addAction($cid)
    {
        $var = array();
        $var['cid'] = $cid;
        $category = Category::model($cid);
        $this->setBreadCrumbs($category);

        //filters category
        $var['filter_types'] = Arrays::dataKeyValue(Filter::getTypes());
        $var['filter_goods_params'] = Arrays::dataKeyValue(Filter::getGoodsParams());
        $var['chara'] = $this->getChara();
        $var['chara_values'] = $this->getCharaValue();
        $this->site->isParser = false;
        $this->response($this->view->load('shop/category/collection/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать подборгу
     * @Model(name=Shop\Catalog\Entity\Collection)
     */
    public function editAction(Collection $model)
    {
        $var = array();
        $var['cid'] = $model->cid;
        $category = Category::model($model->cid);
        $this->setBreadCrumbs($category);


        $var['collection'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();

        //filters category
        $var['filters'] = Arrays::resultAsArray($model->getFilters());
        $var['filter_types'] = Arrays::dataKeyValue(Filter::getTypes());
        $var['filter_goods_params'] = Arrays::dataKeyValue(Filter::getGoodsParams());
        $var['chara_goods'] = Arrays::resultAsArray($model->getValueCharacteristics());
        $var['chara'] = $this->getChara();
        $var['chara_values'] = $this->getCharaValue();
        $this->site->isParser = false;
        $this->response($this->view->load('shop/category/collection/edit', $var));

    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $collection = new Collection($post['collection'][Collection::model()->primary_key()]);
            $collection->values($post['collection']);

            $register = $this->register;
            $collection->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Подборка в магазине изменена: id=[id]',
                    $orm
                );
            };

            $collection->save(true);

            if (count($post['meta'])) {
                $meta = $collection->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            #filters
            if (count($post['filters']))
                foreach ($post['filters'] as $filter) {
                    $collection->addFilter($filter);
                }

            #chara_goods
            if (count($post['chara_goods']))
                foreach ($post['chara_goods'] as $chara) {
                    $collection->addCharacteristics($chara);
                }

            $result = array(
                'ok' => 'Готово',
                'id' => $collection->pk()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);
    }


    /**
     * @Post
     */
    public function collectionsDataAction()
    {
        $cid = $this->httpRequest->getPost('cid', 0);
        $result['collections'] = $this->getCollections($cid);

        $ids = array();
        foreach ($result['collections'] as $item) {
            $ids[] = $item['id'];
        }

        if (count($ids)) {
            $images = Image::model()
                ->select('preview', 'target_id')
                ->whereByTargetId($ids)
                ->whereByTargetType(Collection::model())
                ->find_all();
            $result['images'] = Arrays::resultAsArray($images, false);
        }

        $this->response($result);
    }


    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $collection = new Collection($post['id']);
        try {
            if (!$collection->loaded())
                throw new Error(_t('Shop:Admin', 'No such category'));


            $register = $this->register;
            $collection->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Подборка в магазине удалена: [name]',
                    $orm
                );
            };

            $collection->delete(true);
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
        $collection = new Collection($post['id']);
        if ($collection->loaded()) {
            $collection->status = (int)$post['status'];
            $collection->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function typeDataAction()
    {
        $post = $this->httpRequest->getPost();
        $collection = new Collection($post['id']);
        if ($collection->loaded()) {
            $collection->type = (int)$post['type'];
            $collection->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


    /**
     * @Post
     */
    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $collection = new Collection($post['id']);
        if ($collection->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $collection->pos = (int)$post['pos'];
                }
                $collection->save(true);
                $result['ok'] = _t('CMS:Admin', 'Ready');

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    /**
     * @param $cid
     * @return array
     * @throws Error
     */
    private function getCollections($cid)
    {
        $list = Collection::model()
            ->sort()
            ->select('id', 'cid', 'type', 'name', 'status', 'cid', 'pos')
            ->cached()
            ->where('cid', '=', $cid)
            ->find_all();

        $arr = array();
        foreach ($list as $item) {
            $arr[] = $item;
        }
        return $arr;
    }

    private function getChara()
    {
        $chara = Characteristics::model()->select()->cached()->sort()->find_all();
        $result = array();
        foreach ($chara as $item) {
            $item['id'] = $item['character_id'];
            $result[] = $item;
        }
        return $result;
    }


    private function getCharaValue()
    {
        $units = Arrays::resultAsArrayKey(
            Unit::model()
                ->select('abbr', 'unit_id')
                ->cached()
                ->find_all(),
            'unit_id');
        $value = CharacteristicsValues::model()
            ->select('value_id', 'character_id', 'name', 'unit_id')
            ->cached()
            ->sort()
            ->find_all();
        $result = array();
        foreach ($value as $item) {
            $item['unit'] = isset($units[$item['unit_id']]) ? $units[$item['unit_id']]['abbr'] : '';
            $result[$item['character_id']][] = $item;
        }
        return $result;
    }

}