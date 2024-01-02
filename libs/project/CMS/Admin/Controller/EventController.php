<?php

namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Event;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;


/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle События #admin_event
 */
class EventController extends Controller
{

    /** @AddTitle Список */
    public function listAction($page)
    {
        $events = Event::model()->sort();
        $var['get'] = $get = $this->httpRequest->getQuery();

        if (Helpers::getDomains() && $get['domain']) {
            $events->where('site', '=', $get['domain']);
        } else {
            $events->where('site', '=', 'www');
        }

        $pagination = PaginationBuilder::factory($events)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_event');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['events'] = array();
        foreach ($result as $item) {
            $var['events'][] = $item->as_array();
        }
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load('cms/event/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить событие
     */
    public function addAction($cid)
    {
        $var['cid'] = $cid;
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load('cms/event/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать событие
     * @Model(name=CMS\Core\Entity\Event)
     */
    public function editAction(Event $model)
    {
        $var = array();
        $var['event'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['domain'] = Helpers::getDomains();
        $var['multi'] = Helpers::isMultiDomain();
        $this->response($this->view->load('cms/event/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $event = new Event($post['event'][Event::model()->primary_key()]);
            $event->values($post['event']);
            $register = $this->container->getService('register');
            $event->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Событие изменено: id=[id]',
                    $orm
                );
            };
            $event->save(true);

            if (count($post['meta'])) {
                $meta = $event->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $event->pk()
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
        $event = new Event($post['id']);
        try {
            if (!$event->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->container->getService('register');
            $event->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Событие удалено: [name]',
                    $orm
                );
            };
            $event->delete(true);
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
        $event = new Event($post['id']);
        if ($event->loaded()) {
            $event->main = (int)$post['main'];
            $event->save(true);
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
        $event = new Event($post['id']);
        if ($event->loaded()) {
            $event->status = (int)$post['status'];
            $event->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


}