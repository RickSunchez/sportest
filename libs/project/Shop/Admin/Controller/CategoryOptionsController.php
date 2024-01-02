<?php

namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;


/**
 * @Template(name=admin)
 * @Admin
 */
class CategoryOptionsController extends Controller
{

    /**
     * @Post
     * @Model(name=Shop\Catalog\Entity\Category)
     */
    public function offGoodsDataAction(Category $model)
    {
        $res = $model->getChildren();
        $idsCat = array();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }

        $idsCat[] = $model->pk();

        $goods = Goods::model()->whereCatId($idsCat)->find_all();
        foreach ($goods as $item) {
            $item->status = 0;
            $item->save();
        }

        $msg = _sf('{0} шт. отключено', count($goods));

        $this->response(array('ok' => $msg));
    }


    /**
     * @Post
     * @Model(name=Shop\Catalog\Entity\Category)
     */
    public function offCatsGoodsDataAction(Category $model)
    {
        $res = $model->getChildren();
        $idsCat = array();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }

        $idsCat[] = $model->pk();

        $goods = Goods::model()->whereCatId($idsCat)->find_all();
        foreach ($goods as $item) {
            $item->status = 0;
            $item->save();
        }

        $categories = Category::model()->where('cid', 'in', $idsCat)->find_all();
        foreach ($categories as $item) {
            $item->status = 0;
            $item->save();
        }

        $msg = _sf('Отключено: товары {0} шт., категории {1} шт.', count($goods), count($idsCat));

        $this->response(array('ok' => $msg));
    }

    /**
     * @Post
     * @Model(name=Shop\Catalog\Entity\Category)
     */
    public function delGoodsDataAction(Category $model)
    {
        $res = $model->getChildren();
        $idsCat = array();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }

        $idsCat[] = $model->pk();

        $goods = Goods::model()->whereCatId($idsCat)->find_all();
        foreach ($goods as $item) {
            $item->delete();
        }

        $msg = _sf('{0} шт. удалено', count($goods));

        $this->response(array('ok' => $msg));
    }

    /**
     * @Post
     * @Model(name=Shop\Catalog\Entity\Category)
     */
    public function delCatsGoodsDataAction(Category $model)
    {
        $res = $model->getChildren();
        $idsCat = array();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }

        $idsCat[] = $model->pk();

        $goods = Goods::model()->whereCatId($idsCat)->find_all();
        foreach ($goods as $item) {
            $item->delete();
        }

        $categories = Category::model()->where('cid', 'in', $idsCat)->find_all();
        foreach ($categories as $item) {
            $item->delete();
        }

        $msg = _sf('Удалено: товары {0} шт., категории {1} шт.', count($goods), count($idsCat));

        $this->response(array('ok' => $msg));
    }
}