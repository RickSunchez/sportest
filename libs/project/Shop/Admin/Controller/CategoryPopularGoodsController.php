<?php

namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryPopularProduct;
use Shop\Commodity\Entity\Goods;


/**
 * @Template(name=admin)
 * @Admin
 */
class CategoryPopularGoodsController extends Controller
{

    /**
     * @Post
     */
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        $cat = new Category($post['cat_id']);
        if ($cat->loaded()) {
            $cat->addProduct(array(
                'product_id' => $post['goods_id']
            ));
        }
        $this->response(array('ok' => 1));
    }

    /**
     * @Model(name=Shop\Catalog\Entity\Category,field=cid)
     */
    public function loadDataAction(Category $model)
    {
        $items = CategoryPopularProduct::model()
            ->where('cat_id', '=', $model->pk())
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
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $item = new CategoryPopularProduct($post['item'][CategoryPopularProduct::model()->primary_key()]);
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
     * @Model(name=Shop\Catalog\Entity\CategoryPopularProduct)
     */
    public function deleteDataAction(CategoryPopularProduct $model)
    {
        $model->delete(true);
        $this->response(array('ok' => 1));
    }


}