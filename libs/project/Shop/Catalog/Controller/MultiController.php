<?php

namespace Shop\Catalog\Controller;

use Shop\Catalog\Entity\CategoryFilter;
use Shop\Catalog\Helpers\Filter;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Helpers\Options;

class MultiController extends ShopController
{


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

        if ($model->show_cats && $model->children) {
            $this->listCategories($model);
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
        $childrenCatIds = array();
        foreach ($res as $cat) {
            $idsCat[] = $cat['cid'];
            $childrenCatIds[] = $cat['cid']; // save children cat ids
            $var['sub_categories'][] = $cat;
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
                ->sort($get)
                ->whereCatsId($idsCat);

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

        $var['category'] = $category;
        $var['get'] = $get;
        $var['ids'] = $idsCat;
        $var['basket'] = $this->basket;
        $theme = $category->prefix_goods ? '_' . $category->prefix_goods : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/list' . $theme, $var));

    }


}