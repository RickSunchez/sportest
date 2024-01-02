<?php

namespace Shop\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Accompany;
use Shop\Commodity\Entity\Characteristics;
use Shop\Commodity\Entity\CharacteristicsGroup;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Provider;
use Shop\Commodity\Entity\Unit;
use Shop\Commodity\Entity\Vendor;
use Shop\Store\Entity\Currency;

/**
 * @Template(name=admin)
 * @Admin
 */
class GoodsController extends Controller
{

    protected $tmp = 'shop/goods';

    /**
     * @var \Shop\Catalog\Entity\Category
     */
    public $category;

    /**
     * @var  Register
     * @inject
     */
    public $register;

    /**
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    public function before()
    {
        if (!$this->isViewPartial) {
            $cid = $this->httpRequest->getRequest('cid');
            $type_id = $this->httpRequest->getRequest('type_id', Category::TYPE_GOODS);
            $this->category = new Category($cid);
            if ($this->category->loaded()) {
                $this->breadCrumbs->addLink('Товары', 'admin_goods?action=list&type_id=' . $this->category->type_id);
                $this->breadCrumbs->addLink(
                    $this->category->name,
                    _sf('admin_goods?action=list&cid={0}&type_id={1}', $this->category->pk(), $this->category->type_id));
            } else {
                $this->breadCrumbs->addLink('Товары', 'admin_goods?action=list&type_id=' . $type_id);
            }
        }
    }


    /**
     * @AddTitle Список
     * @Get
     */
    public function listAction($page, $moder, $type_id = Category::TYPE_GOODS)
    {
        $get = $this->httpRequest->getQuery();
        $var['type_id'] = $get['type_id'] = $type_id;
        $table = Goods::model()->table_name();
        $goods = Goods::model()
            ->select('goods_id', 'name', 'article', 'popular', 'status', 'code', 'pos', $table . '.cid', 'amount', 'value', 'value_old', 'moder')
            ->ctype($type_id)->sortByPopular();


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
            $ids[] = $item['goods_id'];
            $var['goods'][] = $item;
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
        $this->response($this->view->load($this->tmp . '/list', $var));
    }

    /**
     * @Post
     */
    public function goodsDataAction()
    {
        $get = $this->httpRequest->getRequest();
        $form = $this->httpRequest->getPost('form', null);
        $type_id = $this->httpRequest->getPost('type_id', Category::TYPE_GOODS);

        $name = $form['name'];
        $article = $form['article'];
        $cid = $form['cid'];

        $goods = Goods::model()
            ->select_array($this->container->getParameters('product_select_list'))
            ->ctype($type_id)
            ->sort();

        if ($name) {
            $goods->where_open()
                ->or_where('goods_id', '=', $name)
                ->or_where('name', 'like', '%' . $name . '%')
                ->where_close();
        }

        if ($article) {
            $goods->where('article', 'LIKE', '%' . $article . '%');
        }

        if ($cid) {
            $category = new Category($cid);
            $idsCats = array();
            if ($category->loaded()) {
                $idsCats[] = $category->pk();
                foreach ($category->getChildren() as $cat) {
                    $idsCats[] = $cat['cid'];
                }
                $goods->where('cid', 'in', $idsCats);
            }
        }

        $pagination = PaginationBuilder::factory($goods)
            ->setItemCount(false)
            ->setPage($get['page'])
            ->setItemsPerPage(30)
            ->addQueries($get);

        $result['pagination'] = $pagination->as_array();
        $result['get'] = $get;

        $ids = $result['goods'] = array();
        foreach ($pagination->result() as $item) {
            $ids[] = $item['goods_id'];
            $result['goods'][] = $item;
        }

        if (count($ids) && $form['image']) {
            $images = Image::model()
                ->main()
                ->select()
                ->whereByTargetId($ids)
                ->whereByTargetType(Goods::model())
                ->find_all();
            $arr = Arrays::resultAsArrayKey($images, 'target_id');
            foreach ($result['goods'] as &$item) {
                if (isset($arr[$item['goods_id']])) {
                    $item['src'] = $arr[$item['goods_id']]['preview'];
                } else {
                    $item['src'] = '/source/images/no.png';
                }
            }
        }
        $this->response($result);
    }

