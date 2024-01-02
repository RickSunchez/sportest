<?php

namespace CMS\Core\Controller;

use CMS\Core\Entity\Image;
use CMS\Core\Entity\Article;
use CMS\Core\Entity\Tags;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use CMS\Catalog\Entity\Category;
use Delorius\Utils\Strings;
use Delorius\View\Html;


class ArticleController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /** @var int */
    protected $perPage;

    /** @var array */
    protected $config = array();

    /**
     * Current category id
     * @var int
     */
    public static $categoryId = 0;

    public function before()
    {
        $this->config = $this->container->getParameters('cms.article');
        $this->perPage = $this->config['page'];
        if (!$this->isViewPartial) {
            if ($this->config['layout'])
                $this->layout($this->config['layout']);
        }
    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\Article)
     */
    public function indexAction(Article $model, $url)
    {
        load_or_404($model);
        $this->lastModified($model->date_edit ? $model->date_edit : $model->date_cr);
        $this->setSite('articleId', $model->pk());

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

        if ($model->url != $url) {
            $this->httpResponse->redirect(
                $model->link(),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
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
                $categories = $this->setBreadCrumbsParents($category, true);
                $this->setSite('articleCategoryId', $category->pk());
                $var['category'] = $category;
                $var['categories'] = $categories;
            }
        }

        $var['image'] = $image = $model->getImage();
        $meta = $model->getMeta();
        $this->setMeta($meta, array(
            'desc' => $model->getPreview(),
            'title' => $model->name,
            'property' => array(
                'og:title' => $model->name,
                'og:description' => $model->getPreview(),
                'og:image' => $image->normal
            )
        ));

        if ($meta->title) {
            $this->breadCrumbs->setLastItem($meta->title);
        } else {
            $this->breadCrumbs->setLastItem($model->name);
        }

        #update Views
        $model->updateViews();
        $model->save();

        $var['article'] = $model;
        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load('cms/article/show' . $theme, $var));
    }

    /**
     * @Model(name=CMS\Catalog\Entity\Category,field=cid,loaded=false)
     */
    public function listAction(Category $model, $url, $page)
    {
        if ($model->loaded() && $model->url != $url) {
            $this->httpResponse->redirect(
                $model->link(),
                Response::S301_MOVED_PERMANENTLY
            );
            exit;
        }

        $articles = Article::model()->active()->sort();

        if (Helpers::isMultiDomain()) {
            $articles->where('site', '=', Helpers::getCurrentDomain());
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
                $ids[] = $cat->pk();
            }
            $ids[] = $model->pk();
            $articles->where('cid', 'IN', $ids);
            $this->setBreadCrumbsParents($model);
            $this->breadCrumbs->setLastItem($model->name);
            $this->setSite('articleCategoryId', $model->pk());
            $var['category'] = $model;

            $meta = $model->getMeta();
            $this->setMeta($meta, array(
                'title' => $model->loaded() ? $model->name : $this->config['first']['name'],
                'desc' => $model->loaded() ? $model->description : '',
                'property' => array(
                    'og:image' => $model->loaded() ? $model->getImage()->normal : '',
                    'og:title' => $model->loaded() ? $model->name : $this->config['first']['name'],
                    'og:description' => $model->loaded() ? $model->description : ''
                )
            ));


        } else {

            if ($this->config['first']['name']) {
                $this->breadCrumbs->setLastItem(
                    $this->config['first']['name']
                );
                $this->setMeta(null, array(
                    'title' => $this->config['first']['name'],
                    'property' => array(
                        'og:title' => $this->config['first']['name'],
                    )
                ));
            }
        }

        $pagination = PaginationBuilder::factory($articles)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrArticle = $pagination->result();
        $ids = array();
        $var['articles'] = array();
        foreach ($arrArticle as $item) {
            $var['articles'][] = $item;
            $ids[] = $item->pk();
        }
        $var['images'] = $this->getImages($ids);
        $var['pagination'] = $pagination;

        $theme = $prefix ? '_' . $prefix : '';
        $this->response($this->view->load('cms/article/list' . $theme, $var));
    }

    /**
     * @param $tag
     * @param $page
     * @throws NotFound
     * @throws \Delorius\Exception\Error
     */
    public function tagAction($tag, $page)
    {
        /** @var Tags $orm */
        $orm = Tags::model()
            ->whereByTargetType(Article::model())
            ->whereUrl($tag)
            ->find();

        if (!$orm->loaded()) {
            throw new NotFound('Тег не найден');
        }

        $var['tag'] = $orm;

        #breadCrumbs
        $this->breadCrumbs->addLink(
            $this->config['first']['name'],
            link_to_array($this->config['first']['router']),
            $this->config['first']['name'],
            false
        );
        $this->breadCrumbs->setLastItem(
            Strings::firstUpper($orm->show)
        );
        #breadCrumbs end

        $meta = $orm->getMeta();
        $this->setMeta($meta, array(
            'title' => $orm->show,
            'desc' => Html::clearTags($orm->text),
            'property' => array(
                'og:title' => $orm->show,
                'og:description' => Html::clearTags($orm->text)
            )
        ));

        $articles = Article::model()->active()->sort();
        if (Helpers::isMultiDomain()) {
            $articles->where('site', '=', Helpers::getCurrentDomain());
        }
        $articles->whereByTag($orm);

        $pagination = PaginationBuilder::factory($articles)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrArticle = $pagination->result();
        $ids = array();
        $var['articles'] = array();
        foreach ($arrArticle as $item) {
            $var['articles'][] = $item;
            $ids[] = $item->pk();
        }
        $var['images'] = $this->getImages($ids);
        $var['pagination'] = $pagination;

        $this->response($this->view->load('cms/article/list_tag', $var));
    }


    public function categoriesPartial()
    {

        $categories = Category::model()
            ->sort()
            ->type(Category::TYPE_ARTICLE)
            ->active()
            ->select('cid', 'url', 'pid', 'name', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = array();

        foreach ($categories as $cat) {
            $cat['link'] = link_to('article_category', array('url' => $cat['url'], 'cid' => $cat['cid']));
            $var['categories'][$cat['pid']][] = $cat;
        }

        $var['selfCategoryId'] = $this->getSite('articleCategoryId');
        $var['category_id'] = 0;

        $this->response($this->view->load('cms/article/_menu', $var));
    }


    public function tagsPartial()
    {
        $tags = Tags::model()->select('name', 'url')->active()->sort()->find_all();

        if (0 == count($tags)) {
            return;
        }

        $var['tags'] = $tags;
        $this->response($this->view->load('cms/article/_tags', $var));
    }

    /**
     * @param int $limit
     */
    public function listPartial($limit = 3, $theme = null)
    {
        $articles = Article::model()
            ->active()
            ->sort()
            ->cached()
            ->limit($limit);

        if (Helpers::isMultiDomain()) {
            $articles->where('site', '=', Helpers::getCurrentDomain());
        }

        $result = $articles->find_all();
        $ids = array();
        foreach ($result as $item) {
            $var['articles'][] = $item;
            $ids[] = $item->pk();
        }

        $var['images'] = $this->getImages($ids);
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/article/_list' . $theme, $var));
    }

    protected function getImages($targetId = null)
    {
        $images = Image::model()->whereByTargetType(Article::model())->cached();
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
        $categories = '';
        $parentCategory = $category->getParents();
        if ($parentCategory) {
            $reverse = array_reverse($parentCategory);
            foreach ($reverse as $cat) {
                $categories .= _sf(' {0} / ', $cat['name']);
                $this->breadCrumbs->addLink(
                    $cat['name'],
                    _sf(
                        'article_category?url={0}&cid={1}', $cat['url'], $cat['cid']
                    )
                );
            }
        }

        if ($self) {
            $categories .= _sf(' {0} / ', $category->name);
            $this->breadCrumbs->addLink(
                $category->name,
                _sf(
                    'article_category?url={0}&cid={1}', $category->url, $category->pk()
                )
            );
        }

        return $categories;
    }


}