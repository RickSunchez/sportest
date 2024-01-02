<?php

namespace CMS\Core\Controller;

use CMS\Catalog\Entity\Category;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Video;
use CMS\Core\Entity\Tags;
use CMS\Core\Entity\TagsObject;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\View\Html;

class VideoController extends Controller
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
        $this->config = $this->container->getParameters('cms.video');
        $this->perPage = $this->config['page'];
        if ($this->config['layout'])
            $this->layout($this->config['layout']);
    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\Video)
     */
    public function showAction(Video $model)
    {
        load_or_404($model);

        $this->setSite('videoId', $model->pk());

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
                $this->setSite('videoCategoryId', $category->pk());
                $var['category'] = $category;
            }
        }

        $var['image'] = $image = $model->getImage();
        $meta = $model->getMeta();
        $this->setMeta($meta, array(
            'desc' => $model->text,
            'title' => $model->name,
            'property' => array(
                'og:title' => $model->name,
                'og:description' => $model->text,
                'og:image' => $image->normal,
                'og:video' => $this->urlScript->getAbsoluteUrlNoQuery(),
            )
        ));

        if ($meta->title) {
            $this->breadCrumbs->setLastItem($meta->title);
        } else {
            $this->breadCrumbs->setLastItem($model->name);
        }

        $var['video'] = $model;
        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load('cms/video/show' . $theme, $var));
        $this->lastModified($model->date_edit ? $model->date_edit : $model->date_cr);
    }

    /**
     * @Model(name=CMS\Catalog\Entity\Category,field=cid,loaded=false)
     */
    public function listAction(Category $model, $page, $tag = null)
    {
        $video = Video::model()
            ->active()
            ->sort();

        if (Helpers::isMultiDomain()) {
            $video->where('site', '=', Helpers::getCurrentDomain());
        }

        if ($model->loaded() || $tag) {
            if ($this->config['first']['name'] && count($this->config['first']['router'])) {
                $this->breadCrumbs->addLink(
                    $this->config['first']['name'],
                    link_to_array($this->config['first']['router']),
                    $this->config['first']['name'],
                    false
                );
            }

            if ($model->loaded()) {
                $prefix = $model->prefix;
                $ids = array();
                $cats = $model->getChildren();
                foreach ($cats as $cat) {
                    $ids[] = $cat['cid'];
                }
                $ids[] = $model->pk();
                $video->where('cid', 'IN', $ids);
                $categories = $this->setBreadCrumbsParents($model, true);
                $var['categories'] = $categories;
                $var['category'] = $model;
                $this->setSite('videoCategoryId', $model->pk());

            }

            if ($tag) {
                $orm = Tags::model()
                    ->whereByTargetType($video)
                    ->whereName($tag)
                    ->find();

                if ($orm->loaded()) {

                    $var['tag'] = $orm;
                    $this->breadCrumbs->addLink(
                        $this->config['first']['name'],
                        link_to_array($this->config['first']['router']),
                        $this->config['first']['name'],
                        false
                    );
                    $this->breadCrumbs->setLastItem(
                        '#' . $orm->name
                    );

                    $meta = $orm->getMeta();
                    $this->setMeta($meta, array(
                        'title' => '#' . $orm->name,
                        'property' => array(
                            'og:title' => '#' . $orm->name,
                        )
                    ));
                    $video->whereTagName($tag);

                } else {
                    throw new NotFound('Тег не найден');
                }
            }

        } else {

            if ($this->config['first']['name']) {
                $this->breadCrumbs->setLastItem(
                    $this->config['first']['name']
                );
            }
        }

        $pagination = PaginationBuilder::factory($video)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrVideo = $pagination->result();
        $ids = array();
        $var['videos'] = array();
        foreach ($arrVideo as $item) {
            $var['videos'][] = $item;
            $ids[] = $item->pk();
        }

        $var['images'] = $this->getImages($ids);
        $var['pagination'] = $pagination;

        $meta = $model->loaded() ? $model->getMeta() : null;
        $this->setMeta($meta, array(
            'title' => $model->loaded() ? $model->name : $this->config['first']['name'],
            'property' => array(
                'og:image' => $model->loaded() ? $model->getImage()->normal : '',
                'og:title' => $model->loaded() ? $model->name : $this->config['first']['name'],
                'og:description' => $model->loaded() ? $model->description : ''
            )
        ));

        $theme = $prefix ? '_' . $prefix : '';
        $this->response($this->view->load('cms/video/list' . $theme, $var));
    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\Video)
     */
    public function getAction(Video $model)
    {
        load_or_404($model);
        $this->response(array('url' => $model->url));
    }

    /**
     * @param int $limit
     */
    public function listPartial($limit = 3, $theme = null)
    {
        $videos = Video::model()
            ->active()
            ->sort()
            ->limit($limit);

        if (Helpers::isMultiDomain()) {
            $videos->where('site', '=', Helpers::getCurrentDomain());
        }
        $result = $videos->find_all();
        $ids = array();
        foreach ($result as $item) {
            $var['videos'][] = $item;
            $ids[] = $item->pk();
        }

        $var['images'] = $this->getImages($ids);
        $theme = $theme ? '_' . $theme : null;
        $this->response($this->view->load('cms/video/_list' . $theme, $var));
    }

    public function tagsPartial($limit = null)
    {
        $tags = Tags::model()->sort();
        if ($limit) {
            $tags->limit($limit);
        }
        $tagsObject = new TagsObject();
        $tags = $tags->join($tagsObject->table_name(), 'inner')
            ->on($tags->table_name() . '.tag_id', '=', $tagsObject->table_name() . '.tag_id')
            ->where($tagsObject->table_name() . '.target_type', '=', Video::model()->table_name())
            ->group_by($tags->table_name() . '.tag_id');

        if ($cid = $this->getSite('videoCategoryId')) {
            $tags->where($tagsObject->table_name() . '.option', '=', $cid);
        }

        $var['tags'] = $tags->find_all();

        $this->response($this->view->load('cms/video/_tags', $var));
    }

    /**
     * Показать случайны видео из этой же категори,
     * кроме указаного видео
     * @param $cid
     * @param int $limit
     * @param null $videoId товар который стоит сключить из показа
     * @param null $theme
     * @throws \Delorius\Exception\Error
     */
    public function inRndPartial($limit = 4, $cid = null, $videoId = null, $theme = null)
    {
        $videos = Video::model()
            ->active()
            ->limit($limit)
            ->order_by(DB::expr('RAND()'));

        if ($cid) {
            $videos->where('cid', '=', $cid);
        }

        if ($videoId) {
            $videos->where('id', '<>', $videoId);
        }

        $result = $videos->find_all();
        $ids = $var['videos'] = array();
        foreach ($result as $item) {
            $ids[] = $item->pk();
            $var['videos'][] = $item;
        }
        $var['images'] = $this->getImages($ids);
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/video/_in_list_rnd' . $theme, $var));
    }

    protected function getImages($targetId = null)
    {
        $images = Image::model()->whereByTargetType(Video::model())->cached();
        if ($targetId) {
            $images->whereByTargetId($targetId);
        }
        $image = Arrays::resultAsArrayKey($images->find_all(), 'target_id');
        return $image;
    }

    public function categoriesPartial($theme = null)
    {
        $categories = Category::model()
            ->type(Category::TYPE_VIDEO)
            ->sort()
            ->active()
            ->select(array('cid', 'id'), 'url', 'pid', 'name', 'object', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = array();
        foreach ($categories as $cat) {
            $cat['link'] = link_to('video_category', array('cid' => $cat['id'], 'url' => $cat['url']));
            $var['categories'][$cat['pid']][] = $cat;
        }
        $var['selfCategoryId'] = $this->getSite('videoCategoryId');
        $var['menu_id'] = 0;

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/video/_categories' . $theme, $var));
    }

    /**
     * @param Category $category
     * @param bool $self
     */
    protected function setBreadCrumbsParents(Category $category, $self = false)
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
                        'video_category?url={0}&cid={1}', $cat['url'], $cat['cid']
                    )
                );
            }
        }

        if ($self) {
            $categories .= _sf(' {0} / ', $category->name);
            $this->breadCrumbs->addLink(
                $category->name,
                _sf(
                    'video_category?url={0}&cid={1}', $category->url, $category->pk()
                )
            );
        }

        return $categories;
    }


}