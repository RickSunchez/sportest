<?php

namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Collection;
use Shop\Commodity\Entity\Vendor;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Коллекции #admin_collection?action=list
 */
class CollectionController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $collections = Collection::model()->sort();
        $get = $this->httpRequest->getQuery();

        if (isset($get['cid'])) {

            $category = Category::model($get['cid']);

            $idsCat = array();
            $idsCat[] = $category->pk();
            foreach ($category->getChildren() as $cat) {
                $idsCat[] = $cat->pk();
            }
            $collections->where('cid', 'in', $idsCat);
        }

        if (isset($get['name'])) {
            $aQuery = Strings::parserKeywords($get['name'], 3);
            $collections->where_open();
            foreach ($aQuery as $q) {
                $collections->or_where('name', 'like', '%' . $q . '%');
            }
            $collections->where_close();
        }

        $pagination = PaginationBuilder::factory($collections)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_collection');

        $ids = $var['collections'] = $var['images'] = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $var['collections'][] = $item->as_array();
            $ids[] = $item->pk();
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Collection::model())
                ->where('main', '=', 1);
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $categories = Category::model()
            ->select('cid', 'name')
            ->find_all();
        $var['collection_categories'] = Arrays::resultAsArray($categories, false);
        $this->response($this->view->load('shop/collection/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Создать коллекцию
     */
    public function addAction()
    {
        $collections = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($collections);
        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $var['reload'] = 1;
        $this->response($this->view->load('shop/collection/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать коллекцию
     * @Model(name=Shop\Commodity\Entity\Collection)
     */
    public function editAction(Collection $model)
    {
        $var['collection'] = $model->as_array();
        $var['type_id'] = $model->ctype;
        $var['meta'] = $model->getMeta()->as_array();
        $var['images'] = Arrays::resultAsArray($model->getImages());
        $var['attributes'] = Arrays::resultAsArray($model->getAttributes());
        $var['packages'] = Arrays::resultAsArray($model->getPackages());
        $var['packages_goods'] = Arrays::resultAsArray($model->getPackageGoods());
        $var['goods'] = Arrays::resultAsArray($model->getGoods());
        $collections = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($collections);
        $var['types'] = Arrays::dataKeyValue(Category::getTypes());
        $var['reload'] = 0;
        $this->response($this->view->load('shop/collection/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $collection = new Collection($post['collection'][Collection::model()->primary_key()]);
            $collection->values($post['collection']);
            $collection->save(true);

            #meta
            if (count($post['meta'])) {
                $meta = $collection->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            #attributes
            foreach ($post['attributes'] as $attr) {
                $collection->addAttribute($attr);
            }

            #packages
            foreach ($post['packages'] as $pack) {
                $collection->addPackage($pack);
            }

            #packages_goods
            foreach ($post['packages_goods'] as $pack) {
                $collection->addGoods($pack);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'collection' => $collection->as_array(),
                'attributes' => Arrays::resultAsArray($collection->getAttributes()),
                'packages' => Arrays::resultAsArray($collection->getPackages()),
                'packages_goods' => Arrays::resultAsArray($collection->getPackageGoods()),
                'goods' => Arrays::resultAsArray($collection->getGoods())
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Vendor)
     */
    public function deleteDataAction(Vendor $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }

}