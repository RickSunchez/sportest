<?php

namespace Boat\Admin\Controller;
use CMS\Core\Entity\Image;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;
use Shop\Store\Entity\Currency;

/**
 * @Template(name=admin)
 * @Admin
 */
class GoodsController extends \Shop\Admin\Controller\GoodsController
{


    /**
     * @AddTitle Список
     * @Get
     */
    public function listAction($page, $moder, $type_id = Category::TYPE_GOODS)
    {
        $get = $this->httpRequest->getQuery();
        $var['type_id'] = $get['type_id'] = $type_id;
        $goods = Goods::model()->ctype($type_id)->sortByPopular();
        if ($this->category->loaded()) {
            $idsCat = array();
            $idsCat[] = $this->category->pk();
            foreach ($this->category->getChildren() as $cat) {
                $idsCat[] = $cat['cid'];
            }
            $goods->whereCatId($idsCat);
        }

        if ($get['cid'] == '-1') {
            $goods->where('cid', '=', 0);
        }

        if (isset($get['name'])) {
            $goods->where($goods->table_name() . '.name', 'like', '%' . $get['name'] . '%');
        }
        if (isset($get['article'])) {
            $goods->where($goods->table_name() . '.article', 'like', '%' . $get['article'] . '%');
        }

        if ($moder == 1) {
            $goods->moder();
        }

        if (isset($get['status'])) {
            $goods->active($get['status']);
        }

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_goods');

        $arr = $pagination->result();
        $ids = array();
        foreach ($arr as $item) {
            $ids[] = $item->pk();
            $var['goods'][] = $item->as_array();
        }

        if (sizeof($ids)) {

            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Goods::model())
                ->where('main', '=', 1);
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $currency = Currency::model()->order_pk()->find_all();
        $var['currency'] = Arrays::resultAsArray($currency);
        $var['goods_types'] = Arrays::dataKeyValue(Category::getTypes());
        $this->response($this->view->load('boat/shop/goods/list', $var));
    }

}