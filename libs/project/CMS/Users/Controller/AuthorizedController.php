<?php
namespace CMS\Users\Controller;

use CMS\Users\Entity\User;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Security\AuthenticationException;
use Delorius\Utils\Strings;
use Delorius\Utils\Validators;

/**
 * @User(isLoggedIn=false)
 */
class AuthorizedController extends Controller
{
    /**
     * @service notify
     * @inject
     */
    public $notify;

    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('cms.user');
        if ($this->config['template']['forms'])
            $this->template($this->config['template']['forms']);
        if ($this->config['layout']['forms'])
            $this->layout($this->config['layout']['forms']);
    }

    /**
     * @SetTitle Авторизация
     */
    public function authAction()
    {
        if ($this->user->isLoggedIn()) {
            $this->httpResponse->redirect(link_to('cabinet'));
            exit();
        }
        $this->response($this->view->load('cms/authorized/login'));
    }

    /**
     * @Post
     */
    public function forgotDataAction()
    {
        $email = $this->httpRequest->getPost('email',null);

        try{
            if(!Validators::isEmail($email)){
                throw new Error(_t('CMS:Users','Email Add a valid'));
            }

            $user = User::model()->where('email','=',$email)->find();
            if(!$user->loaded())
                throw new Error(_t('CMS:Users','This email does not exist'));

            $user->hash = Strings::random(40);
            $user->save();

            $var['user'] = $user;

            $this->notify->setAddressee($user->email,$user->login);
            $this->notify->send(
                _t('CMS:Users','Recover password'),
                $this->view->load('cms/mail/forgot',$var)
            );
            $result['ok'] = _t('CMS:Users','On your mail sent the information to restore password');
        }catch (Error $e){
            $result['error'] = $e->getMessage();
        }

        $this->response($result);

    }

    /**
     * @SetTitle Восстановления пароля
     * @Model(name=CMS\Users\Entity\User)
     */
    public function forgotAction(User $model,$hash)
    {
        if($model->hash != $hash)
        {
            throw new NotFound(_t('CMS:Users','Wrong hash'));
        }

        $post = $this->httpRequest->getPost();
        if(sizeof($post)){
            try{

                if(Strings::length($post['password1']) == 0){
                    throw new Error(_t('CMS:Users','Enter your new password'));
                }

                if($post['password1'] != $post['password2']){
                    throw new Error(_t('CMS:Users','Passwords do not match'));
                }

                $model->password = $post['password1'];
                $model->hash = '';
                $model->save();

                $this->setFlash('ok',_t('CMS:Users','The password is changed'));
                $this->httpResponse->redirect(link_to('cabinet'));
                exit;
            }catch (Error $e){
                $var['error']= $e->getMessage();
            }
        }
        $var['user'] = $model;
        $var['post'] = $post;
        $this->response($this->view->load('cms/users/forgot',$var));
    }

    /**
     * @SetTitle Восстановления пароля
     */
    public function remindAction()
    {
        $this->response($this->view->load('cms/authorized/remind'));
    }

    /**
     * @Post
     */
    public function authDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('error' => '', 'ok' => '');
        try {
            $this->user->login($post['email'], $post['password']);
            $result['ok'] = _t('CMS:Users','Welcome');
            if($post['is_data']){
                $result['user'] = $this->user->getIdentity()->getData();
            }
        } catch (AuthenticationException $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);
    }

    /**
     * @SetTitle Регистрация
     */
    public function regAction()
    {
        if ($this->user->isLoggedIn()) {
            $this->httpResponse->redirect(link_to('cabinet'));
            exit();
        }
        $this->response($this->view->load('cms/authorized/reg'));
    }

    /**
     * @Post
     */
    public function regDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        try {
            if ($post['password1'] != $post['password2']) {
                throw new Error(_t('CMS:Users', 'Passwords do not match'));
            }
            $post['password'] = $post['password1'];
            $user = new User();
            $user->values($post);
            $user->save(true);
            $result['ok'] = 'Поздравляем!';


            $this->notify->setAddressee($user->email,$user->login);
            $var['user'] = $user;
            $var['password'] = $post['password1'];
            $this->notify->send(
                _t('CMS:Users','Register'),
                $this->view->load('cms/mail/reg',$var)
            );
            $this->user->login($post['email'], $post['password']);
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        } catch (Error $e) {
            $result['errors'][] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @User
     */
    public function logoutAction()
    {
        $this->user->logout(true);
        $this->httpResponse->redirect(link_to('homepage'));
        exit;
    }

}