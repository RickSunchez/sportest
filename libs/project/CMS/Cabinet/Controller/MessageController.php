<?php
namespace CMS\Cabinet\Controller;

use CMS\Core\Helper\Jevix\JevixEasy;
use CMS\HelpDesk\Entity\Task;
use CMS\HelpDesk\Entity\TaskMessage;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @User
 */
class MessageController extends BaseController
{

    public function before()
    {
        parent::before();
        $this->breadCrumbs->addLink('Техподдержка', 'cabinet_help_desk_list');
    }

    /**
     * @param $page
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $task = Task::model()->sort()->currentUser();
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($task)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('cabinet_help_desk_list');


        $var['pagination'] = $pagination;
        $items = $pagination->result();
        foreach( $items as $item )
        {
            $var['task'][] = $item->as_array();
        }

        $this->response($this->view->load('cms/task/list', $var));
    }

    /**
     * @Model(name=CMS\HelpDesk\Entity\Task)
     */
    public function showAction(Task $model, $page)
    {
        if ( !$model->isCurrentUser() ) {
            throw new ForbiddenAccess('Чужая задача');
        }

        $this->breadCrumbs->setLastItem('#'.$model->pk());
        $messages = TaskMessage::model()->where('task_id', '=', $model->pk())->sort();
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($messages)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->addQueries(array('id' => $model->pk()))
            ->setRoute('cabinet_help_desk_show');

        $var['pagination'] = $pagination;
        $items = $pagination->result();
        foreach( $items as $item )
        {
            $var['messages'][] = $item->as_array();
        }
        $var['task'] = $model;
        $model->set('read_user', 1)->save();
        $this->response($this->view->load('cms/task/message', $var));
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array();

        try {
            $post['text'] = JevixEasy::Parser($post['text']);
            $task = new Task();
            $task->values($post, array('subject', 'text'));
            $task->status = Task::STATUS_CREATE;
            $task->type_id = Task::TYPE_BID;
            $task->read_admin = 1;
            $task->read_user = 0;
            $task->count_msg = 0;
            $task->save(true);
            $result['ok'] = _t('CMS:HelpDesk', 'Task is created');

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function addMassageDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array();
        $task = new Task($post['task_id']);
        $post['form']['text'] = JevixEasy::Parser($post['form']['text']);
        $res = $task->addMessage($post['form']);
        if ($res) {
            $result['ok'] = _t('CMS:HelpDesk', 'Comment added');
        } else {
            $result['error'] = _t('CMS:HelpDesk', 'Failed to add a comment');
        }

        $this->response($result);
    }

}