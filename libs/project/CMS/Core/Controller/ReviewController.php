<?php

namespace CMS\Core\Controller;

use CMS\Core\Entity\Review;
use CMS\Mail\Model\Notification\NotifySystem;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

class ReviewController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var NotifySystem
     * @service notify.system
     * @inject
     */
    public $notify;

    /** @var int */
    protected $perPage;

    /** @var array */
    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('cms.review');

        if (!$this->isViewPartial) {
            $this->perPage = $this->config['page'];
            if ($this->config['layout'])
                $this->layout($this->config['layout']);
        }
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $review = new Review();
            $review->values($post);
            $review->status = 0;
            $review->save();
            $result['ok'] = _t('CMS:Core', 'Your review added');

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @param $page
     * @throws \Delorius\Exception\Error
     */
    public function listAction($page)
    {
        $reviews = Review::model()->active()->sort();
        $pagination = PaginationBuilder::factory($reviews)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery());

        if ($this->config['first']['name']) {
            $this->breadCrumbs->setLastItem(
                $this->config['first']['name']
            );
        }

        $this->getHeader()->setPagination($pagination);

        $arrNews = $pagination->result();
        $ids = array();
        $var['reviews'] = array();
        foreach ($arrNews as $item) {
            $var['reviews'][] = $item;
            $ids[] = $item->pk();
        }

        $this->setMeta(null, array(
            'title' => $this->config['first']['name']
        ));

        $var['pagination'] = $pagination;
        $this->response($this->view->load('cms/review/list', $var));

    }

    /**
     * @Model(field=id,name=CMS\Core\Entity\Review)
     */
    public function showAction(Review $model)
    {
        load_or_404($model);
        $this->lastModified($model->date_answer ? $model->date_answer : $model->date_cr);

        if ($this->config['first']['name'] && count($this->config['first']['router'])) {
            $this->breadCrumbs->addLink(
                $this->config['first']['name'],
                link_to_array($this->config['first']['router']),
                $this->config['first']['name'],
                false
            );
        }

        $this->breadCrumbs->setLastItem('Отзыв №' . $model->pk());
        $this->setMeta(null, array(
            'title' => 'Отзыв №' . $model->pk(),
            'desc' => $model->text
        ));
        $var['review'] = $model;
        $this->response($this->view->load('cms/review/show', $var));
    }

    /**
     * @param null $theme
     * @param int $limit
     * @throws \Delorius\Exception\Error
     */
    public function listPartial($theme = null, $limit = 3)
    {
        $reviews = Review::model()->active()->sort()->limit($limit)->find_all();
        $var['reviews'] = array();
        foreach ($reviews as $item) {
            $var['reviews'][] = $item;
        }

        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/review/_list' . $theme, $var));
    }

}