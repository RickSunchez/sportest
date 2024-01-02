<?php

namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\News;
use CMS\Core\Entity\Tags;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Новости #admin_news
 */
class NewsController extends Controller
{

    /** @AddTitle Список */
    public function listAction($page)
    {
        $news = News::model()->sort();
        $var['get'] = $get = $this->httpRequest->getQuery();

        if (Helpers::getDomains() && $get['domain']) {
            $news->where('site', '=', $get['domain']);
        } else {
            $news->where('site', '=', 'www');
        }

        $pagination = PaginationBuilder::factory($news)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_news');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['news'] = array();
        foreach ($result as $item) {
            $var['news'][] = $item->as_array();
        }
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load('cms/news/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить новость
     */
    public function addAction($cid)
    {
        $var['cid'] = $cid;
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load('cms/news/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать новость
     * @Model(name=CMS\Core\Entity\News)
     */
    public function editAction(News $model)
    {
        $var = array();
        $var['news'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['tags'] = Arrays::resultAsArray($model->getTags());
        $var['domain'] = Helpers::getDomains();
        $var['multi'] = Helpers::isMultiDomain();
        $this->response($this->view->load('cms/news/edit', $var));
    }


    public function tagsDataAction($term)
    {
        $tags = Tags::model()
            ->sort()
            ->whereByTargetType(News::model())
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        $result = Arrays::each($tags, function ($value) {
            return $value->name;
        });
        if (!count($result)) {
            die('{}');
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $news = new News($post['news'][News::model()->primary_key()]);
            $news->values($post['news']);
            $register = $this->container->getService('register');
            $news->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Новость изменена: id=[id]',
                    $orm
                );
            };
            $news->save(true);

            if (count($post['meta'])) {
                $meta = $news->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            if (count($post['tags'])) {
                foreach ($post['tags'] as $tag) {
                    $news->setTag($tag);
                }
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $news->pk()
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
        $news = new News($post['id']);
        try {
            if (!$news->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->container->getService('register');
            $news->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Новость удалена: [name]',
                    $orm
                );
            };
            $news->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function mainDataAction()
    {
        $post = $this->httpRequest->getPost();
        $news = new News($post['id']);
        if ($news->loaded()) {
            $news->main = (int)$post['main'];
            $news->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $news = new News($post['id']);
        if ($news->loaded()) {
            $news->status = (int)$post['status'];
            $news->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


}