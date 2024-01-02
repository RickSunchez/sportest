<?php
namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Options\Inventory;
use Shop\Commodity\Entity\Options\Item;
use Shop\Commodity\Entity\Options\Variant;
use Shop\Commodity\Helpers\Options;

/**
 * @Template(name=admin)
 * @Admin
 */
class OptionController extends Controller
{

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @AddTitle Список опций
     * @Model(name=Shop\Commodity\Entity\Goods)
     */
    public function listAction(Goods $model)
    {
        $this->breadCrumbs->addLink('Товар: ' . $model->name, _sf('admin_goods?action=edit&id={0}', $model->pk()));
        $options = Item::model()
            ->byGoodsId($model->pk())
            ->sort()
            ->find_all();
        foreach ($options as $item) {
            $var['options'][] = $item->as_array();
        }
        $var['goods'] = $model->as_array();
        $this->response($this->view->load('shop/goods/option/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @Model(name=Shop\Commodity\Entity\Options\Item)
     * @AddTitle Редактирование опции
     */
    public function editAction(Item $model)
    {
        $goods = new Goods($model->goods_id);
        $this->breadCrumbs->addLink('Товар: ' . $goods->name, _sf('admin_goods?action=edit&id={0}', $goods->pk()));
        $var['goods'] = $goods->as_array();
        $var['option'] = $model->as_array();
        $var['types'] = Arrays::dataKeyValue(Options::getTypes($model->type));
        $var['variant_types'] = Variant::getTypes();

        $variants = Variant::model()->sort()->where('option_id', '=', $model->pk())->find_all();
        $ids = array();
        foreach ($variants as $variant) {
            $var['variants'][] = $variant->as_array();
            $ids[] = $variant->pk();
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Variant::model());
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }
        $this->response($this->view->load('shop/goods/option/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @Model(name=Shop\Commodity\Entity\Goods)
     * @AddTitle Добавить опцию
     */
    public function addAction(Goods $model)
    {
        $this->breadCrumbs->addLink('Товар: ' . $model->name, _sf('admin_goods?action=edit&id={0}', $model->pk()));
        $var['goods'] = $model->as_array();
        $var['types'] = Arrays::dataKeyValue(Options::getTypes());
        $this->response($this->view->load('shop/goods/option/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @Model(name=Shop\Commodity\Entity\Options\Variant)
     * @AddTitle Редактирование варианта
     */
    public function variantAction(Variant $model)
    {
        $option = new Item($model->option_id);
        $goods = new Goods($option->goods_id);
        $this->breadCrumbs->addLink('Товар: ' . $goods->name, _sf('admin_goods?action=edit&id={0}', $goods->pk()));
        $this->breadCrumbs->addLink('Опция: ' . $option->name, _sf('admin_option?action=edit&id={0}', $option->pk()));
        $var['goods'] = $goods->as_array();
        $var['option'] = $option->as_array();
        $var['variant'] = $model->as_array();
        $var['image'] = $model->getImage()->as_array();
        $this->response($this->view->load('shop/goods/option/edit_variant', $var));
    }

    /**
     * @Model(name=Shop\Commodity\Entity\Goods)
     * @AddTitle Комбинации опций
     */
    public function combinationAction(Goods $model, $page)
    {
        $this->breadCrumbs->addLink('Товар: ' . $model->name, _sf('admin_goods?action=edit&id={0}', $model->pk()));
        $var['goods'] = $model->as_array();
        $inventories = Inventory::model()
            ->sort()
            ->byGoodsId($model->pk());
        $get = $this->httpRequest->getQuery();
        $get['step'] = isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE;
        $pagination = PaginationBuilder::factory($inventories)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($get['step'])
            ->addQueries($get)
            ->addQueries(array('action' => 'combination', 'id' => $model->pk()))
            ->setRoute('admin_option');
        $var['pagination'] = $pagination;
        $result = $pagination->result();
        $ids = $optIds = $varIds = array();
        foreach ($result as $item) {
            $ids[] = $item->pk();
            $var['combinations'][] = $item->as_array();
            $arr = explode('_', $item->combination);
            foreach ($arr as $key => $value) {
                if ($key % 2) {
                    $varIds[$value] = $value;
                } else {
                    $optIds[$value] = $value;
                }
            }
        }

        if (count($optIds)) {
            $options = Item::model()
                ->select('id', 'name')
                ->where('id', 'in', $optIds)
                ->find_all();
            foreach ($options as $option) {
                $var['options'][$option['id']] = $option['name'];
            }
        }

        if (count($varIds)) {
            $variants = Variant::model()
                ->select('name', 'id')
                ->where('id', 'in', $varIds)
                ->find_all();
            foreach ($variants as $variant) {
                $var['variants'][$variant['id']] = $variant['name'];
            }
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Inventory::model());
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }

        $var['get'] = $get;
        $this->response($this->view->load('shop/goods/option/combination', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $option = new Item($post['option'][Item::model()->primary_key()]);
            $option->values($post['option']);
            $option->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'option' => $option->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveVariantDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $option = new Item($post['option'][Item::model()->primary_key()]);
            $id = $option->addVariant($post['variant']);
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'variant' => Variant::model($id)->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveCombinationDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $combination = new Inventory($post['combination'][Inventory::model()->primary_key()]);
            $combination->values($post['combination']);
            $combination->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'combination' => $combination->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Options\Item)
     */
    public function deleteDataAction(Item $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Options\Inventory)
     */
    public function deleteCombinationDataAction(Inventory $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Options\Variant)
     */
    public function deleteVariantDataAction(Variant $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function genCombinationsDataAction()
    {
        $id = $this->httpRequest->getRequest('id');
        $update = $this->httpRequest->getRequest('update', true);
        Options::genCombinationsByGoods($id, $update);
        $this->response(array('ok'));
    }
}