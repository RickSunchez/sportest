<?php
namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\TypeGoods;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Типы товаров #admin_goods_type?action=list
 */
class TypeController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    protected $types = array();

    public function before()
    {
        $this->types = $this->container->getParameters('shop.commodity.types');
    }

    /**
     * @Get
     * @AddTitle Список
     */
    public function listAction()
    {
        $var['types'] = $this->types;
        $this->response($this->view->load('shop/goods/type/list', $var));
    }

    /**
     * @Get
     * @param $id
     * @AddTitle Товары
     */
    public function goodsAction($id)
    {
        $type = $this->types[$id]['name'];
        $this->breadCrumbs->setLastItem($type);
        $arrGoods = Goods::model()
            ->whereType($id)
            ->find_all();

        $ids = array();
        foreach ($arrGoods as $item) {
            $ids[] = $item->pk();
            $var['goods'][] = $item->as_array();
        }
        if (count($ids)) {
            $arrImages = Image::model()
                ->where('main', '=', 1)
                ->where('target_id', 'in', $ids)
                ->whereByTargetType(Goods::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($arrImages, 'target_id', true);
        }
        $types = TypeGoods::model()->where('type_id', '=', $id)->sort()->find_all();
        $var['types'] = Arrays::resultAsArray($types);
        $var['type_id'] = $id;
        $this->response($this->view->load('shop/goods/type/goods', $var));
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $type = TypeGoods::model()
            ->where('type_id', '=', $post['type_id'])
            ->where('goods_id', '=', $post['goods_id'])
            ->find();
        if ($type->loaded()) {
            $type->delete();
            Goods::model()->cache_delete();

        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function goodsDataAction()
    {
        $name = $this->httpRequest->getPost('name');
        $goods = Goods::model()->sort();

        $aQuery = Strings::parserKeywords($name, 3);
        $goods->where_open();
        foreach ($aQuery as $q) {
            $goods->or_where('name', 'like', '%' . $q . '%');
        }
        $goods->where_close();

        $ids = $result = array();
        $res = $goods->find_all();
        foreach ($res as $item) {
            $ids[] = $item->pk();
            $result['goods'][] = $item->as_array();
        }
        if (count($ids)) {
            $arrImages = Image::model()
                ->where('main', '=', 1)
                ->where('target_id', 'in', $ids)
                ->whereByTargetType(Goods::model())
                ->find_all();
            $result['images'] = Arrays::resultAsArrayKey($arrImages, 'target_id', true);
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Goods,field=goods_id)
     */
    public function addDataAction(Goods $model, $type_id)
    {
        if ($model->setType($type_id)) {
            $result['ok'] = _t('Shop:Admin', 'Product added');
            Goods::model()->cache_delete();
        } else {
            $result['error'] = _t('Shop:Admin', 'Failed to add item');
        }
        $types = TypeGoods::model()->where('type_id', '=', $type_id)->sort()->find_all();
        $result['types'] = Arrays::resultAsArray($types);
        $this->response($result);
    }


    /**
     * @Post
     */
    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $type = TypeGoods::model()
            ->where('type_id', '=', $post['type_id'])
            ->where('goods_id', '=', $post['goods_id'])
            ->find();
        if ($type->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $type->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $type->pos++;
                } else if ($post['type'] == 'down') {
                    $type->pos--;
                }
                $type->save(true);
                $goods = Goods::model()->whereType($type->type_id)->find_all();
                $result['goods'] = Arrays::resultAsArray($goods);
                $types = TypeGoods::model()->where('type_id', '=', $type->type_id)->sort()->find_all();
                $result['types'] = Arrays::resultAsArray($types);
                $result['ok'] = _t('CMS:Admin', 'These modified');

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = _t('CMS:Admin', 'Object not found');
        }
        $this->response($result);
    }


}