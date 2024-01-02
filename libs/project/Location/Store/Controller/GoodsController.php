<?php

namespace Location\Store\Controller;

use Delorius\Exception\NotFound;
use Shop\Catalog\Entity\Category;

class GoodsController extends \Shop\Commodity\Controller\GoodsController
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

}