    public function addAccoDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $acco = new Accompany();
            $acco->target_id = $post['target']['goods_id'];
            $acco->current_id = $post['current']['goods_id'];
            $acco->save(true);
            $result['ok'] = 'Товар добавлен';
            $result['accompany'] = $acco->as_array();
        } catch (OrmValidationError $e) {
            $result['error'] = 'Ошибка сервера';
        }
        $this->response($result);
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить продукцию
     * @Get
     */
    public function addAction($cid, $type_id = Category::TYPE_GOODS)
    {
        $var = array();
        $var['multi'] = Goods::model()->isMulti();
        $var['cid'] = $cid;
        $var['type_id'] = $type_id;
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $var['unit'] = Arrays::resultAsArray($units, false);
        $vendors = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($vendors);
        $providers = Provider::model()->cached()->sort()->find_all();
        $var['providers'] = Arrays::resultAsArray($providers);
        $currency = Currency::model()->order_pk()->find_all();
        $var['currency'] = Arrays::resultAsArray($currency);
        $var['types'] = $this->container->getParameters('shop.commodity.types');
        $var['goods_types'] = Arrays::dataKeyValue(Category::getTypes());
        $this->response($this->view->load($this->tmp . '/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать продукцию
     * @Get
     * @Model(name=Shop\Commodity\Entity\Goods)
     */
    public function editAction(Goods $model)
    {
        $var = array();
        $var['type_id'] = $model->ctype;
        $var['goods'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['images'] = Arrays::resultAsArray($model->getImages());
        $var['sections'] = Arrays::resultAsArray($model->getSections());
        $var['chara_goods'] = Arrays::resultAsArray($model->getValueCharacteristics());
        $var['attributes'] = Arrays::resultAsArray($model->getAttributes());
        $var['accompanies'] = Arrays::resultAsArray($model->getAccompanies());

        if ($model->isMulti()) {
            $var['multi'] = true;
            $var['cats'] = Arrays::resultAsArray($model->getCatIds());
        }

        $accos = Accompany::model()->where('current_id', '=', $model->pk())->sort()->find_all();
        $var['goods_accompanies'] = Arrays::resultAsArray($accos);
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $var['units'] = Arrays::resultAsArray($units, false);
        $vendors = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($vendors);
        $providers = Provider::model()->cached()->sort()->find_all();
        $var['providers'] = Arrays::resultAsArray($providers);
        $var['cid'] = $model->cid;
        $currency = Currency::model()->order_pk()->find_all();
        $var['currency'] = Arrays::resultAsArray($currency);
        $var['types'] = $this->container->getParameters('shop.commodity.types');
        $var['list_types'] = $model->getTypes();
        $var['goods_types'] = Arrays::dataKeyValue(Category::getTypes());
        $var['accs_types'] = Arrays::dataKeyValue(Accompany::getTypes());
        $this->response($this->view->load($this->tmp . '/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Массовое редактирования продукции
     * @Get
     */
    public function editIdsAction($ids, $type_id = Category::TYPE_GOODS)
    {
        $var = array();
        $var['ids'] = $ids;
        $var['type_id'] = $type_id;
        $unit = Unit::model()->select()->sort()->find_all();
        $var['unit'] = Arrays::resultAsArray($unit);
        $vendors = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($vendors);
        $providers = Provider::model()->cached()->sort()->find_all();
        $var['providers'] = Arrays::resultAsArray($providers);
        $currency = Currency::model()->order_pk()->find_all();
        $var['currency'] = Arrays::resultAsArray($currency);
        $var['goods_types'] = Arrays::dataKeyValue(Category::getTypes());
        $var['accs_types'] = Arrays::dataKeyValue(Accompany::getTypes());
        $this->response($this->view->load($this->tmp . '/edit_ids', $var));
    }


    /**
     * @Post
     */
    public function saveIdsDataAction()
    {
        $post = $this->httpRequest->getPost();
        $arrGoods = Goods::model()->where('goods_id', 'IN', explode(',', $post['ids']))->find_all();
        foreach ($arrGoods as $goods) {
            try {
                $goods->values($post['goods']);
                $goods->moder = 0;
                $goods->save(true);

                #meta
                $meta = $goods->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);

                #sections
                foreach ($post['sections'] as $section) {
                    $goods->addSection($section);
                }

                #chara_goods
                foreach ($post['chara_goods'] as $chara) {
                    $goods->addCharacteristics($chara);
                }

                #attributes
                foreach ($post['attributes'] as $attr) {
                    $goods->addAttribute($attr);
                }

            } catch (OrmValidationError $e) {
                $result = array('errors' => $e->getErrorsMessage());
            }

        }

        if (!sizeof($result))
            $result = array(
                'ok' => _t('CMS:Admin', 'These modified') . ' ID:' . $post['ids'],
            );

        $this->response($result);

    }

    /**
     * @Post(parser=false)
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $goods = new Goods($post['goods'][Goods::model()->primary_key()]);
            $goods->values($post['goods']);
            $goods->moder = 0;
            $register = $this->register;
            $goods->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Товар добавлен/изменен: id=[id]',
                    $orm
                );
            };

            $goods->save(true);

            #meta
            if (count($post['meta'])) {
                $meta = $goods->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            #sections
            if (count($post['sections']))
                foreach ($post['sections'] as $section) {
                    $goods->addSection($section);
                }

            #chara_goods
            if (count($post['chara_goods']))
                foreach ($post['chara_goods'] as $chara) {
                    $goods->addCharacteristics($chara);
                }

            #attributes
            if (count($post['attributes']))
                foreach ($post['attributes'] as $attr) {
                    $goods->addAttribute($attr);
                }

            #types
            if (count($post['types']))
                foreach ($post['types'] as $type) {
                    $goods->setType($type['id'], $type['status'] ? false : true);
                }

            #multiCats
            if ($goods->isMulti()) {
                if (count($post['cats']))
                    foreach ($post['cats'] as $cat) {
                        $goods->setCat($cat);
                    }
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'goods' => $goods->as_array(),
                'chara_goods' => Arrays::resultAsArray($goods->getValueCharacteristics()),
                'attributes' => Arrays::resultAsArray($goods->getAttributes()),
                'sections' => Arrays::resultAsArray($goods->getSections()),
                'cats' => $goods->isMulti() ? Arrays::resultAsArray($goods->getCatIds()) : array(),
                'chara' => $this->getChara(),
                'chara_values' => $this->getCharaValue(),
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);

    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $goodsDel = new Goods($post['id']);
        if ($goodsDel->loaded()) {

            $register = $this->register;
            $goodsDel->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Товар удален: id=[id]',
                    $orm
                );
            };

            $goodsDel->delete(true);
        }
        $result['ok'] = _t('CMS:Admin', 'These modified');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteAccoDataAction()
    {
        $post = $this->httpRequest->getPost();
        $acco = new Accompany($post['id']);
        if ($acco->loaded()) {
            $acco->delete(true);
        }
        $this->response($post);
    }

    /**
     * @Post
     */
    public function changePosDataAction()
    {

        $post = $this->httpRequest->getPost();
        $goods = new Goods($post['id']);
        if ($goods->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $goods->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $goods->pos++;
                } else if ($post['type'] == 'down') {
                    $goods->pos--;
                }
                $goods->save(true);

                $goods = Goods::model()->sort();
                if ($this->category->loaded()) {
                    $goods->whereCatId($this->category->pk());
                }

                $pagination = PaginationBuilder::factory($goods)
                    ->setItemCount(false)
                    ->setPage((int)$post['page'])
                    ->setItemsPerPage(ADMIN_PER_PAGE);
                $result['goods'] = Arrays::resultAsArray($pagination->result());
                $result['ok'] = _t('CMS:Admin', 'These modified');


            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function changeAccoDataAction()
    {

        $post = $this->httpRequest->getPost();
        $acco = new Accompany($post['id']);
        if ($acco->loaded()) {
            try {
                $acco->values($post);
                $acco->save(true);
                $accos = Accompany::model()
                    ->where('current_id', '=', $acco->current_id)
                    ->sort()
                    ->find_all();
                $result['goods_accompanies'] = Arrays::resultAsArray($accos);
                $result['ok'] = _t('CMS:Admin', 'These modified');
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Goods)
     */
    public function copyGoodsDataAction(Goods $model)
    {

        try {

            $value = $model->as_array();
            unset($value[$model->primary_key()]);

            #goods
            $goods = new Goods();
            $goods->values($value);
            $goods->name = $goods->name . ' (копия)';
            $goods->save(true);

            #Meta
            $goods_meta = $goods->getMeta();
            $model_meta = $model->getMeta()->as_array();
            $goods_meta->values($model_meta, array('title', 'keys', 'desc', 'redirect'));
            $goods_meta->save();

            #Section
            foreach ($model->getSections() as $section) {
                $arr = $section->as_array();
                unset($arr[$section->primary_key()]);
                $goods->addSection($arr);
            }

            #Characteristics
            foreach ($model->getValueCharacteristics() as $chara) {
                $arr = $chara->as_array();
                unset($arr[$chara->primary_key()]);
                $goods->addCharacteristics($arr);
            }

            #Attribute
            foreach ($model->getAttributes() as $attr) {
                $arr = $attr->as_array();
                unset($arr[$attr->primary_key()]);
                $goods->addAttribute($arr);
            }


            $result['ok'] = 'Товор скопирован';
            $result['url'] = link_to('admin_goods', array('action' => 'edit', 'id' => $goods->pk()));

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function selectEditDataAction()
    {
        $post = $this->httpRequest->getPost();

        switch ($post['action']) {
            case 'delete':
                $goods = Goods::model()->where('goods_id', 'IN', $post['ids'])->find_all();
                foreach ($goods as $item) {
                    $item->delete();
                }
                break;
            case 'active':
            case 'deactivate':
                $goods = Goods::model()->where('goods_id', 'IN', $post['ids'])->find_all();
                foreach ($goods as $item) {
                    $item->status = ($post['action'] == 'active') ? 1 : 0;
                    $item->save();
                }
                break;
        }
        Goods::model()->cache_delete();
        $this->response(array('ok' => 1));
    }

    public function goodsListDataAction()
    {
        $var['type_id'] = $this->httpRequest->getRequest('type_id', Category::TYPE_GOODS);
        $var['cid'] = $this->httpRequest->getRequest('cid', null);
        $var['option'] = $this->httpRequest->getRequest('option', null);
        $var['select'] = $this->httpRequest->getRequest('select', 'selectGoods');
        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $this->response($this->view->load($this->tmp . '/_select_goods', $var));
    }

    public function goodsListAdsDataAction()
    {
        $var['type_id'] = $this->httpRequest->getRequest('type_id', Category::TYPE_GOODS);
        $var['cid'] = $this->httpRequest->getRequest('cid', null);
        $var['gid'] = $this->httpRequest->getRequest('gid', null);
        $var['option'] = $this->httpRequest->getRequest('option', null);
        $var['select'] = $this->httpRequest->getRequest('select', 'selectGoodsAds');
        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $this->response($this->view->load($this->tmp . '/_select_goods_ads', $var));
    }

    /**
     * @Post
     */
    public function copyCharaDataAction()
    {
        $goods = $this->httpRequest->getRequest('goods');
        $value = $this->httpRequest->getRequest('value');

        try {
            $product = new Goods($goods['goods_id']);
            if (!$product->loaded()) {
                throw new Error('Нет такого товара');
            }

            $result['chara_goods'] = array();
            foreach ($product->getValueCharacteristics() as $chara) {
                $arr = array(
                    'character_id' => $chara->character_id,
                    'value_id' => $value ? $chara->value_id : 0
                );
                $result['chara_goods'][] = $arr;
            }

            $result['ok'] = 1;

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function copyGoodsAdsDataAction()
    {
        $goods = $this->httpRequest->getRequest('goods');
        $form = $this->httpRequest->getRequest('form');


        $copy_goods = new Goods($form['gid']);
        if (!$copy_goods->loaded()) {
            return;
        }
        $copy_accompanies = Accompany::model()->select()->where('current_id', '=', $copy_goods->pk())->sort()->find_all();
        $copy_arr = array();
        foreach ($copy_accompanies as $accompany) {
            $copy_arr[$accompany['target_id']] = $accompany;
        }

        $goods = new Goods($goods['goods_id']);
        if (!$goods->loaded()) {
            return;
        }

        $accompanies = Accompany::model()->select()->where('current_id', '=', $goods->pk())->sort()->find_all();
        $arr = array();
        foreach ($accompanies as $acco) {
            $arr[$acco['target_id']] = $acco;
        }

        $add_arr = array();
        foreach ($copy_arr as $target_id => $item) {
            if (!isset($arr[$target_id])) {
                $add_arr[] = $item;
            }
        }


        if (count($add_arr)) {
            foreach ($add_arr as $item) {
                $acco = new Accompany();
                $acco->target_id = $item['target_id'];
                $acco->current_id = $goods->pk();
                $acco->type_id = $item['type_id'];
                $acco->pos = $item['pos'];
                $acco->save();
            }

        }

        $this->response(array(
            'goods' => $goods->as_array(),
            'form' => $form,
            'copy_arr' => $copy_arr,
            'arr' => $arr,
            'add_arr' => $add_arr,
        ));
    }


    /**
     * @Post
     */
    public function copyGoodsCatsAdsDataAction()
    {
        $form = $this->httpRequest->getRequest('form');

        #copy
        $copy_goods = new Goods($form['gid']);
        if (!$copy_goods->loaded()) {
            $this->response(array(
                'form' => $form,
                'error' => 'Not goods'

            ));
            return;
        }
        $copy_accompanies = Accompany::model()->select()->where('current_id', '=', $copy_goods->pk())->sort()->find_all();
        $copy_arr = array();
        foreach ($copy_accompanies as $accompany) {
            $copy_arr[$accompany['target_id']] = $accompany;
        }
        #copy_end

        $category = new Category($form['cid']);
        if (!$category->loaded()) {
            $this->response(array(
                'form' => $form,
                'error' => 'Not category'

            ));
            return;
        }

        #cats
        $idsCat = array();
        $idsCat[] = $category->pk();
        $category->set_active_cats(false);
        $res = $category->getChildren();
        foreach ($res as $cat) {
            $idsCat[] = $cat['cid'];
        }


        $goods = Goods::model()
            ->select('goods_id')
            ->where('cid', 'IN', $idsCat)
            ->find_all();

        $gids = array();
        foreach ($goods as $good) {
            $gids[] = $good['goods_id'];
        }

        if (count($gids) == 0) {
            $this->response(array(
                'form' => $form,
                'error' => 'Not goods in cats'

            ));
            return;
        }

        $accompanies = Accompany::model()->select()->where('current_id', 'in', $gids)->find_all();
        $arr = array();
        foreach ($accompanies as $acco) {
            $arr[$acco['current_id']][$acco['target_id']] = $acco;
        }
        #cats_end


        foreach ($goods as $good) {
            $good['goods_id'];
            $current_arr = $arr[$good['goods_id']];

            $add_arr = array();
            if (count($current_arr)) {
                foreach ($copy_arr as $target_id => $item) {
                    if (!isset($current_arr[$target_id])) {
                        $add_arr[] = $item;
                    }
                }
            } else {
                $add_arr = $copy_arr;
            }


            if (count($add_arr)) {
                foreach ($add_arr as $item) {
                    $acco = new Accompany();
                    $acco->target_id = $item['target_id'];
                    $acco->current_id = $good['goods_id'];
                    $acco->type_id = $item['type_id'];
                    $acco->pos = $item['pos'];
                    $acco->save();
                }

            }

        }


        $this->response(array(
            'form' => $form,
            '$copy_arr' => $copy_arr,
            '$arr' => $arr,
            '$idsCat' => $idsCat,
            'count goods' => count($gids)

        ));


        return;


        $goods = new Goods($goods['goods_id']);
        if (!$goods->loaded()) {
            return;
        }

        $accompanies = Accompany::model()->select()->where('current_id', '=', $goods->pk())->sort()->find_all();
        $arr = array();
        foreach ($accompanies as $acco) {
            $arr[$acco['target_id']] = $acco;
        }

        $add_arr = array();
        foreach ($copy_arr as $target_id => $item) {
            if (!isset($arr[$target_id])) {
                $add_arr[] = $item;
            }
        }


        if (count($add_arr)) {
            foreach ($add_arr as $item) {
                $acco = new Accompany();
                $acco->target_id = $item['target_id'];
                $acco->current_id = $goods->pk();
                $acco->type_id = $item['type_id'];
                $acco->pos = $item['pos'];
                $acco->save();
            }

        }

        $this->response(array(
            'goods' => $goods->as_array(),
            'form' => $form,
            'copy_arr' => $copy_arr,
            'arr' => $arr,
            'add_arr' => $add_arr,
        ));
    }

    protected function getChara()
    {
        $groups = Arrays::resultAsArrayKey(CharacteristicsGroup::model()
            ->select('group_id', 'name')
            ->cached()
            ->find_all(),
            'group_id');
        $chara = Characteristics::model()->select('name', 'character_id', 'group_id')->cached()->sort()->find_all();
        $result = array();
        foreach ($chara as $item) {
            $item['group'] = isset($groups[$item['group_id']]) ? $groups[$item['group_id']]['name'] . ' => ' : '';
            $result[] = $item;
        }
        return $result;
    }

    protected function getCharaValue()
    {
        $units = Arrays::resultAsArrayKey(
            Unit::model()
                ->select('abbr', 'unit_id')
                ->cached()
                ->find_all(),
            'unit_id');
        $value = CharacteristicsValues::model()
            ->select('value_id', 'character_id', 'name', 'unit_id')
            ->cached()
            ->sort()
            ->find_all();
        $result = array();
        foreach ($value as $item) {
            $item['unit'] = isset($units[$item['unit_id']]) ? $units[$item['unit_id']]['abbr'] : '';
            $result[$item['character_id']][] = $item;
        }
        return $result;
    }

    /**
     *
     */
    public function getCharaDataAction()
    {
        $result['chara'] = $this->getChara();
        $result['chara_values'] = $this->getCharaValue();

        $this->response($result);
    }

}