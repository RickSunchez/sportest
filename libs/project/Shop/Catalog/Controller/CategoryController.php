<?php

namespace Shop\Catalog\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\View\Html;
use Shop\Catalog\Entity\Category;
use CMS\Core\Entity\Image;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\TypeGoods;
use Shop\Commodity\Helpers\Options;

class CategoryController extends Controller
{
    /**
     * @var \Shop\Store\Component\Cart\Basket
     * @service basket
     * @inject
     */
    public $basket;

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * Config Shop:Catalog
     * @var array
     */
    protected $config = array();

    /**
     * @var int
     */
    protected $type_id = Category::TYPE_GOODS;

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var string
     */
    protected $router;


    public function before()
    {
        if (!$this->container->getParameters('shop.catalog.init')) {
            throw new ForbiddenAccess('Отключен каталог');
        }
        $this->config = $this->container->getParameters('shop.catalog.type.' . $this->type_id);
        $this->router = $this->config['router'];
        $this->perPage = $this->config['page'];
        $this->setSite('goodsTypeId', $this->type_id);
    }


    /**
     * @Get
     */
    public function indexAction()
    {
        $this->setBreadCrumbs(true);
        $this->listCategories();
    }

    /**
     * @param $url
     * @Model(name=Shop\Catalog\Entity\Category,field=cid)
     */
    public function listAction(Category $model, $url)
    {
        load_or_404($model);


        if ($this->type_id != $model->type_id) {
            throw new NotFound('Тип каталога не соотвествует');
        }

        #corrections url
        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $this->getUrlModel($model),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $this->setSite('categoryId', $model->pk());
        $this->setSite('category', $model->pk());
        $this->setGUID($model->pk());

        $this->setBreadCrumbs();
        $this->setBreadCrumbsParents($model);
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

        #contains subdirectories
        if ($model->children) {
            $this->listCategories($model);

        } #contains goods
        else if ($model->goods) {
            $this->listGoods($model);

        } #nothing
        else {
            if ($this->config['layout']['nothing'])
                $this->layout($this->config['layout']['nothing']);
            $var['category'] = $model;
            $this->response($this->view->load($this->config['view']['category'] . '/none', $var));
        }
    }

    public function multiMenuPartial($theme = null, $image = false)
    {
        $categories = Category::model()
            ->sort()
            ->active()
            ->type($this->type_id)
            ->select(array('cid', 'id'), 'url', 'pid', 'name', 'goods', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = $ids = array();
        foreach ($categories as $cat) {
            $ids[] = $cat['id'];
            $cat['link'] = link_to($this->router, array('url' => $cat['url'], 'cid' => $cat['id']));
            $var['categories'][$cat['pid']][] = $cat;
        }
        $var['selfCategoryId'] = $this->getSite('categoryId');
        $var['menu_id'] = 0;

        if (count($ids) && $image) {
            $images = Image::model()
                ->select('image_id', 'target_id', 'name', 'normal', 'preview')
                ->whereByTargetType(Category::model())
                ->whereByTargetId($ids)
                ->cached()
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['category'] . '/_multi_menu' . $theme, $var));
    }

