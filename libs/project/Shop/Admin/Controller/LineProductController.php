<?php
namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\LineProduct;
use Shop\Commodity\Entity\LineProductItem;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Выборки товаров #admin_line_product?action=list
 */
class LineProductController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $this->response($this->view->load('shop/line/list'));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать
     * @Model(name=Shop\Commodity\Entity\LineProduct)
     */
    public function editAction(LineProduct $model)
    {
        $var['line'] = $model->as_array();
        $this->response($this->view->load('shop/line/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $line = new LineProduct($post['line'][LineProduct::model()->primary_key()]);
            $line->values($post['line']);
            $line->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'line' => $line->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\LineProduct)
     */
    public function deleteDataAction(LineProduct $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function loadDataAction()
    {
        $lines = LineProduct::model()->sort()->find_all();
        $result['lines'] = Arrays::resultAsArray($lines);
        $this->response($result);
    }

    /**
     * @Post
     */
    public function addProductDataAction()
    {
        $post = $this->httpRequest->getPost();
        $line = new LineProduct($post['line_id']);
        if ($line->loaded()) {
            $line->addProduct(array(
                'product_id' => $post['goods_id']
            ));
        }
        $this->response(array('ok' => 1));
    }

    /**
     * @Model(name=Shop\Commodity\Entity\LineProduct)
     */
    public function loadProductItemDataAction(LineProduct $model)
    {
        $items = LineProductItem::model()
            ->where('line_id', '=', $model->pk())
            ->select()
            ->sort()
            ->find_all();

        $ids = $result['items'] = array();
        foreach ($items as $item) {
            $ids[] = $item['product_id'];
            $result['items'][] = $item;
        }

        if (count($ids)) {
            $result['products'] = array();
            $products = Goods::model()->select('goods_id', 'name', 'article')->where('goods_id', 'in', $ids)->find_all();
            foreach ($products as $item) {
                $result['products'][] = $item;
            }
        }

        $this->response($result);
    }


    /**
     * @Post
     */
    public function saveItemDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $item = new LineProductItem($post['item'][LineProductItem::model()->primary_key()]);
            $item->values($post['item']);
            $item->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'item' => $item->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\LineProductItem)
     */
    public function deleteItemDataAction(LineProductItem $model)
    {
        $model->delete(true);
        $this->response(array('ok' => 1));
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $line = new LineProduct($post['id']);
        if ($line->loaded()) {
            $line->status = (int)$post['status'];
            $line->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }
}