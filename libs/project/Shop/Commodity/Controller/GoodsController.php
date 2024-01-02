<?php

namespace Shop\Commodity\Controller;

use CMS\Core\Entity\Image;
use Delorius\DataBase\DB;
use Delorius\Http\IResponse;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Shop\Catalog\Controller\ShopController;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Accompany;
use Shop\Commodity\Entity\Collection;
use Shop\Commodity\Entity\CollectionPackage;
use Shop\Commodity\Entity\CollectionProduct;
use Shop\Commodity\Entity\CollectionProductItem;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\TypeGoods;
use Shop\Commodity\Entity\Vendor;
use Shop\Commodity\Helpers\Options;
use Shop\Commodity\Helpers\Popular;
use Shop\Commodity\Rendering\MultiBreadcrumbRenderer;

class GoodsController extends ShopController
{

    /**
     * Current goods id
     * @var int
     */
    protected static $goodsId = 0;

    public function before()
    {
        $this->config = $this->container->getParameters('shop.commodity.type.' . $this->type_id);
        $this->router = $this->config['router'];
    }

    /**
     * @Model(name=Shop\Commodity\Entity\Goods,field=id)
     */
    public function showAction(Goods $model, $url)
    {
        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $model->link(),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        if ($this->config['layout']['goods'])
            $this->layout($this->config['layout']['goods']);

        $this->lastModified($model->date_edit ? $model->date_edit : $model->date_cr);

        if ($model->status == 0) {
            $var['goods'] = $model;

            $this->breadCrumbs->setLastItem($model->name);
            $this->setMeta(null, array(
                'title' => $model->name,
                'property' => array(
                    'og:title' => $model->name
                )
            ));
            $this->httpResponse->setCode(Response::S404_NOT_FOUND);
            $this->response($this->view->load($this->config['view']['goods'] . '/none', $var));
            return;
        }

        #breadCrumbs
        if (!$model->isMulti()) {
            $this->setBreadCrumbs();
            if ($model->cid) {
                $category = new Category($model->cid);
                if ($category->loaded()) {
                    $this->setSite('categoryId', $model->cid);
                    $this->setSite('category', $category);
                    $this->setBreadCrumbsParents($category, true);
                    $var['category'] = $category;
                    $metaGoods = $category->getMetaGoods();
                    $metaGoods->setKey('product', $model->as_array());
                    $metaGoods->setKey('category', $category->as_array());
                    $var['alt_text'] = $metaGoods->getText();
                }
            }

            $this->breadCrumbs->setLastItem($model->getShortName());
        } else {
            $cats = $model->getMultiCategories();
            $this->setBreadCrumbs();
            $this->breadCrumbs->setLastItem($model->getShortName());
            $this->breadCrumbs->setRenderer(new MultiBreadcrumbRenderer($cats, $this->router));
        }
        #breadCrumbs end


        Popular::view($model->pk());
        $this->setSite('productId', $model->pk());
        $this->setSite('product', $model);
        $this->setGUID($model->pk());

        Options::acceptFirstVariants($model, $this->config['show']['images']);

        if ($this->config['show']['images']) {
            $var['images'] = $model->getImages();
        }
        if ($this->config['show']['sections']) {
            $var['sections'] = $model->getSections();
        }
        if ($this->config['show']['characteristics']) {
            $var['characteristics'] = $model->getGroupCharacteristics();
        }
        if ($this->config['show']['types']) {
            $var['types'] = $model->getTypes();
        }

        $var['goods'] = $model;
        $var['basket'] = $this->basket;
        $var['get'] = $this->httpRequest->getQuery();

        $meta = $model->getMeta();

        if ($metaGoods) {
            if (!$meta->title && ($value = $metaGoods->getTitle())) {
                $meta->setTitle($value);
            }
            if (!$meta->desc && ($value = $metaGoods->getDesc())) {
                $meta->setDesc($value);
            }
        }

        $this->setMeta($meta, array(
            'title' => $model->name,
            'desc' => $model->brief,
            'property' => array(
                'og:image' => $model->image ? $model->image->preview : null,
                'og:title' => $model->name,
                'og:description' => $model->brief,
            )
        ));


        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/show' . $theme, $var));
    }


