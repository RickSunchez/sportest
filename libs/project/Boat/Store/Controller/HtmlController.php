<?php
namespace Boat\Store\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\Collection;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Helpers\Options;

class HtmlController extends Controller
{

    public function collectionPartial($categoryId)
    {
        $collections = Collection::model()
            ->select('name', 'url', 'id')
            ->where('cid', '=', $categoryId)
            ->active()
            ->where('type_id', '=', Category::TYPE_GOODS)
            ->sort()
            ->find_all();
        $ids = $var['collections'] = array();
        foreach ($collections as $item) {
            $var['collections'][] = $item;
            $ids[] = $item['id'];
        }


        if (count($ids)) {
            $images = Image::model()
                ->select('preview', 'target_id')
                ->whereByTargetId($ids)
                ->whereByTargetType(Collection::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $this->response($this->view->load('boat/shop/collection/_list', $var));
    }


    public function collectionProductPartial($categoryId)
    {

        $collections = Collection::model()
            ->select('name', 'url', 'id', 'header')
            ->where('cid', '=', $categoryId)
            ->where('type_id', '=', Category::TYPE_GOODS)
            ->sort()
            ->active()
            ->find_all();

        $var['collections'] = $collections;
        $this->response($this->view->load('boat/shop/collection/_list_slider', $var));
    }

    /**
     * @param null $limit
     * @param Category|null $category
     * @param bool $is_image
     */
    public function productsPartial($limit = null, Category $category = null, $theme = null, $is_image = true)
    {
        if (!$category) {
            return;
        }

        if ($category->goods <= $limit) {
            $var['count'] = 0;
        } else {
            $var['count'] = $category->goods - $limit;
        }

        $var['category'] = $category;


        $products = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->active()
            ->where('cid', '=', $category->pk())
            ->ctype(Category::TYPE_GOODS)
            ->sortByPopular();

        if ($limit) {
            $products->limit($limit);
        }

        $ids = $var['products'] = array();
        $result = $products->find_all();
        foreach ($result as $item) {
            $var['products'][] = $item;
            $ids[] = $item['goods_id'];
        }

        if (sizeof($ids)) {
            Options::acceptFirstVariantsByProducts($var['products'], $ids, $is_image);
        }
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('shop/category/_list_products' . $theme, $var));
    }


    public function menuTopPartial()
    {
        $categories = Category::model()
            ->sort()
            ->type(Category::TYPE_GOODS)
            ->active()
            ->select(array('cid', 'id'), 'url', 'pid', 'name','goods', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = $ids = array();
        foreach ($categories as $cat) {
            $cat['link'] = link_to_city('shop_category_list', array('url' => $cat['url'], 'cid' => $cat['id']));
            $var['categories'][$cat['pid']][] = $cat;
            $ids[] = $cat['id'];
        }

        if (count($ids)) {
            $images = Image::model()
                ->select('preview', 'target_id','image_id')
                ->whereByTargetId($ids)
                ->whereByTargetType(Category::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $this->response($this->view->load('html/menu_sub_top', $var));
    }

    public function menuMainPartial()
    {
        $categories = Category::model()
            ->type(Category::TYPE_GOODS)
            ->parent()
            ->active()
            ->select(array('cid', 'id'), 'url', 'pid', 'name','goods', 'children')
            ->cached()
            ->sort()
            ->find_all();

        $var['categories'] = $ids = array();
        foreach ($categories as $cat) {
            $cat['link'] = link_to_city('shop_category_list', array('url' => $cat['url'], 'cid' => $cat['id']));
            $var['categories'][] = $cat;
            $ids[] = $cat['id'];
        }

        if (count($ids)) {
            $images = Image::model()
                ->select('preview', 'target_id','image_id')
                ->whereByTargetId($ids)
                ->whereByTargetType(Category::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $this->response($this->view->load('html/menu_main', $var));
    }

}