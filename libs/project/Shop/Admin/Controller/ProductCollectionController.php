<?php

namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\CollectionProduct;
use Shop\Commodity\Entity\CollectionProductItem;
use Shop\Commodity\Entity\Goods;

/**
 * @Template(name=admin)
 * @Admin
 */
class ProductCollectionController extends Controller
{
    /**
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @AddTitle Список
     */
    public function listAction($page, $type_id = Category::TYPE_GOODS)
    {
        $type_id = $this->httpRequest->getRequest('type_id', Category::TYPE_GOODS);
        $this->breadCrumbs->addLink('Группы товаров', 'admin_product_collection?action=list&type_id=' . $type_id);

        $collections = CollectionProduct::model()->order_pk('desc');
        $get = $this->httpRequest->getQuery();

        if ($get['name']) {
            $collections->where('name', 'LIKE', '%' . $get['name'] . '%');
        }

        $pagination = PaginationBuilder::factory($collections)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'));

        $ids = $var['collections'] = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $var['collections'][] = $item->as_array();
            $ids[] = $item->pk();
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['type_id'] = $type_id;
        $this->response($this->view->load('shop/goods/collection/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать
     * @Model(name=Shop\Commodity\Entity\CollectionProduct)
     */
    public function editAction(CollectionProduct $model)
    {
        $this->breadCrumbs->addLink('Группы товаров', 'admin_product_collection?action=list&type_id=' . $model->type_id);

        $var['collection'] = $model->as_array();
        $items = CollectionProductItem::model()->sort()->where('coll_id', '=', $model->pk())->find_all();
        $ids = $goodsIds = array();
        foreach ($items as $item) {
            $var['items'][] = $item->as_array();
            $ids[] = $item->pk();
            $goodsIds[] = $item->product_id;
        }

        if (count($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(CollectionProductItem::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArray($images);
        }

        if (count($goodsIds)) {
            $goods = Goods::model()
                ->select('goods_id', 'name')
                ->where('goods_id', 'in', $goodsIds)->find_all();
            $var['goods'] = Arrays::resultAsArray($goods, false);
        }

        $this->response($this->view->load('shop/goods/collection/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $collection = new CollectionProduct($post['collection'][CollectionProduct::model()->primary_key()]);
            $collection->values($post['collection']);
            $collection->save(true);
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'collection' => $collection->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\CollectionProduct)
     */
    public function deleteDataAction(CollectionProduct $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function saveItemDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $collection = new CollectionProduct($post['collection'][CollectionProduct::model()->primary_key()]);
            $id = $collection->addItem($post['item']);
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'item' => CollectionProductItem::model($id)->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\CollectionProductItem)
     */
    public function deleteItemDataAction(CollectionProductItem $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }
}