<?php
namespace CMS\Core\Controller;

use CMS\Catalog\Entity\Category;
use CMS\Core\Entity\Gallery;
use CMS\Core\Entity\Image;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

class GalleryController extends Controller
{

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var array
     */
    protected $config = array();

    /** @var int */
    protected $perPage;

    public function before()
    {
        $this->config = $this->container->getParameters('cms.gallery');
        $this->perPage = $this->config['page'];
        if ($this->config['layout'])
            $this->layout($this->config['layout']);
    }

    public function indexAction()
    {
        if ($this->config['first']['name']) {
            $this->breadCrumbs->setLastItem(
                $this->config['first']['name']
            );
        }

        $categories = Category::model()
            ->where('type_id', '=', Category::TYPE_GALLERY)
            ->active()
            ->sort()
            ->find_all();
        $ids = $var['categories'] = array();
        foreach ($categories as $item) {
            $var['categories'][] = $item;
            $ids[] = $item->pk();
        }

        if (count($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Category::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }
        $this->response($this->view->load('cms/gallery/index', $var));
    }

    /**
     * @Model(name=CMS\Catalog\Entity\Category,field=cid,loaded=false)
     */
    public function listAction(Category $model, $page)
    {
        $var = array();
        $galleries = Gallery::model()->active()->sort();

        if (Helpers::isMultiDomain()) {
            $galleries->where('site', '=', Helpers::getCurrentDomain());
        }

        if ($model->loaded()) {
            if ($this->config['first']['name'] && count($this->config['first']['router'])) {
                $this->breadCrumbs->addLink(
                    $this->config['first']['name'],
                    link_to_array($this->config['first']['router']),
                    $this->config['first']['name'],
                    false
                );
            }
            $prefix = $model->prefix;
            $ids = array();
            $res = $model->getChildren();
            foreach ($res as $cat) {
                $ids[] = $cat['cid'];
            }
            $ids[] = $model->pk();
            $galleries->where('cid', 'IN', $ids);
            $categories = $this->setBreadCrumbsParents($model, true, true);
            $var['categories'] = $categories;
            $var['category'] = $model;
            $this->setSite('galleryCategoryId', $model->pk());

        } else {

            if ($this->config['first']['name']) {
                $this->breadCrumbs->setLastItem(
                    $this->config['first']['name']
                );
            }

        }

        $pagination = PaginationBuilder::factory($galleries)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrGalleries = $pagination->result();
        $var['galleries'] = $ids = array();
        foreach ($arrGalleries as $gallery) {
            $ids[] = $gallery->pk();
            $var['galleries'][] = $gallery;
        }
        $var['pagination'] = $pagination;
        if (count($ids)) {
            $images = Image::model()
                ->main(true)
                ->whereByTargetId($ids)
                ->whereByTargetType(Gallery::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $meta = $model->loaded() ? $model->getMeta() : null;
        $this->setMeta($meta, array(
            'title' => $model->loaded() ? $model->name : $this->config['first']['name'],
            'property' => array(
                'og:image' => $model->loaded() ? $model->getImage()->preview : '',
                'og:title' => $model->loaded() ? $model->name : $this->config['first']['name'],
                'og:description' => $model->loaded() ? $model->description : ''
            )
        ));

        $theme = $prefix ? '_' . $prefix : '';
        $this->response($this->view->load('cms/gallery/list' . $theme, $var));
    }

    /**
     * @Model(name=CMS\Core\Entity\Gallery)
     */
    public function showAction(Gallery $model)
    {
        load_or_404($model);

        if (Helpers::isMultiDomain()) {
            $site = Helpers::getCurrentDomain();
            if ($model->site != $site) {
                $this->httpResponse->redirect(
                    $model->link(),
                    Response::S301_MOVED_PERMANENTLY
                );
                exit;
            }
        }

        $this->setSite('galleryId', $model->pk());
        $this->breadCrumbs->setLastItem($model->name);
        $var['images'] = $model->getImages();
        $var['gallery'] = $model;

        if ($this->config['first']['name'] && count($this->config['first']['router'])) {
            $this->breadCrumbs->addLink(
                $this->config['first']['name'],
                link_to_array($this->config['first']['router']),
                $this->config['first']['name'],
                false
            );
        }

        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {
                $this->setBreadCrumbsParents($category, true);
                $this->setSite('galleryCategoryId', $category->pk());
                $var['category'] = $category;
            }
        }

        $this->setMeta(null, array(
            'desc' => $model->note,
            'title' => $model->name,
            'property' => array(
                'og:image' => $model->getMainImage()->normal,
                'og:title' => $model->name,
                'og:description' => $model->note
            )
        ));

        $this->response($this->view->load('cms/gallery/show', $var));
    }

    /**
     * @Model(name=CMS\Core\Entity\Gallery)
     */
    public function imageAction(Gallery $model, $image_id = null)
    {
        $this->setSite('galleryId', $model->pk());

        $image = Image::model()->whereByTargetId($model->pk())
            ->whereByTargetType($model)
            ->where('image_id', '=', $image_id)
            ->find();

        if (!$image->loaded()) {
            throw new NotFound('Изображение не найдено');
        }
        $this->breadCrumbs->setLastItem($image->name ? $image->name : '#' . $image->pk());
        $var['image'] = $image;

        if ($this->config['first']['name'] && count($this->config['first']['router'])) {
            $this->breadCrumbs->addLink(
                $this->config['first']['name'],
                link_to_array($this->config['first']['router']),
                $this->config['first']['name'],
                false
            );
        }

        if ($model->cid) {
            $category = new Category($model->cid);
            if ($category->loaded()) {
                $this->setBreadCrumbsParents($category, true);
                $this->setSite('galleryCategoryId', $category->pk());
                $var['category'] = $category;
            }
        }

        $this->breadCrumbs->addLink($model->name, $model->link(), null, false);
        $this->getHeader()->addTitle($model->name);

        if ($image->name) {
            $this->getHeader()->addTitle($image->name);
        } else {
            $this->getHeader()->addTitle('#' . $image->pk());
        }

        $this->setMeta(null, array(
            'property' => array(
                'og:image' => $image->normal,
                'og:title' => $image->name ? $image->name : $image->pk(),
                'og:description' => $image->text
            )
        ));

        $this->response($this->view->load('cms/gallery/image', $var));
    }

    /**
     * @param int $limit
     * @param null $theme
     * @throws \Delorius\Exception\Error
     */
    public function listPartial($limit = 3, $theme = null)
    {
        $galleries = Gallery::model()
            ->active()
            ->sort()
            ->cached()
            ->limit($limit);

        if (Helpers::isMultiDomain()) {
            $galleries->where('site', '=', Helpers::getCurrentDomain());
        }
        $result = $galleries->find_all();
        $var['galleries'] = $ids = array();
        foreach ($result as $item) {
            $var['galleries'][] = $item;
            $ids[] = $item->pk();
        }

        if (count($ids)) {
            $images = Image::model()
                ->main(true)
                ->whereByTargetId($ids)
                ->whereByTargetType(Gallery::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }

        $theme = $theme ? '_' . $theme : null;
        $this->response($this->view->load('cms/gallery/_list' . $theme, $var));
    }


    /**
     * @param null $theme
     * @throws \Delorius\Exception\Error
     */
    public function categoriesPartial($theme = null)
    {
        $categories = Category::model()
            ->type(Category::TYPE_GALLERY)
            ->sort()
            ->active()
            ->select(array('cid', 'id'), 'url', 'pid', 'name', 'object', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = array();
        foreach ($categories as $cat) {
            $cat['link'] = link_to('gallery_category', array('cid' => $cat['id'], 'url' => $cat['url']));
            $var['categories'][$cat['pid']][] = $cat;
        }
        $var['selfCategoryId'] = $this->getSite('galleryCategoryId');
        $var['menu_id'] = 0;

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/gallery/_categories' . $theme, $var));
    }

    /**
     * @param Category $category
     * @param bool|false $self
     * @return string
     */
    protected function setBreadCrumbsParents(Category $category, $self = false, $last = false)
    {
        $categories = '';
        $parentCategory = $category->getParents();
        if ($parentCategory) {
            $reverse = array_reverse($parentCategory);
            foreach ($reverse as $cat) {
                $categories .= _sf(' {0} / ', $cat['name']);
                $this->breadCrumbs->addLink(
                    $cat['name'],
                    _sf(
                        'gallery_category?url={0}&cid={1}', $cat['url'], $cat['cid']
                    )
                );
            }
        }

        if ($self) {
            $categories .= _sf(' {0} / ', $category->name);
            if ($last) {
                $this->breadCrumbs->setLastItem($category->name);
            } else {
                $this->breadCrumbs->addLink(
                    $category->name,
                    _sf(
                        'gallery_category?url={0}&cid={1}', $category->url, $category->pk()
                    )
                );
            }
        }

        return $categories;
    }
}