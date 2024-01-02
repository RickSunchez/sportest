<?php

namespace Location\Store\Controller;

use CMS\Core\Entity\Image;
use Delorius\Exception\NotFound;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryFilter;
use Shop\Catalog\Entity\Collection as CategoryCollection;

class ShopController extends \Shop\Catalog\Controller\ShopController
{

    /**
     * @var \Location\Core\Model\CitiesBuilder
     * @service city
     * @inject
     */
    public $city;

    public function before()
    {
        parent::before();

        if (!$this->httpRequest->getRequest('not_change_city')) {

            $city_url = $this->getRouter('city_url');
            if ($city_url == null) {
                $this->city->setDefault();
            } elseif (!$this->city->has($city_url)) {
                throw new NotFound('Город не найден');
            } else {
                $this->city->set($city_url);
            }

            $this->setGUID($this->city->getId());
        }
    }


    /**
     * @param null $theme
     * @param bool $image
     * @throws \Delorius\Exception\Error
     */
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
            $cat['link'] = link_to_city($this->router, array('url' => $cat['url'], 'cid' => $cat['id']));
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

    /**
     * @param Category $category
     * @param bool $self
     */
    protected function setBreadCrumbsParents(Category $category, $self = false)
    {
        $city_url = $this->city->getUrl();

        if ($this->city->isDefault()) {
            $router = 'default_' . $this->router;
            $str = '{0}?url={1}&cid={2}';
        } else {
            $router = $this->router;
            $str = '{0}?url={1}&cid={2}&city_url={3}';
        }

        $parentCategory = $category->getParents();
        if ($parentCategory) {
            $reverse = array_reverse($parentCategory);
            $this->setSite('parentCategoryId', $reverse[0]['cid']);
            foreach ($reverse as $cat) {
                $this->breadCrumbs->addLink(
                    $cat['name'],
                    _sf(
                        $str, $router, $cat['url'], $cat['cid'], $city_url
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
                    $str, $router, $category->url, $category->pk(), $city_url
                )
            );
        }
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
                    link_to_city_array($this->config['first']['router']),
                    $this->config['first']['name'],
                    false
                );
            }
        }
    }


    /**
     * @param Category $model
     * @return string
     */
    public function getUrlModel(Category $model)
    {
        return link_to_city($this->router, array('cid' => $model->pk(), 'url' => $model->url));
    }


    /**
     * @param Category $model
     * @return string
     */
    public function getUrlModelCollection(CategoryCollection $model)
    {
        return link_to_city('shop_category_collection', array('id' => $model->pk(), 'url' => $model->url));
    }


    /**
     * @param Category $category
     * @param CategoryFilter $filter
     * @return string
     */
    public function getUrlModelStaticFilter(Category $category, CategoryFilter $filter)
    {
        return link_to_city('shop_category_filter', array('cid' => $category->pk(), 'url' => $category->url, 'url_filter' => $filter->url));
    }
}