    public function accompaniesPartial($goodsId, $type_id = null, $theme = null)
    {
        $accoIds = new Accompany();
        $goods = new Goods();
        $goods
            ->join($accoIds->table_name(), 'INNER')
            ->on(
                $goods->table_name() . '.' . $goods->primary_key(),
                '=',
                $accoIds->table_name() . '.target_id'
            )
            ->where($accoIds->table_name() . '.current_id', '=', $goodsId)
            ->active()
            ->is_amount()
            ->order_by($accoIds->table_name() . '.pos', 'desc')
            ->order_pk();

        if (is_scalar($type_id) && $type_id !== null) {
            $goods->where($accoIds->table_name() . '.type_id', '=', $type_id);
        }

        $accompanies = $goods->find_all();
        foreach ($accompanies as $acco) {
            $ids[] = $acco->pk();
            $var['goods'][] = $acco;
        }

        if (count($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }

        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/_accompanies' . $theme, $var));

    }

    /**
     * @param int $typeId Тип товара
     * @param mixed $limit Кол-во выводимых товаров
     */
    public function listTypePartial($typeId, $limit = 0, $theme = null)
    {
        $var = array();
        $goods = Goods::model()
            ->active()
            ->is_amount()
            ->ctype($this->type_id)
            ->whereType($typeId)
            ->orderByType();
        if (is_int($limit) && $limit != 0) {
            $goods->limit($limit);
        }

        $arrGoods = $goods->find_all();
        $ids = array();
        foreach ($arrGoods as $item) {
            $var['goods'][] = $item;
            $ids[] = $item->pk();
        }
        if (count($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }
        $var['theme'] = $theme;
        $var['type_id'] = $typeId;
        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/type/' . $typeId . $theme, $var));
    }


    public function filterSortPartial($theme = null)
    {
        $get = $this->httpRequest->getRequest();
        /** @var \Delorius\Http\UrlScript $url */
        $url = clone $this->container->getService('url');
        $url->setQuery($get);

        $url_price_asc = clone $url;
        $get['sort'] = 'price';
        $get['order'] = 'asc';
        $url_price_asc->setQuery($get);
        $var['url_price_asc'] = $url_price_asc;

        $url_price_desc = clone $url;
        $get['sort'] = 'price';
        $get['order'] = 'desc';
        $url_price_desc->setQuery($get);
        $var['url_price_desc'] = $url_price_desc;

        $url_name_asc = clone $url;
        unset($get['sort']);
        unset($get['order']);
        $url_name_asc->setQuery($get);
        $var['url_name_asc'] = $url_name_asc;

        $url_name_desc = clone $url;
        $get['sort'] = 'name';
        $get['order'] = 'desc';
        $url_name_desc->setQuery($get);
        $var['url_name_desc'] = $url_name_desc;

        $var['url_current'] = clone $url;
        $url->setQuery(array());
        $var['url'] = $url;

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/_filter_sort' . $theme, $var));
    }

    /**
     * Вы только что смотрели
     * @return null
     */
    public function youWatchedPartial()
    {
        $youWatched = $this->session->getSection('youWatched');
        $ids = $youWatched->ids ? $youWatched->ids : array();
        $currentGoodsId = $this->getSite('productId');
        $var['count'] = $count = $this->container->getParameters('shop.you_watched');

        if (count($ids))
            foreach ($ids as $index => $id) {
                if ($id == $currentGoodsId) {
                    unset($ids[$index]);
                    break;
                }
            }

        if ($count < count($ids)) {
            array_pop($ids);
        }

        if (count($ids)) {
            $var['ids'] = $ids;
            $goods = Goods::model()
                ->active()
                ->where('ctype', '=', $this->type_id)
                ->where('goods_id', 'in', $ids)
                ->find_all();

            $_ids_ = array();
            foreach ($goods as $item) {
                $var['goods'][$item->pk()] = $item;
                $_ids_[] = $item->pk();
            }
            if (count($_ids_)) {
                Options::acceptFirstVariantsByProducts($var['goods'], $_ids_, $this->config['show']['images']);
            }
        }
        array_unshift($ids, $currentGoodsId);
        $youWatched->ids = $ids;
        $this->response($this->view->load($this->config['view']['goods'] . '/_you_watched', $var));
    }

    /**
     * Показать случайны товара из этой же категори,
     * кроме указаного товара
     * @param $catId
     * @param int $limit
     * @param null $goodsId товар который стоит сключить из показа
     * @param null $theme
     * @throws \Delorius\Exception\Error
     */
    public function inRndPartial($catId, $limit = 4, $goodsId = null, $theme = null)
    {
        $db = Goods::model()
            ->active()
            ->limit($limit)
            ->order_by(DB::expr('RAND()'))
            ->where('ctype', '=', $this->type_id)
            ->is_amount()
            ->whereCatId($catId);
        if ($goodsId != null) {
            $db->where('goods_id', '<>', $goodsId);
        }
        $result = $db->find_all();

        $ids = $var['goods'] = array();
        foreach ($result as $item) {
            $ids[] = $item->pk();
            $var['goods'][] = $item;
        }
        if (count($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }
        $var['basket'] = $this->basket;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/_in_list_rnd' . $theme, $var));
    }



    /**
     * COLLECTION
     **/

    /**
     * @Model(name=Shop\Commodity\Entity\Collection)
     */
    public function collectionShowAction(Collection $model, $url)
    {

        if ($this->config['layout']['collection'])
            $this->layout($this->config['layout']['collection']);

        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $model->link(),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $this->lastModified($model->date_edit ? $model->date_edit : $model->date_cr);

        #breadCrumbs
        $this->setBreadCrumbs();
        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {

                $this->setBreadCrumbsParents($category, true);
                $var['category'] = $category;
            }
        }
        $this->breadCrumbs->setLastItem($model->getShortName());
        #breadCrumbs end

        #breadCrumbs
        $this->setBreadCrumbs();
        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {
                $this->setSite('categoryId', $model->cid);
                $var['category'] = $category;
                $this->setBreadCrumbsParents($category, true);
            }

        }
        $this->breadCrumbs->setLastItem($model->getShortName());
        #breadCrumbs end


        $this->setSite('categoryId', $model->cid);
        $this->setSite('collectionId', $model->pk());

        if ($this->config['show']['images']) {
            $var['images'] = $model->getImages();
        }

        $var['goods'] = $goodsIds = $vendorIds = array();
        foreach ($model->getGoods() as $goods) {
            $var['goods'][$goods->pk()] = $goods;
            $goodsIds[] = $goods->pk();
            $vendorIds[] = $goods->vendor_id;
        }

        if (count($goodsIds)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $goodsIds, $this->config['show']['images']);
        }

        #vendor
        if ($model->vendor_id) {
            $vendorIds[] = $model->vendor_id;
        }
        if ($this->config['show']['vendor'] && count($vendorIds)) {
            $vendors = Vendor::model()
                ->where('vendor_id', 'in', $vendorIds)
                ->find_all();
            $var['vendors'] = array();
            foreach ($vendors as $vendor) {
                $var['vendors'][$vendor['vendor_id']] = $vendor;
            }
        }

        $packageGoods = $model->getPackageGoods();
        $packageIds = $packageDate = array();
        foreach ($packageGoods as $item) {
            if ($item->package_id && !isset($packageDate[$item->package_id]))
                $packageIds[] = $item->package_id;

            $packageDate[$item->package_id][] = $item->goods_id;
        }
        $var['packageIds'] = $packageIds;
        $var['packageDate'] = $packageDate;

        if (count($packageIds)) {
            $packages = CollectionPackage::model()->where('id', 'in', $packageIds)->find_all();
            $var['packages'] = Arrays::resultAsArrayKey($packages, 'id');
        }
        $var['collection'] = $model;
        $var['category'] = $category;
        $meta = $model->getMeta();
        $this->setMeta($meta, array(
            'title' => $model->name,
            'desc' => $model->text,
            'property' => array(
                'og:image' => $model->getMainImage()->preview,
                'og:title' => $model->name,
                'og:description' => $model->text
            )
        ));

        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/collection' . $theme, $var));

    }