    public function subPartial($categoryId, $theme = null, $image = false)
    {
        $var['cid'] = $categoryId;
        $categories = Category::model()
            ->sort()
            ->active()
            ->parent($categoryId)
            ->type($this->type_id)
            ->select(array('cid', 'id'), 'url', 'pid', 'name', 'goods', 'children')
            ->find_all();

        $var['categories'] = $ids = array();
        foreach ($categories as $item) {
            $var['categories'][] = $item;
            $ids[] = $item['id'];
        }

        if (count($ids) && $image) {
            $images = Image::model()
                ->select('image_id', 'target_id', 'name', 'normal', 'preview')
                ->whereByTargetType(Category::model())
                ->whereByTargetId($ids)
                ->cached()
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['category'] . '/_sub_list' . $theme, $var));
    }

    /**
     * @param  \Shop\Catalog\Entity\Category $category
     */
    protected function listCategories(Category $category = null)
    {
        if ($this->config['layout']['catalog'])
            $this->layout($this->config['layout']['catalog']);

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
     * @param Category $category
     */
    protected function listGoods(Category $category)
    {
        if ($this->config['layout']['goods'])
            $this->layout($this->config['layout']['goods']);

        $get = $this->httpRequest->getRequest();
        $goods = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->ctype($this->type_id)
            ->whereCatId($category->pk())
            ->filters($get)
            ->sort($get)
            ->active();

        $childrenCatIds = array();
        $childrenCatIds[] = $category->pk();
        $this->setSite('childrenCatIds', $childrenCatIds);

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($category->show_all ? PaginationBuilder::ITEMS_ALL : $get['page'])
            ->setItemsPerPage($this->perPage)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $this->getHeader()->setPagination($pagination);

        $ids = $var['goods'] = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $ids[] = $item['goods_id'];
            $var['goods'][] = $item;
        }
        if (count($ids)) {
            Options::acceptFirstVariantsByProducts($var['goods'], $ids, $this->config['show']['images']);
        }

        if ($this->config['show']['types']) {
            if (count($ids)) {
                $types = TypeGoods::model()
                    ->where('goods_id', 'in', $ids)
                    ->find_all();
                foreach ($types as $type) {
                    $var['types'][$type->type_id][$type->goods_id] = true;
                }
            }
        }
        $var['category'] = $category;
        $var['get'] = $get;
        $var['basket'] = $this->basket;
        $theme = $category->prefix_goods ? '_' . $category->prefix_goods : '';
        $this->response($this->view->load($this->config['view']['goods'] . '/list' . $theme, $var));
    }

    /**
     * @Post
     * @throws \Delorius\Exception\Error
     */
    public function searchDataAction()
    {
        $term = $this->httpRequest->getPost('term');
        $term = Html::clearTags($term);
        $items = array();

        if (!$term) {
            return;
        }

        $categories = Category::model()
            ->type($this->type_id)
            ->select('name', 'cid', 'url')
            ->active()
            ->sort()
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        foreach ($categories as $item) {
            $item['link'] = link_to($this->router, array('cid' => $item['cid'], 'url' => $item['url']));
            unset($item['cid'], $item['url']);
            $item['type'] = 'category';
            $items[] = $item;
        }

        $goods = Goods::model()
            ->limit(10)
            ->active()
            ->ctype($this->type_id)
            ->sort()
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        foreach ($goods as $item) {
            $arr['link'] = $item->link();
            $arr['name'] = $item->name;
            $arr['value'] = $item->value;
            $arr['price'] = $item->getPrice();
            $arr['id'] = $item->pk();
            $arr['type'] = 'product';
            $items[] = $arr;
        }

        $this->response($items);
    }

    /**
     * @param Category $category
     * @param bool $self
     */
    protected function setBreadCrumbsParents(Category $category, $self = false)
    {
        $parentCategory = $category->getParents();
        if ($parentCategory) {
            $reverse = array_reverse($parentCategory);
            $this->setSite('parentCategoryId', $reverse[0]['cid']);
            foreach ($reverse as $cat) {
                $this->breadCrumbs->addLink(
                    $cat['name'],
                    _sf(
                        '{0}?url={1}&cid={2}', $this->router, $cat['url'], $cat['cid']
                    )
                );
            }
        } else {
            $this->setSite('parentCategoryId', $category->pk());
        }

        if ($self) {
            $this->breadCrumbs->addLink(
                $category->name,
                _sf(
                    '{0}?url={1}&cid={2}', $this->router, $category->url, $category->pk()
                )
            );
        }
    }

    /**
     * @param Category $model
     * @return string
     */
    public function getUrlModel(Category $model)
    {
        return link_to($this->router, array('cid' => $model->pk(), 'url' => $model->url));
    }

    /**
     * @param bool $first
     */
    protected function setBreadCrumbs($first = false)
    {
        if ($first) {
            if ($this->config['first']['name']) {
                $this->breadCrumbs->setLastItem(
                    $this->config['first']['name']
                );
            }
        } else {
            if ($this->config['first']['name'] && count($this->config['first']['router'])) {
                $this->breadCrumbs->addLink(
                    $this->config['first']['name'],
                    link_to_array($this->config['first']['router']),
                    $this->config['first']['name'],
                    false
                );
            }
        }
    }

}