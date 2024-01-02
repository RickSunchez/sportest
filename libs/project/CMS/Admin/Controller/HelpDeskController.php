<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\HelpDesk\Entity\Task;
use CMS\HelpDesk\Entity\TaskMessage;
use CMS\Users\Entity\User;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Delorius\Utils\Valid;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Техподдержка #admin_help_im?action=list
 */
class HelpDeskController extends Controller
{

    /**
     * @AddTitle Список вопросов
     */
    public function listAction($page)
    {


        $task = Task::model()->sort();
        $get = $this->httpRequest->getQuery();

        if (isset($get['user_id'])) {
            $task->where('user_id', '=', $get['user_id']);
        }

        if (isset($get['email'])) {
            $users = User::model()->where('email', 'like', '%' . $get['email'] . '%')->find_all();
            if (count($users)) {
                $ids = array();
                foreach ($users as $user) {
                    $ids[] = $user->pk();
                }
                $task->where('user_id', 'in', $ids);
            } else {
                $task->where('user_id', '=', 0);
            }
        }

        $pagination = PaginationBuilder::factory($task)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_help_im');

        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $result = $pagination->result();
        $ids = $var['tasks'] = array();
        foreach ($result as $item) {
            $var['tasks'][] = $item->as_array();
            $ids[$item->userId()] = $item->userId();;
        }

        if (sizeof($ids)) {
            $var['users'] = Arrays::resultAsArrayKey(User::model()->cached('+5 minutes')->where('user_id', 'in', $ids)->find_all(), 'user_id', true);
        }

        $this->response($this->view->load('cms/help/im/list', $var));
    }

    /**
     * @AddTitle Добавить
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function addAction()
    {
        $var['task'] = new Task();
        $var['type'] = Arrays::dataKeyValue(Task::getType());
        $var['status'] = Arrays::dataKeyValue(Task::getStatus());
        $this->response($this->view->load('cms/help/im/add', $var));
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $task = new Task($post['id']);
        try {
            if (!$task->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));
            $register = $this->container->getService('register');
            $task->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'В тех.поддержке удалена задача: id=[task_id]',
                    $orm
                );
            };
            $task->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function addTaskDataAction()
    {
        $post = $this->httpRequest->getPost('task');
        $user = User::model()->where('email', '=', $post['email'])->find();
        if ($user->loaded()) {
            try {
                $task = new Task();
                $task->values($post);
                $task->is_admin = 1;
                $task->user_id = $user->pk();

                $register = $this->container->getService('register');
                $task->onAfterSave[] = function ($orm) use ($register) {
                    $register->add(
                        Register::TYPE_INFO,
                        Register::SPACE_ADMIN,
                        'В тех.поддержке создана задача: id=[task_id]',
                        $orm
                    );
                };

                $task->save(true);
                $result['ok'] = _t('CMS:Admin', 'Ready');
                $result['task_id'] = $task->task_id;
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = 'Нет такого пользователя';
        }
        $this->response($result);
    }

    /**
     * @AddTitle Ответить
     * @Model(name=CMS\HelpDesk\Entity\Task)
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function editAction(Task $model)
    {
        $messages = TaskMessage::model()->where('task_id', '=', $model->pk())->sort()->find_all();
        $model->read_admin = 1;
        $model->read_user = 0;
        $model->save(true);
        $var['messages'] = Arrays::resultAsArray($messages);
        $var['task'] = $model->as_array();
        $var['type'] = Arrays::dataKeyValue(Task::getType());
        $var['status'] = Arrays::dataKeyValue(Task::getStatus());
        $this->response($this->view->load('cms/help/im/answer', $var));
    }

    /**
     * @Post
     */
    public function addMassageDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $task = new Task($post['task_id']);
            if (!$task->loaded()) {
                throw new Error('Нет такой задачи');
            }
            $res = $task->addMessage($post['form'], 1);
            if (!$res) {
                throw new Error('Не удалось добавить сообщение');
            }

            $this->container->getService('register')->add(
                Register::TYPE_INFO,
                Register::SPACE_ADMIN,
                'В тех.поддержке дали ответ на задачу: id=[task_id]',
                $task
            );

            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);

    }

    /**
     * @Post
     */
    public function userDataAction()
    {
        $post = $this->httpRequest->getPost();
        $found = User::model()
            ->where('email', 'LIKE', $post['input'] . '%')
            ->limit(10)
            ->find_all()
            ->as_array();
        $result['data'] = array();

        foreach ($found as $user) {
            $result['data'][] = $user->email;
        }

        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\HelpDesk\Entity\Task)
     */
    public function reStatusDataAction(Task $model)
    {
        $status = $this->httpRequest->getPost('status', 1);
        $model->status = $status;
        $model->save(true);
        $result = array(
            'ok' => _t('CMS:Admin', 'These modified'),
            'status' => $status,
            'status_name' => $model->getNameStatus()
        );
        $this->response($result);
    }

}