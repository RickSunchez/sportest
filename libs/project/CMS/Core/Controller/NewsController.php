<?php
namespace CMS\Core\Controller;

use CMS\Catalog\Entity\Category;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\News;
use CMS\Core\Entity\Tags;
use CMS\Core\Entity\TagsObject;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\NotFound;
use Delorius\Http\Response;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\Calendar;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class NewsController extends Controller
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
        $this->config = $this->container->getParameters('cms.news');
        $this->perPage = $this->config['page'];

        if(!$this->isViewPartial){
            if ($this->config['layout'])
                $this->layout($this->config['layout']);
        }
    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\News)
     */
    public function indexAction(News $model, $url)
    {
        load_or_404($model);
        $this->setSite('newsId', $model->pk());
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
                $this->setBreadCrumbsParents($category, true);
                $this->setSite('newsCategoryId', $category->pk());
                $var['category'] = $category;
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

        $var['news'] = $model;
        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load('cms/news/show' . $theme, $var));
    }

    /**
     * @Model(name=CMS\Catalog\Entity\Category,field=cid,loaded=false)
     */
    public function listAction(Category $model, $page, $date = null, $tag = null)
    {
        $news = News::model()
            ->active()
            ->sort();

        if (Helpers::isMultiDomain()) {
            $news->where('site', '=', Helpers::getCurrentDomain());
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
                $res = $model->getChildren();
                foreach ($res as $cat) {
                    $ids[] = $cat['cid'];
                }
                $ids[] = $model->pk();
                $news->where('cid', 'IN', $ids);
                $categories = $this->setBreadCrumbsParents($model, true);
                $var['categories'] = $categories;
                $var['category'] = $model;
                $this->setSite('newsCategoryId', $model->pk());
            }

            if ($tag) {
                $orm = Tags::model()
                    ->whereByTargetType($news)
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
                    $news->whereTagName($tag);

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

        if ($date && ($date = strtotime($date))) {
            $d = getdate($date);
            $day = $d["mday"];
            $month = $d["mon"];
            $year = $d["year"];

            $begin_date = mktime(0, 0, 0, $month, $day, $year);
            $end_date = mktime(0, 0, 0, $month, ++$day, $year);
            $news->where('date_cr', 'BETWEEN', DB::expr(_sf('{0}  AND {1}', $begin_date, $end_date)));
        }

        $pagination = PaginationBuilder::factory($news)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrNews = $pagination->result();
        $ids = array();
        $var['news'] = array();
        foreach ($arrNews as $item) {
            $var['news'][] = $item;
            $ids[] = $item->pk();
        }
        $var['images'] = $this->getImages($ids);
        $var['pagination'] = $pagination;

        $meta = $model->loaded() ? $model->getMeta() : null;
        $this->setMeta($meta, array(
            'title' => $model->loaded() ? $model->name : $this->config['first']['name'],
            'property' => array(
                'og:image' => $model->loaded() ? $model->getImage()->preview : '',
                'og:title' => $model->loaded() ? $model->name : $this->config['first']['name'],
                'og:description' => $model->loaded() ? $model->description : ''
            )
        ));


        if ($date) {
            $this->getHeader()->AddTitle(_sf('{0}/{1}/{2}', $day, $month, $year));
        }
        $theme = $prefix ? '_' . $prefix : '';
        $this->response($this->view->load('cms/news/list' . $theme, $var));
    }

    /**
     * @param int $limit
     */
    public function listPartial($limit = 3, $theme = null)
    {
        $news = News::model()
            ->active()
            ->sort()
            ->cached()
            ->limit($limit);

        if (Helpers::isMultiDomain()) {
            $news->where('site', '=', Helpers::getCurrentDomain());
        }
        $result = $news->find_all();
        $ids = array();
        foreach ($result as $item) {
            $var['news'][] = $item;
            $ids[] = $item->pk();
        }

        $var['images'] = $this->getImages($ids);
        $theme = $theme ? '_' . $theme : null;
        $this->response($this->view->load('cms/news/_list' . $theme, $var));
    }


    /**
     * @Post
     */
    public function calendarDataAction()
    {
        $post = $this->httpRequest->getPost();
        if (isset($post['date'])) {
            $date_str = _sf('01-{0}', $post['date']);
            $date = strtotime($date_str);
        }
        if (!$date) {
            $date = time();
        }
        $d = getdate($date);
        $month = $d["mon"];
        $year = $d["year"];
        $calendar = $this->getCalendar($date, $post['prefix']);
        $this->response(array(
            'html' => $calendar->genMonth(null, $month, $year)->render()
        ));
    }


    /**
     * @param null $prefix
     */
    public function calendarPartial($prefix = null)
    {
        $get = $this->httpRequest->getQuery();
        if (isset($get['date'])) {
            $date = strtotime($get['date']);
        }
        if (!$date) {
            $date = time();
        }
        $d = getdate($date);
        $month = $d["mon"];
        $day = $d["mday"];
        $year = $d["year"];
        $calendar = $this->getCalendar($date, $prefix);
        $container = Html::el('div', array(
            'id' => ($prefix ? $prefix . '-' : '') . 'df-calendar'
        ));
        $container->setHtml($calendar->genMonth($day, $month, $year));
        $this->response($container);
    }


    public function tagsPartial($limit = null)
    {
        $tag = Tags::model()->sort();
        if ($limit) {
            $tag->limit($limit);
        }
        $tagObject = new TagsObject();
        $tag = $tag->join($tagObject->table_name(), 'inner')
            ->on($tag->table_name() . '.tag_id', '=', $tagObject->table_name() . '.tag_id')
            ->where($tagObject->table_name() . '.target_type', '=', News::model()->table_name())
            ->group_by($tag->table_name() . '.tag_id');

        if ($cid = $this->getSite('newsCategoryId')) {
            $tag->where($tagObject->table_name() . '.option', '=', $cid);
        }

        $var['tags'] = $tag->find_all();

        $this->response($this->view->load('cms/news/_tags', $var));
    }

    /**
     * @param $date
     * @return Calendar|null
     */
    protected function getCalendar($date, $prefix = null)
    {
        $d = getdate($date);
        $month = $d["mon"];
        $year = $d["year"];

        $next_month = $month + 1;
        $next_year = $year;
        if ($next_month == 13) {
            $next_month = 1;
            $next_year += 1;
        }

        $begin_date = mktime(0, 0, 0, $month, 1, $year);
        $end_date = mktime(0, 0, 0, $next_month, 1, $next_year);

        $calendar = new Calendar();
        $calendar->setOptions(CLD_NAVIGATION);
        if ($prefix)
            $calendar->setCssPrefix($prefix);

        $news = News::model()
            ->select('date_cr')
            ->active()
            ->where('date_cr', 'BETWEEN', DB::expr(_sf('{0}  AND {1}', $begin_date, $end_date)))
            ->find_all();

        $arrTitle = array();
        foreach ($news as $item) {
            $arrTitle[date('d', $item['date_cr'])] += 1;
            $calendar->addUDateLink($item['date_cr'], link_to('news', array('date' => date('d-m-Y', $item['date_cr']))));
        }

        if (count($arrTitle)) {
            foreach ($arrTitle as $day => $count) {
                $form1 = 'новость';
                $form2 = 'новости';
                $form5 = 'новостей';
                $calendar->addDateTitle($day, $month, $year, _sf('{0} {1}', $count, Strings::pluralForm($count, $form1, $form2, $form5)));
            }
        }
        return $calendar;
    }

    protected function getImages($targetId = null)
    {
        $images = Image::model()->whereByTargetType(News::model())->cached();
        if ($targetId) {
            $images->whereByTargetId($targetId);
        }
        $image = Arrays::resultAsArrayKey($images->find_all(), 'target_id');
        return $image;
    }


    public function categoriesPartial($theme = null)
    {
        $categories = Category::model()
            ->type(Category::TYPE_NEWS)
            ->sort()
            ->active()
            ->select(array('cid', 'id'), 'url', 'pid', 'name', 'object', 'children')
            ->cached()
            ->find_all();

        $var['categories'] = array();
        foreach ($categories as $cat) {
            $cat['link'] = link_to('news_category', array('cid' => $cat['id'], 'url' => $cat['url']));
            $var['categories'][$cat['pid']][] = $cat;
        }
        $var['selfCategoryId'] = $this->getSite('newsCategoryId');
        $var['menu_id'] = 0;

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/news/_categories' . $theme, $var));
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
                        'news_category?url={0}&cid={1}', $cat['url'], $cat['cid']
                    )
                );
            }
        }

        if ($self) {
            $categories .= _sf(' {0} / ', $category->name);
            $this->breadCrumbs->addLink(
                $category->name,
                _sf(
                    'news_category?url={0}&cid={1}', $category->url, $category->pk()
                )
            );
        }

        return $categories;
    }


}