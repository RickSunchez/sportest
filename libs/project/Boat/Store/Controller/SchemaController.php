<?php

namespace Boat\Store\Controller;

use Boat\Core\Entity\Note;
use Boat\Core\Entity\NoteItem;
use Boat\Core\Entity\Schema;
use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Vendor;
use Shop\Commodity\Helpers\Options;

class SchemaController extends Controller
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

    /**
     * @var \Location\Core\Model\CitiesBuilder
     * @service city
     * @inject
     */
    public $city;


    /**
     * @param $url
     * @Model(name=Boat\Core\Entity\Schema)
     */
    public function indexAction(Schema $model, $url)
    {

//        load_or_404($model);

        #corrections url
        if ($model->url != $url) {
            $this->httpResponse->redirect(
                link_to_city('schema_index', array('id' => $model->pk(), 'url' => $model->url)),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $var['schema'] = $model;

        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {
                $this->setBreadCrumbsParents($category, true);
            }
        }

        if ($model->vid) {
            $vendor = new Vendor($model->vid);
            if ($vendor->loaded()) {
                $var['vendor'] = $vendor;
                $vendor_name = $vendor->name;
            }
        }
        #breadCrumbs
        $this->breadCrumbs->setLastItem($model->name);
        $this->setMeta($model->getMeta(), array(
            'title' => ($vendor_name ? $vendor_name : '') . ' ' . $model->name,
            'property' => array(
                'og:title' => ($vendor_name ? $vendor_name : '') . ' ' . $model->name,
            )
        ));
        #breadCrumbs end


        $items = Note::model()
            ->where('sid', '=', $model->pk())
            ->sort()
            ->find_all();

        $ids = $var['notes'] = array();
        foreach ($items as $item) {
            $ids[] = $item->pk();
            $var['notes'][] = $item;
        }

        if (count($ids))
            $var['images'] = $this->getImages($ids);

        $this->response($this->view->load('boat/schema/index', $var));
    }

    /**
     * @param $url
     * @Model(name=Boat\Core\Entity\Note)
     */
    public function noteAction(Note $model, $url)
    {
//        load_or_404($model);

        #corrections url
        if ($model->url != $url) {
            $this->httpResponse->redirect(
                link_to_city('schema_note', array('id' => $model->pk(), 'url' => $model->url)),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $var['schema'] = $schema = new Schema($model->sid);
//        if (!$schema->loaded()) {
//            throw new NotFound('Каталог не найден');
//        }

        if ($schema->cid) {
            $var['category'] = $category = new Category($schema->cid);
            if ($category->loaded()) {
                $this->setBreadCrumbsParents($category, true);
            }
        }

        if ($schema->vid) {
            $vendor = new Vendor($schema->vid);
            if ($vendor->loaded()) {
                $var['vendor'] = $vendor;
            }
        }

        #breadCrumbs
        $city_url = $this->city->getUrl();

        if ($this->city->isDefault()) {
            $str = 'default_schema_index?url={0}&id={1}';
        } else {
            $str = 'schema_index?url={0}&id={1}&city_url={2}';
        }

        $this->breadCrumbs->addLink($schema->name,
            _sf(
                $str, $schema->url, $schema->pk(), $city_url
            ));
        $this->breadCrumbs->setLastItem($model->name);
        $this->setMeta($model->getMeta(), array(
            'title' => $model->name,
            'property' => array(
                'og:title' => $model->name,
            )
        ));
        #breadCrumbs end


        $var['note'] = $model;
        $var['image'] = $model->getImage();

        $items = NoteItem::model()
            ->where('nid', '=', $model->pk())
            ->sort()
            ->active()
            ->find_all();

        $pids = $var['items'] = array();
        foreach ($items as $item) {
            $pids[] = $item->pid;
            $var['items'][] = $item;
        }

        if (count($pids)) {
            $var['products'] = array();
            $products = Goods::model()->where('goods_id', 'in', $pids)->find_all();
            foreach ($products as $item) {
                $var['products'][$item->pk()] = $item;
            }
            Options::acceptFirstVariantsByProducts($var['products'], $pids, false);
        }

        $this->response($this->view->load('boat/schema/note', $var));
    }

    public function liPartial($pid)
    {

        $scheme = Schema::model()
            ->where('pid', '=', $pid)
            ->active()
            ->find();

        if ($scheme->loaded()) {
            $var['scheme'] = $scheme;
            $this->response($this->view->load('boat/schema/_li', $var));
        }
    }

    public function rightPartial($pid)
    {

        $items = NoteItem::model()
            ->where('pid', '=', $pid)
            ->active()
            ->sort()
            ->find_all();

        $nds = $var['items'] = array();
        foreach ($items as $item) {
            $nds[] = $item->nid;
            $var['items'][$item->nid][] = $item;
        }

        if (count($nds)) {
            $notes = Note::model()
                ->where('id', 'in', $nds)
                ->active()
                ->sort()
                ->find_all();

            $sds = $var['notes'] = array();
            foreach ($notes as $note) {
                $sds[] = $note->sid;
                $var['notes'][] = $note;
            }


            if (count($sds)) {
                $schemes = Schema::model()
                    ->where('id', 'in', $sds)
                    ->active()
                    ->sort()
                    ->find_all();

                $vds = $var['schemes'] = array();
                foreach ($schemes as $scheme) {
                    $vds[] = $scheme->vid;
                    $var['schemes'][$scheme->pk()] = $scheme;
                }

                if (count($vds)) {
                    $vendors = Vendor::model()
                        ->where('vendor_id', 'in', $vds)
                        ->find_all();
                    $var['vendors'] = array();
                    foreach ($vendors as $vendor) {
                        $var['vendors'][$vendor->pk()] = $vendor;
                    }
                }
            }
        }

        $this->response($this->view->load('boat/schema/_right', $var));
    }

    public function notePartial($cid)
    {
        $schemes = Schema::model()
            ->where('cid', '=', $cid)
            ->sort()
            ->active()
            ->find_all();
        if (count($schemes)) {
            $ids = array();
            foreach ($schemes as $scheme) {
                $ids[] = $scheme->pk();
                $var['schemes'][] = $scheme;
            }

            if (count($ids))
                $var['images'] = $this->getImagesSchema($ids);

            $this->response($this->view->load('boat/schema/_list', $var));
        }
    }

    protected function getImages($targetId = null)
    {
        $images = Image::model()->whereByTargetType(Note::model())->cached();
        if ($targetId) {
            $images->whereByTargetId($targetId);
        }
        $image = Arrays::resultAsArrayKey($images->find_all(), 'target_id');
        return $image;
    }

    protected function getImagesSchema($targetId = null)
    {
        $images = Image::model()->whereByTargetType(Schema::model())->cached();
        if ($targetId) {
            $images->whereByTargetId($targetId);
        }
        $image = Arrays::resultAsArrayKey($images->find_all(), 'target_id');
        return $image;
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

}