    /**
     * @param string $text
     */
    public function searchAction($page)
    {
        $length = 4;
        $get = $this->httpRequest->getRequest();

        if ($this->config['layout']['search'])
            $this->layout($this->config['layout']['search']);

        $var['query'] = $query = Strings::trim(Html::clearTags($get['query']));

        if ($this->httpRequest->isPost()) {
            $browser = $this->container->getService('browser');
            $mobile = null;
            if ($browser->isMobile()) {
                $mobile = '_m';
            }
            \CMS\SEO\Model\Search::add($query, 'product' . $mobile, $length);
        }

        $goods = Goods::model()
            ->active()
            ->ctype($this->type_id)
            ->filters($get)
            ->sort($get);

        if (Strings::length($query) > 0) {
            $this->breadCrumbs->setLastItem(_t('Shop:Catalog', 'Search') . ': "' . Strings::firstUpper($query) . '" ');
            $this->getHeader()->AddTitle(Strings::firstUpper($query));
            $var['aQuery'] = $aQuery = Strings::parserKeywords($query, $length);
            $goods->where_open();
            $goods->or_where('name', 'like', '%' . $query . '%');
            $goods->or_where('short_name', 'like', '%' . $query . '%');
            $goods->or_where('article', 'like', '%' . $query . '%');
            $goods->or_where('brief', 'like', '%' . $query . '%');
            $goods->or_where('model', 'like', '%' . $query . '%');
            $goods->where_close();
        } elseif (isset($get['vendors'][0])) {
            $vendor = new Vendor($get['vendors'][0]);
            if ($vendor->loaded()) {
                $var['query'] = $vendor->name;
                $this->breadCrumbs->setLastItem(_t('Shop:Catalog', 'Search') . ': "' . $vendor->name . '" ');
                $this->getHeader()->AddTitle('Производитель: ' . $vendor->name);
            }
        } else {
            $this->httpResponse->redirect(link_to('homepage'));
            exit;
        }

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $this->getHeader()->setPagination($pagination);
        $var['goods'] = $ids = array();
        $result = $pagination->result();
        if (count($result) == 1) {
            $goods = $result->current();
            $this->httpResponse->redirect($goods->link());
        } elseif (count($result) == 0) {
            $this->httpResponse->setCode(IResponse::S404_NOT_FOUND);
        }
        foreach ($result as $item) {
            $ids[] = $item->pk();
            $var['goods'][] = $item;
        }
        if (sizeof($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }
        $this->setSite('goodsIds', $ids);
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
        $var['get'] = $get;
        $var['basket'] = $this->basket;
        $this->response($this->view->load($this->config['view']['goods'] . '/search', $var));
    }

    /**
     * @param bool $image_id
     * @throws \Delorius\Exception\Error
     */
    public function collectionProductPartial($image_id = true)
    {
        if (!($productId = $this->getSite('productId'))) {
            return;
        }

        $result = CollectionProductItem::model()
            ->select('coll_id')
            ->where('product_id', '=', $productId)
            ->find_all();
        if (!count($result)) {
            return;
        }

        $ids = array();
        foreach ($result as $item) {
            $ids[] = $item['coll_id'];
        }

        $var['collections'] = $collections = CollectionProduct::model()
            ->active()
            ->sort()
            ->select('label', 'id', 'prefix')
            ->where('id', 'in', $ids)
            ->find_all();

        if (!count($collections)) {
            return;
        }

        $cids = array();
        foreach ($collections as $item) {
            $cids[] = $item['id'];
        }

        $items = CollectionProductItem::model()
            ->select()
            ->where('coll_id', 'in', $cids)
            ->sort()
            ->find_all();

        $var['items'] = $ids = $productIds = array();
        foreach ($items as $item) {
            $var['items'][$item['coll_id']][] = $item;
            $ids[] = $item['id'];
            $productIds[$item['product_id']] = $item['product_id'];
        }

        if (count($ids) && $image_id) {
            $images = Image::model()
                ->select('image_id', 'target_id', 'normal', 'preview')
                ->whereByTargetType(CollectionProductItem::model())
                ->whereByTargetId($ids)
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        if (count($productIds)) {
            $goods = Goods::model()
                ->select('goods_id', 'url', 'name')
                ->active()
                ->where('goods_id', 'in', $productIds)
                ->find_all();
            $var['products'] = Arrays::resultAsArrayKey($goods, 'goods_id');
        }

        $var['currentId'] = $productId;
        $this->response($this->view->load($this->config['view']['goods'] . '/_collection', $var));
    }

}