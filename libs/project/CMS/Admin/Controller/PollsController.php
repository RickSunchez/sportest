<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\ItemPoll;
use CMS\Core\Entity\Poll;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 */
class PollsController extends Controller
{

    /**
     * @SetTitle Опросы #admin_poll?action=list
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $get = $this->httpRequest->getQuery();
        $polls = Poll::model()->sort();
        $pagination = PaginationBuilder::factory($polls)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_poll');

        $var['polls'] = Arrays::resultAsArray($pagination->result());
        $var['pagination'] = $pagination;
        $var['get'] = $this->httpRequest->getQuery();
        $this->response($this->view->load('cms/poll/list', $var));
    }

    /**
     * @SetTitle Опросы #admin_poll?action=list
     * @AddTitle Добавить опрос
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/poll/edit'));
    }

    /**
     * @SetTitle Опросы #admin_poll?action=list
     * @AddTitle Редактировать опрос
     * @Model(name=CMS\Core\Entity\Poll)
     */
    public function editAction(Poll $model)
    {
        $var['poll'] = $model->as_array();
        $var['items'] = Arrays::resultAsArray($model->getItems());
        $this->response($this->view->load('cms/poll/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $poll = new Poll($post['poll'][Poll::model()->primary_key()]);
            $poll->values($post['poll']);
            $register = $this->container->getService('register');
            $poll->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Голосование изменено: id=[poll_id]',
                    $orm
                );
            };
            $poll->save(true);

            foreach ($post['items'] as $item) {
                $poll->addItem($item);
            }

            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['poll'] = $poll->as_array();
            $result['items'] = Arrays::resultAsArray($poll->getItems());

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteItemDataAction()
    {
        $id = $this->httpRequest->getPost('id');
        $value = new ItemPoll($id);
        if ($value->loaded()) {
            $value->delete(true);
        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $poll = new Poll($post['id']);
        $register = $this->container->getService('register');
        $poll->onAfterSave[] = function ($orm) use ($register) {
            $register->add(
                Register::TYPE_INFO,
                Register::SPACE_ADMIN,
                $orm->status?'Опрос активирован':'Опрос деактивирова',
                $orm
            );
        };
        if ($poll->loaded()) {
            if ($post['status']) {
                DB::update($poll->table_name())
                    ->set(array('status' => 0))
                    ->execute($poll->db_config());
                $poll->status = 1;
                $poll->save();
            } else {
                $poll->status = 0;
                $poll->save();
            }
        }

        $polls = Poll::model()->sort();
        $pagination = PaginationBuilder::factory($polls)
            ->setItemCount(false)
            ->setPage($post['get']['page'])
            ->setItemsPerPage(20)
            ->addQueries($post['get'])
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_poll');
        $result['polls'] = Arrays::resultAsArray($pagination->result());
        $result['ok'] = _t('CMS:Admin', 'These modified');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $id = $this->httpRequest->getPost('id');
        $poll = new Poll($id);
        if ($poll->loaded()) {
            $register = $this->container->getService('register');
            $poll->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Голосование удалено: [text]',
                    $orm
                );
            };
            $poll->delete(true);
        }
        $this->response(array('ok'));
    }

}