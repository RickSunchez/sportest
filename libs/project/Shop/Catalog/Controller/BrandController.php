<?php

namespace Shop\Catalog\Controller;

use CMS\Core\Entity\Image;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Vendor;
use Shop\Commodity\Helpers\Options;


class BrandController extends ShopController
{

    public function before()
    {
        $this->config = $this->container->getParameters('shop.vendor');
        $this->perPage = $this->config['page'];
        $this->setSite('goodsTypeId', $this->type_id);
    }

    /**
     * @throws \Delorius\Exception\Error
     */
    public function listBrandAction()
    {
        $this->setBreadCrumbs(true);
        $this->setMeta(null, array(
            'title' => 'Каталог брендов',
        ));

        $vendors = Vendor::model()
            ->sort()
            ->active()
            ->find_all();
        $var['vendors'] = $vendors;

        $images = Image::model()
            ->whereByTargetType(Vendor::model())
            ->select('image_id', 'target_id', 'preview', 'normal')
            ->cached()
            ->find_all();
        $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');

        $this->response($this->view->load($this->config['view'] . '/list', $var));
    }

    /**
     * @param $url
     */
    public function showBrandAction($url)
    {
        $model = Vendor::model()
            ->where('url', '=', $url)
            ->find();

        $var['vendor'] = $model;

        load_or_404($model);

        $this->setSite('vendorId', $model->pk());
        $this->setSite('vendor', $model);
        $this->setGUID($model->pk());

        $this->setBreadCrumbs();
        $this->breadCrumbs->setLastItem($model->name);

        $this->setMeta($model->getMeta(), array(
            'title' => $model->name,
            'desc' => $model->text,
            'property' => array(
                'og:image' => $model->getImage()->preview,
                'og:title' => $model->name,
                'og:description' => $model->text,
            )
        ));


        $get = $this->httpRequest->getRequest();
        $goods = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->ctype($this->type_id)
            ->where('vendor_id', '=', $model->pk())
            ->sort($get)
            ->active();

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($get['page'])
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
            Options::acceptFirstVariantsByProducts($var['goods'], $ids);
        }

        $var['basket'] = $this->basket;
        $this->response($this->view->load($this->config['view'] . '/show', $var));
    }

    /**
     * @param null $limit
     * @throws \Delorius\Exception\Error
     */
    public function listBrandPartial($limit = null)
    {
        $vendors = Vendor::model()
            ->sort()
            ->active();

        if ($limit) {
            $vendors->limit($limit);
        }
        $var['vendors'] = $vendors->find_all();

        $images = Image::model()
            ->whereByTargetType(Vendor::model())
            ->select('image_id', 'target_id', 'preview', 'normal')
            ->cached()
            ->find_all();
        $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');

        $this->response($this->view->load($this->config['view'] . '/_list', $var));
    }


}