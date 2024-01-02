<?php
namespace CMS\Admin\Controller;

use CMS\Admin\Entity\Admin;
use CMS\Core\Component\Register;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Администраторы #admin_root?action=list
 *
 */
class RootController extends Controller
{
    /**
     * @var \CMS\Core\Component\Register
     * @service register
     * @inject
     */
    public $register;

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $var['users'] = Admin::model()->find_all();
        $this->response($this->view->load('cms/root/list', $var));
    }

    /**
     * @AddTitle Добавить
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/root/edit'));
    }

    /**
     * @AddTitle Редактировать
     */
    public function editAction()
    {
        $id = (int)$this->httpRequest->getQuery('id');
        $admin = new Admin($id);
        if (!$admin->loaded())
            throw new NotFound('Пользователь не найден');
        $var['root'] = $admin->as_array();
        $this->response($this->view->load('cms/root/edit', $var));
    }

    public function deleteAction()
    {
        $id = (int)$this->httpRequest->getQuery('id');
        $admin = new Admin($id);
        if (!$admin->loaded())
            throw new NotFound('Пользователь не найден');
        $register = $this->register;
        $admin->onAfterDelete[] = function ($orm) use ($register) {
            $register->add(
                Register::TYPE_ATTENTION,
                Register::SPACE_ADMIN,
                'Администратор удален: id=[id]',
                $orm
            );
        };
        $admin->delete(true);
        $this->httpResponse->redirect(link_to('admin_root', array('action' => 'list')));
    }


    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        $admin = new Admin((int)$post['admin_id']);
        try {
            if (!$admin->loaded()) {
                $admin->role = 'user';
                $admin->active = 1;
            }
            $new_pass = false;
            if (!empty($post['newPassword']) && $post['newPassword'] == $post['newPasswordVerify']) {
                $admin->password = $post['newPassword'];
                $new_pass = true;
            }
            $admin->values($post, array('login'));

            $register = $this->register;
            $admin->onAfterSave[] = function ($orm) use ($register, $new_pass) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    _sf('Администратор изменён {0}: [id]', $new_pass ? ', установлен новый пароль' : ''),
                    $orm
                );
            };
            $admin->save(true);
            $result['ok'] = 'Готово';
            $result['root'] = $admin->as_array();

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


}