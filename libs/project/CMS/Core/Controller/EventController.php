<?php
namespace CMS\Core\Controller;

use CMS\Catalog\Entity\Category;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Event;
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

class EventController extends Controller
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
        $this->config = $this->container->getParameters('cms.event');
        $this->perPage = $this->config['page'];
        if ($this->config['layout'])
            $this->layout($this->config['layout']);
    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\Event)
     */
    public function indexAction(Event $model, $url)
    {
        load_or_404($model);

        $this->setSite('eventId', $model->pk());

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
                $this->setSite('eventCategoryId', $category->pk());
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

        $var['event'] = $model;
        $theme = $model->prefix ? '_' . $model->prefix : '';
        $this->response($this->view->load('cms/event/show' . $theme, $var));
    }

    /**
     * @Model(name=CMS\Catalog\Entity\Category,field=cid,loaded=false)
     */
    public function listAction(Category $model, $page, $date = null)
    {
        $events = Event::model()
            ->active()
            ->not_final()
            ->sort();

        if (Helpers::isMultiDomain()) {
            $events->where('site', '=', Helpers::getCurrentDomain());
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
            $events->where('cid', 'IN', $ids);
            $categories = $this->setBreadCrumbsParents($model, true);
            $var['categories'] = $categories;
            $var['category'] = $model;
            $this->setSite('eventCategoryId', $model->pk());


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
            $events->where('date_end', 'BETWEEN', DB::expr(_sf('{0}  AND {1}', $begin_date, $end_date)));
        }

        $pagination = PaginationBuilder::factory($events)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        $this->getHeader()->setPagination($pagination);

        $arrEvents = $pagination->result();
        $ids = array();
        $var['events'] = array();
        foreach ($arrEvents as $item) {
            $var['events'][] = $item;
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

        if ($date) {
            $this->getHeader()->AddTitle(_sf('{0}/{1}/{2}', $day, $month, $year));
        }

        $theme = $prefix ? '_' . $prefix : '';
        $this->response($this->view->load('cms/event/list' . $theme, $var));
    }

    /**
     * @param int $limit
     */
    public function listPartial($limit = 3)
    {
        $events = Event::model()
            ->active()
            ->sort()
            ->not_final()
            ->limit($limit);

        if (Helpers::isMultiDomain()) {
            $events->where('site', '=', Helpers::getCurrentDomain());
        }
        $result = $events->find_all();
        $ids = array();
        foreach ($result as $item) {
            $var['events'][] = $item;
            $ids[] = $item->pk();
        }

        $var['images'] = $this->getImages($ids);
        $this->response($this->view->load('cms/event/_list', $var));
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
        $calendar = $this->getCalendar($date);
        $container = Html::el('div', array(
            'id' => ($prefix ? $prefix . '-' : '') . 'df-calendar'
        ));
        $container->setHtml($calendar->genMonth($day, $month, $year));
        $this->response($container);
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

        $event = Event::model()
            ->active()
            ->where('date_end', 'BETWEEN', DB::expr(_sf('{0}  AND {1}', $begin_date, $end_date)))
            ->find_all();


        $arrTitle = array();
        foreach ($event as $item) {
            $arrTitle[date('d', $item->date_end)] += 1;
            $calendar->addUDateLink($item->date_end, link_to('events', array('date' => date('d-m-Y', $item->date_end))));
        }

        if (count($arrTitle)) {
            foreach ($arrTitle as $day => $count) {
                $form1 = 'событие';
                $form2 = 'события';
                $form5 = 'событий';
                $calendar->addDateTitle($day, $month, $year, _sf('{0} {1}', $count, Strings::pluralForm($count, $form1, $form2, $form5)));
            }
        }
        return $calendar;
    }

    protected function getImages($targetId = null)
    {
        $images = Image::model()->whereByTargetType(Event::model())->cached();
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
                        'event_category?url={0}&cid={1}', $cat['url'], $cat['cid']
                    )
                );
            }
        }

        if ($self) {
            $categories .= _sf(' {0} / ', $category->name);
            $this->breadCrumbs->addLink(
                $category->name,
                _sf(
                    'event_category?url={0}&cid={1}', $category->url, $category->pk()
                )
            );
        }

        return $categories;
    }


}