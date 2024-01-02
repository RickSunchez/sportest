<?php

namespace Shop\Catalog\Controller;

use CMS\Core\Entity\Image;
use Delorius\Exception\Error;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Http\Response;
use Delorius\Http\UrlScript;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryFilter;
use Shop\Catalog\Entity\CategoryPopularProduct;
use Shop\Catalog\Entity\Collection as CategoryCollection;
use Shop\Catalog\Helpers\Filter;
use Shop\Catalog\Helpers\Shop;
use Shop\Commodity\Entity\Collection;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\LineProduct;
use Shop\Commodity\Entity\LineProductItem;
use Shop\Commodity\Entity\TypeGoods;
use Shop\Commodity\Entity\Vendor;
use Shop\Commodity\Helpers\Options;

class ShopController extends CategoryController
{

    public function before()
    {
        if (!$this->container->getParameters('shop.shop.init')) {
            throw new ForbiddenAccess('Отключен магазин');
        }
        $this->config = $this->container->getParameters('shop.shop.type.' . $this->type_id);
        $this->router = $this->config['router'];
        $this->perPage = $this->config['page'];
        $this->setSite('goodsTypeId', $this->type_id);
    }

    public function indexAction()
    {
        if ($this->config['layout']['catalog'])
            $this->layout($this->config['layout']['catalog']);

        if ($this->config['first']['name']) {

            $this->breadCrumbs->setLastItem(
                $this->config['first']['name']
            );

            $this->setMeta(null, array(
                'title' => $this->config['first']['title'] ? $this->config['first']['title'] : $this->config['first']['name']
            ));

        }

        $categories = Category::model()
            ->sort()
            ->active()
            ->type($this->type_id)
            ->select('cid', 'url', 'pid', 'name', 'goods', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = array();
        foreach ($categories as $cat) {
            $var['categories'][$cat['pid']][] = $cat;
        }

        $images = Image::model()->select()->whereByTargetType(Category::model())->cached()->find_all();
        $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');

        $this->response($this->view->load($this->config['view']['category'] . '/shop_index', $var));
    }

    /**
     * @param null $limit
     * @param Category|null $category
     * @param null $theme
     * @param bool|true $is_image
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

        $res = $category->getChildren();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }
        $idsCat[] = $category->pk();


        $products = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->active()
            ->whereCatsId($idsCat)
            ->ctype($this->type_id)
            ->is_amount()
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
        $this->response($this->view->load($this->config['view']['category'] . '/_list_products' . $theme, $var));
    }

    /**
     * @param null $theme
     * @throws Error
     */
    public function linesPartial($theme = null)
    {
        $lines = LineProduct::model()->sort()->active()->find_all();
        $var['lines'] = $lines;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['category'] . '/_line_products' . $theme, $var));
    }

    /**
     * @param LineProduct $line
     * @param null $theme
     * @throws Error
     */
    public function linesItemsPartial(LineProduct $line, $theme = null)
    {

        $items = LineProductItem::model()
            ->where('line_id', '=', $line->pk())
            ->select()
            ->sort()
            ->find_all();

        $ids = $var['items'] = array();
        foreach ($items as $item) {
            $ids[] = $item['product_id'];
            $var['items'][] = $item;
        }

        if (count($ids)) {
            $goods = Goods::model()
                ->select_array($this->container->getParameters('product_select_list'))
                ->active()
                ->ctype($this->type_id)
                ->where('goods_id', 'in', $ids)
                ->find_all();

            foreach ($goods as $item) {
                $var['goods'][$item['goods_id']] = $item;
            }

            if (sizeof($ids)) {
                Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
            }
        }
        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['category'] . '/_line_product_items' . $theme, $var));
    }


    /**
     * @param int $cid
     * @param string $url
     * @param string|null $url_filter
     * @Model(name=Shop\Catalog\Entity\Category,field=cid)
     */
    public function listAction(Category $model, $url, $url_filter = null)
    {
        load_or_404($model);

        #corrections url
        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $this->getUrlModel($model),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $this->setSite('categoryId', $model->pk());
        $this->setSite('categoryLink', $this->getUrlModel($model));
        $this->setSite('category', $model);
        $this->setGUID($model->pk());

        $this->setBreadCrumbs();


        if (!$url_filter) {
            $this->setBreadCrumbsParents($model);
            $this->breadCrumbs->setLastItem($model->name);
            $meta = $model->getMeta();
        } else {
            $filter = CategoryFilter::model()
                ->where('url', '=', $url_filter)
                ->where('cid', '=', $model->pk())
                ->active()
                ->find();

            load_or_404($filter);

            $this->setSite('categoryFilterId', $filter->pk());
            $this->setSite('categoryFilter', $filter);

            $this->setBreadCrumbsParents($model, true);
            $meta = $model->getMeta();
            $metaFilter = $filter->getMeta();

            if ($value = $metaFilter->getTitle())
                $meta->setTitle($value);
            if ($value = $metaFilter->getDesc())
                $meta->getDesc($value);

            $model->name = $filter->name ? $filter->name : $model->name;
            $model->header = $filter->header ? $filter->header : $model->header;
            $model->text_top = $filter->text_top ? $filter->text_top : $model->text_top;
            $model->text_below = $filter->text_below ? $filter->text_below : $model->text_below;

            $this->breadCrumbs->setLastItem($model->name);
            $feature_hash = $filter->hash;
        }

        $this->setMeta($meta, array(
            'title' => $model->name,
            'desc' => $model->text_below ? $model->text_below : $model->text_top,
            'property' => array(
                'og:image' => $model->getImage()->preview,
                'og:title' => $model->name,
                'og:description' => $model->text_below ? $model->text_below : $model->text_top,
            )
        ));

        $get = $this->httpRequest->getRequest();
        if ($model->show_cats && $model->children) {
            $this->listCategories($model);
        } else if (Shop::isShowCollection($get)) {
            $this->listCollection($model);
        } else {
            $this->listGoods($model, $feature_hash);
        }

    }


    /**
     * @param string|null $url_filter
     * @param Category $category
     */
    protected function listGoods(Category $category, $feature_hash = null)
    {
        if ($this->config['layout']['goods'])
            $this->layout($this->config['layout']['goods']);

        $var['ids'] = $var['sub_categories'] = $idsCat = array();
        $res = $category->getChildren();
        if (count($res)) {
            $childrenCatIds = array();
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
                $childrenCatIds[] = $cat['cid']; // save children cat ids
                $var['sub_categories'][] = $cat;
            }
        }
        $this->setSite('childrenCatIds', $childrenCatIds);
        $idsCat[] = $category->pk();

        $get = $this->httpRequest->getRequest();

        if ($feature_hash) {
            $filters = Filter::parser_hash($feature_hash);
        } else {
            $filters = Filter::parser_request($get);
        }


        if (sizeof($idsCat)) {
            $goods = Goods::model()
                ->select_array($this->container->getParameters('product_select_list'))
                ->active()
                ->ctype($this->type_id)
                ->filters($filters)
                ->sort($get);
            /** если не указаны конкретные категории */
            if (!sizeof($get['cats'])) {
                $goods->where('cid', 'IN', $idsCat);
            }
            $pagination = PaginationBuilder::factory($goods)
                ->setItemCount(false)
                ->setPage($category->show_all ? PaginationBuilder::ITEMS_ALL : $get['page'])
                ->setItemsPerPage($this->perPage)
                ->addQueries($get);

            $var['pagination'] = $pagination;
            $this->getHeader()->setPagination($pagination);
            $var['goods'] = $ids = array();
            $result = $pagination->result();
            foreach ($result as $item) {
                $ids[] = $item['goods_id'];
                $var['goods'][] = $item;
            }

            if (sizeof($ids)) {
                Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
            }
        }

        if ($this->config['show']['types']) {
            if (sizeof($ids)) {
                $types = TypeGoods::model()
                    ->where('goods_id', 'in', $ids)
                    ->cached()
                    ->find_all();
                foreach ($types as $type) {
                    $var['types'][$type->type_id][$type->goods_id] = true;
                }
            }
        }
        $var['category'] = $category;
        $var['get'] = $get;
        $var['ids'] = $idsCat;
        $var['basket'] = $this->basket;
        $theme = $category->prefix_goods ? '_' . $category->prefix_goods : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/list' . $theme, $var));

    }


    /**
     * @param  \Shop\Catalog\Entity\Category $category
     */
    protected function listCategories(Category $category = null)
    {
        $var['category'] = $category;
        $categories = Category::model()
            ->type($this->type_id)
            ->parent($category ? $category->pk() : 0)
            ->active()
            ->sort()
            ->find_all();

        $ids = array();
        foreach ($categories as $item) {
            $var['categories'][] = $item;
            $ids[] = $item->pk();
        }
        if (count($ids)) {
            $images = Image::model()
                ->whereByTargetType(Category::model())
                ->whereByTargetId($ids)
                ->cached()
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $theme = $category->prefix ? '_' . $category->prefix : '';
        $this->response($this->view->load($this->config['view']['category'] . '/list' . $theme, $var));
    }


    /**
     * @param $type_id
     */
    public function typeAction($type_id)
    {
        $this->setBreadCrumbs();

        if ($this->config['layout']['type'])
            $this->layout($this->config['layout']['type']);

        $get = $this->httpRequest->getRequest();

        $goods = Goods::model()
            ->ctype($this->type_id)
            ->active()
            ->whereType($type_id)
            ->filters($get)
            ->sort($get);

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($get['page'])
            ->setItemsPerPage($this->perPage)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $this->getHeader()->setPagination($pagination);
        $var['goods'] = $ids = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $ids[] = $item->pk();
            $var['goods'][] = $item;
        }
        if (sizeof($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }

        $var['get'] = $get;
        $var['basket'] = $this->basket;
        $var['type_id'] = $type_id;
        $type = $this->container->getParameters('shop.commodity.types.' . $type_id);
        $this->breadCrumbs->setLastItem($type['name']);
        $this->response($this->view->load($this->config['view']['goods'] . '/type/list', $var));
    }

    public function popularPartial($limit = 6, Category $category = null)
    {
        if (!$category) {
            return;
        }

        $var['category'] = $category;

        $products = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->active()
            ->ctype($this->type_id)
            ->limit($limit)
            ->sortByPopular();


        $idsCat = array();
        $idsCat[] = $category->pk();
        foreach ($category->getChildren() as $cat) {
            $idsCat[] = $cat['cid'];
        }
        $products->whereCatId($idsCat);


        $ids = $var['products'] = array();
        $result = $products->find_all();
        foreach ($result as $item) {
            $var['goods'][] = $item;
            $ids[] = $item['goods_id'];
        }

        if (sizeof($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, true);
        }
        $var['basket'] = $this->basket;
        $this->response($this->view->load('shop/goods/_list_popular', $var));

    }


    public function popularSelectPartial(Category $category = null, $theme = null)
    {
        $var['category'] = $category;
        $items = CategoryPopularProduct::model()
            ->where('cat_id', '=', $category->pk())
            ->select()
            ->sort()
            ->find_all();

        $ids = $var['items'] = array();
        foreach ($items as $item) {
            $ids[] = $item['product_id'];
            $var['items'][] = $item;
        }

        if (count($ids)) {
            $goods = Goods::model()
                ->select_array($this->container->getParameters('product_select_list'))
                ->active()
                ->ctype($this->type_id)
                ->where('goods_id', 'in', $ids)
                ->find_all();

            foreach ($goods as $item) {
                $var['goods'][$item['goods_id']] = $item;
            }

            if (sizeof($ids)) {
                Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
            }
        }
        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['category'] . '/_popular_products' . $theme, $var));
    }


    /**
     * COLLECTION
     */

    /**
     * @param  \Shop\Catalog\Entity\Category $category
     */
    protected function listCollection(Category $category = null)
    {
        if ($this->config['layout']['collection'])
            $this->layout($this->config['layout']['collection']);

        $var['ids'] = $var['sub_categories'] = $idsCat = array();
        $res = $category->getChildren();
        $childrenCatIds = array();
        foreach ($res as $cat) {
            $idsCat[] = $cat['cid'];
            $childrenCatIds[] = $cat['cid']; // save children cat ids
            $var['sub_categories'][] = $cat;
        }
        $this->setSite('childrenCatIds', $childrenCatIds);
        $idsCat[] = $category->pk();

        $get = $this->httpRequest->getRequest();

        $collections = Collection::model()
            ->active()
            ->sort()
            ->ctype($this->type_id);

        /** если не указаны конкретные категории */
        if (sizeof($idsCat)) {
            $collections->where('cid', 'IN', $idsCat);
        }
        $pagination = PaginationBuilder::factory($collections)
            ->setItemCount(false)
            ->setPage($category->show_all ? PaginationBuilder::ITEMS_ALL : $get['page'])
            ->setItemsPerPage($this->perPage)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $this->getHeader()->setPagination($pagination);
        $var['collections'] = $ids = $idsVendors = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $ids[] = $item->pk();
            if ($item->vendor_id)
                $idsVendors[] = $item->vendor_id;
            $var['collections'][] = $item;
        }


        if (sizeof($ids)) { #images
            $images = Image::model()
                ->whereByTargetType(Collection::model())
                ->whereByTargetId($ids)
                ->where('main', '=', 1)
                ->cached()
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        if (count($idsVendors)) { #vendor
            $vendors = Vendor::model()
                ->where('vendor_id', 'in', $idsVendors)
                ->find_all();

            $var['vendors'] = $idsCountry = array();
            foreach ($vendors as $item) {
                $var['vendors'][$item['vendor_id']] = $item;
            }
        }

        $var['category'] = $category;
        $var['get'] = $get;
        $var['ids'] = $idsCat;
        $theme = $category->prefix ? '_' . $category->prefix : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/list_collection' . $theme, $var));
    }


    /**
     * CATEGORY COLLECTION
     */


    /**
     * @param $url
     * @Model(name=Shop\Catalog\Entity\Collection)
     */
    public function collectionAction(CategoryCollection $model, $url)
    {
        load_or_404($model);

        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $this->getUrlModelCollection($model),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $this->setSite('collectionCategoryId', $model->pk());
        $this->setSite('collectionCategory', $model);


        $this->setBreadCrumbs();
        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {
                $this->setSite('categoryId', $model->cid);
                $this->setSite('category', $category);
                $this->setBreadCrumbsParents($category, true);
            }
        }
        $this->breadCrumbs->setLastItem($model->name);


        $this->setMeta($model->getMeta(), array(
            'title' => $model->name,
            'desc' => $model->text_below ? $model->text_below : $model->text_top,
            'property' => array(
                'og:image' => $model->getImage()->preview,
                'og:title' => $model->name,
                'og:description' => $model->text_below ? $model->text_below : $model->text_top,
            )
        ));

        $this->listCollectionGoods($model, $category);

    }

    /**
     * @param CategoryCollection $collection
     * @param Category|null $category
     * @throws \Delorius\Exception\Error
     */
    protected function listCollectionGoods(CategoryCollection $collection, Category $category = null)
    {
        if ($this->config['layout']['goods'])
            $this->layout($this->config['layout']['goods']);


        $var['ids'] = $idsCat = array();
        if (!$collection->cats && $category && $category->loaded()) {
            $res = $category->getChildren();
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
                $var['sub_categories'][] = $cat;
            }
            $this->setSite('childrenCatIds', $idsCat);
            $idsCat[] = $category->pk();
            $var['ids'] = $idsCat;
        }

        $get = $this->httpRequest->getRequest();
        $filters = $collection->mergeRequest($get);
        $ids = Filter::getGoodsIds($filters);
        $filters = Filter::parser_request($get);

        $goods = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->active()
            ->ctype($this->type_id)
            ->filters($filters)
            ->sort($get);

        if (count($ids)) {
            $goods->where('goods_id', 'in', $ids);
        } else {
            $goods->limit(0);
        }

        if (!count($filters['cats']) && count($idsCat)) {
            $goods->where('cid', 'IN', $idsCat);
        }

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($collection->show_all ? PaginationBuilder::ITEMS_ALL : $get['page'])
            ->setItemsPerPage($this->perPage)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $this->getHeader()->setPagination($pagination);
        $var['goods'] = $ids = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $ids[] = $item['goods_id'];
            $var['goods'][] = $item;
        }

        if (sizeof($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }

        $var['category'] = $collection;
        $var['get'] = $get;
        $var['basket'] = $this->basket;
        $theme = $collection->prefix ? '_' . $collection->prefix : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/list' . $theme, $var));

    }


    /**
     * @Post
     */
    public function filtersDataAction()
    {
        try {
            $data = $this->httpRequest->getRequest();
            $get = rawurldecode($data['get']);
            parse_str($get, $output);
            if (!count($output)) {
                throw new Error('Выберите параметры фильтра');
            }
            $hash = Filter::parser_get($output);

            if ($data['col_cid']) {
                $collection = new CategoryCollection($data['col_cid']);
                if ($collection->loaded()) {
                    $url = $this->getUrlModelCollection($collection);
                    $urlScript = new UrlScript($url);
                    $urlScript->setQuery(array('feature_hash' => $hash));
                } else {
                    throw new Error('Коллекция не найдена');
                }
            } else {
                $category = new Category($data['cid']);
                if ($category->loaded()) {

                    $filter_static = CategoryFilter::model()
                        ->where('hash', '=', $hash)
                        ->where('cid', '=', $category->pk())
                        ->active()
                        ->find();
                    if ($filter_static->loaded()) {
                        $url = $this->getUrlModelStaticFilter($category, $filter_static);
                        $urlScript = new UrlScript($url);
                    } else {
                        $url = $this->getUrlModel($category);
                        $urlScript = new UrlScript($url);
                        $urlScript->setQuery(array('feature_hash' => $hash));
                    }

                } else {
                    throw new Error('Категория не найдена');
                }
            }

            $this->response(array('url' => $urlScript->__toString()));

        } catch (Error $e) {
            $this->response(array('error' => $e->getMessage()));
        }
    }


    /**
     * @param Category $model
     * @return string
     */
    public function getUrlModelCollection(CategoryCollection $model)
    {
        return link_to('shop_category_collection', array('cid' => $model->pk(), 'url' => $model->url));
    }

    /**
     * @param Category $category
     * @param CategoryFilter $filter
     * @return string
     */
    public function getUrlModelStaticFilter(Category $category, CategoryFilter $filter)
    {
        return link_to('shop_category_filter', array('cid' => $category->pk(), 'url' => $category->url, 'url_filter' => $filter->url));
    }

}