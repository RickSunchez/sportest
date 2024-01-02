<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use Delorius\Application\UI\Controller;
use Delorius\Security\AuthenticationException;

/**
 * @Template(name=admin,layout=no_auth)
 * @Admin(isLoggedIn=false)
 * @SetTitle Панель управления
 */
class AuthorizedController extends Controller
{

    /**
     * @AddTitle Вход в систему
     */
    public function loginAction()
    {
        $post = $this->httpRequest->getPost();
        $var = array();
        if (sizeof($post)) {
            try {

                $register = $this->container->getService('register');
                $this->user->onLoggedIn[] = function ($sender) use ($register) {
                    $register->add(
                        Register::TYPE_INFO,
                        Register::SPACE_ADMIN,
                        'Пользователь авторизовался'
                    );
                };
                $this->user->login($post['login'], $post['password']);

                // редирект на текущую страницу
                $this->httpResponse->redirect($this->httpRequest->getUrl());
            } catch (AuthenticationException $e) {
                $var['error'] = $e->getMessage();
            }
        }
        $this->response($this->view->load('cms/authorized/login', $var));
    }

    /**
     * @Admin
     */
    public function logoutAction()
    {
        $register = $this->container->getService('register');
        $this->user->onLoggedOut[] = function ($sender) use ($register) {
            $register->add(
                Register::TYPE_INFO,
                Register::SPACE_ADMIN,
                'Пользователь вышел'
            );
        };
        $this->user->logout(true);
        $this->httpResponse->redirect(link_to('admin'));
    }

    public function loginDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $this->user->login($post['login'], $post['password']);
            // редирект на текущую страницу
            $var['ok'] = _t('CMS:Admin', 'Ready');
        } catch (AuthenticationException $e) {
            $var['error'] = $e->getMessage();
        }
        $this->response($var);
    }

}